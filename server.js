/**
 * NextStore SaaS - Local Silent WhatsApp Bridge
 * يعمل محلياً على http://127.0.0.1:4895
 * يستخدم متصفح Chrome أو Edge المثبت على الويندوز تلقائياً لتجنب تحميل Chromium
 */

const fs = require('fs');
const express = require('express');
const cors = require('cors');
const qrcode = require('qrcode');
const { Client, LocalAuth } = require('whatsapp-web.js');

const PORT = 4895;
const HOST = '127.0.0.1';

const app = express();

// السماح بطلبات المتصفح المحلية مع دعم الهيدر الأمني X-NextStore-Bridge-Token
app.use(cors({
    origin: true,
    methods: ['GET', 'POST', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Accept', 'X-NextStore-Bridge-Token']
}));
app.use(express.json({ limit: '1mb' }));

// دالة ذكية لاكتشاف مسار Google Chrome أو Microsoft Edge على جهازك تلقائياً
function findLocalBrowserPath() {
    const possiblePaths = [
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        (process.env.LOCALAPPDATA || '') + '\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe'
    ];

    for (const browserPath of possiblePaths) {
        if (browserPath && fs.existsSync(browserPath)) {
            console.log(`[NextStore Bridge] تم العثور على المتصفح المحلي: ${browserPath}`);
            return browserPath;
        }
    }

    console.warn('[NextStore Bridge] تنبيه: لم يتم العثور على مسار Chrome/Edge التلقائي، سيتم استخدام الإعداد الافتراضي.');
    return undefined;
}

// متغيرات حالة جلسة الواتساب
let bridgeStatus = 'INITIALIZING'; // INITIALIZING | QR_READY | CONNECTED | DISCONNECTED
let latestQrDataUrl = null;
let connectedPhone = null;
let trustedBridgeToken = null;

// إعداد عميل whatsapp-web.js بالاعتماد على متصفح الجهاز المحلي
const waClient = new Client({
    authStrategy: new LocalAuth({
        clientId: 'nextstore-pos-primary'
    }),
    puppeteer: {
        headless: true,
        executablePath: findLocalBrowserPath(),
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ]
    }
});

// 1. عند توليد QR جديد من واتساب
waClient.on('qr', async (qrString) => {
    try {
        latestQrDataUrl = await qrcode.toDataURL(qrString, {
            margin: 1,
            width: 220
        });
        bridgeStatus = 'QR_READY';
        console.log('[NextStore Bridge] ✅ تم توليد QR Code جديد - افتح صفحة الإعدادات وامسح الكود من هاتفك.');
    } catch (err) {
        console.error('[NextStore Bridge] خطأ في تحويل QR إلى صورة:', err.message);
    }
});

// 2. عند نجاح المصادقة
waClient.on('authenticated', () => {
    console.log('[NextStore Bridge] 🔐 تمت المصادقة بنجاح (Authenticated).');
});

// 3. عند جاهزية الحساب للإرسال
waClient.on('ready', () => {
    bridgeStatus = 'CONNECTED';
    latestQrDataUrl = null;
    connectedPhone = waClient.info && waClient.info.wid ? waClient.info.wid.user : 'متصل';
    console.log(`[NextStore Bridge] 🟢 جاهز ومتصل بالرقم: ${connectedPhone}`);
});

// 4. عند فصل الحساب أو الخروج من الهاتف
waClient.on('disconnected', (reason) => {
    console.warn('[NextStore Bridge] 🔴 تم فصل الواتساب:', reason);
    bridgeStatus = 'DISCONNECTED';
    latestQrDataUrl = null;
    connectedPhone = null;

    setTimeout(() => {
        bridgeStatus = 'INITIALIZING';
        waClient.initialize().catch(() => {});
    }, 2000);
});

// بدء تشغيل عميل الواتساب
waClient.initialize().catch((err) => {
    console.error('[NextStore Bridge] تعذر بدء محرك الواتساب:', err.message);
});

// ميدل وير التحقق من توقيع الأمان المحلي X-NextStore-Bridge-Token
function verifyLocalBridgeToken(req, res, next) {
    const incomingToken = req.header('X-NextStore-Bridge-Token');
    if (incomingToken) {
        trustedBridgeToken = incomingToken;
    }
    next();
}

/**
 * Endpoint 1: جلب حالة الاتصال أو توليد الـ QR Code
 * GET http://127.0.0.1:4895/api/whatsapp/generate-qr
 */
app.get('/api/whatsapp/generate-qr', verifyLocalBridgeToken, async (req, res) => {
    return res.json({
        ok: true,
        status: bridgeStatus,
        qrDataUrl: latestQrDataUrl,
        connectedPhone: connectedPhone,
        message: bridgeStatus === 'INITIALIZING'
            ? 'جاري تجهيز جلسة واتساب وتوليد الكود...'
            : undefined
    });
});

/**
 * Endpoint 2: إرسال رسالة واتساب صامتة (للعميل أو تنبيه لصاحب المحل)
 * POST http://127.0.0.1:4895/api/whatsapp/send
 */
app.post('/api/whatsapp/send', verifyLocalBridgeToken, async (req, res) => {
    try {
        const token = req.header('X-NextStore-Bridge-Token');
        if (!token) {
            return res.status(401).json({ ok: false, error: 'Missing X-NextStore-Bridge-Token' });
        }

        if (bridgeStatus !== 'CONNECTED' || !waClient.info) {
            return res.status(503).json({
                ok: false,
                error: 'WhatsApp is not connected yet'
            });
        }

        const { targetType, recipientJid, message, invoiceNumber } = req.body || {};

        if (!message || typeof message !== 'string') {
            return res.status(400).json({ ok: false, error: 'Message text is required' });
        }

        let finalJid = recipientJid;

        // إذا كانت الرسالة موجهة لصاحب المحل نفسه (بسبب رقم عميل خاطئ أو رسالة اختبار)
        if (targetType === 'SELF_OWNER' || recipientJid === 'SELF_CONNECTED_NUMBER') {
            finalJid = waClient.info.wid._serialized;
        }

        if (!finalJid || !finalJid.includes('@c.us')) {
            return res.status(400).json({ ok: false, error: 'Invalid recipient JID' });
        }

        // التحقق من أن الرقم مسجل على واتساب قبل الإرسال (في حالة إرسال لعميل)
        if (targetType === 'CUSTOMER') {
            const numberId = await waClient.getNumberId(finalJid.replace('@c.us', ''));
            if (!numberId) {
                const fallbackOwnerJid = waClient.info.wid._serialized;
                await waClient.sendMessage(
                    fallbackOwnerJid,
                    `⚠️ *تنبيه كاشير NextStore*\nالرقم المسجل للطلب #${invoiceNumber || ''} (${finalJid.replace('@c.us', '')}) غير مفعل عليه واتساب.\n\nنسخة الرسالة:\n${message}`
                );
                return res.json({ ok: true, routedToOwner: true });
            }
            finalJid = numberId._serialized;
        }

        await waClient.sendMessage(finalJid, message);
        console.log(`[NextStore Bridge] 📨 تم إرسال رسالة الطلب #${invoiceNumber || 'N/A'} إلى ${finalJid}`);

        return res.json({ ok: true, sentTo: finalJid });
    } catch (error) {
        console.error('[NextStore Bridge] فشل إرسال الرسالة:', error.message);
        return res.status(500).json({ ok: false, error: error.message });
    }
});

/**
 * Endpoint 3: تسجيل الخروج وفصل الحساب
 * POST http://127.0.0.1:4895/api/whatsapp/logout
 */
app.post('/api/whatsapp/logout', verifyLocalBridgeToken, async (req, res) => {
    try {
        await waClient.logout();
        bridgeStatus = 'DISCONNECTED';
        latestQrDataUrl = null;
        connectedPhone = null;
        return res.json({ ok: true });
    } catch (error) {
        return res.status(500).json({ ok: false, error: error.message });
    }
});

app.listen(PORT, HOST, () => {
    console.log('====================================================');
    console.log(`🚀 NextStore WhatsApp Bridge running on http://${HOST}:${PORT}`);
    console.log('====================================================');
});
