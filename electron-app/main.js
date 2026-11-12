const { app, BrowserWindow, ipcMain, shell, dialog, session } = require('electron');
const { spawn, spawnSync, execFile } = require('child_process');
const crypto = require('crypto');
const http = require('http');
const net = require('net');
const fs = require('fs');
const os = require('os');
const path = require('path');
const { pathToFileURL } = require('url');

const isDev = !app.isPackaged;
const LARAVEL = isDev ? path.join(__dirname, '..') : path.join(process.resourcesPath, 'laravel');
const PHP_DIR = isDev ? path.join(__dirname, 'php-bin') : path.join(process.resourcesPath, 'php-bin');
const PHP = fs.existsSync(path.join(PHP_DIR, 'php.exe')) ? path.join(PHP_DIR, 'php.exe') : 'php';
const DATA = app.getPath('userData');
// في التجربة (npm start) يبقى storage الخاص بالمشروع كما هو حتى لا يتأثر سيرفر XAMPP
const STORAGE = isDev ? path.join(LARAVEL, 'storage') : path.join(DATA, 'storage');
const DB = path.join(DATA, 'database.sqlite');
const ROUTER = path.join(LARAVEL, 'vendor', 'laravel', 'framework', 'src', 'Illuminate', 'Foundation', 'resources', 'server.php');
const PREFERRED_PORT = 8787;

let php = null;
let win = null;
let port = PREFERRED_PORT;

// بيانات العميل تعيش في userData فلا تضيع مع التحديث أو إعادة التسطيب
const env = {
    ...process.env,
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: DB,
    ...(isDev ? {} : { NEXTSTORE_STORAGE_PATH: STORAGE }),
    NEXTSTORE_DESKTOP: '1',
    LICENSE_ENFORCE: 'true', // برنامج العميل مقفول بالترخيص دايماً ومش سيرفر مالك، مهما كانت متغيرات الجهاز
    HQ_ENABLED: 'false',
    SESSION_EXPIRE_ON_CLOSE: 'true', // قفل البرنامج = تسجيل خروج
    // php.ini المضمّن يقرأ مساره من هنا (extension_dir و cacert)
    ...(PHP === 'php' ? {} : { PHPRC: PHP_DIR, NEXTSTORE_PHP_DIR: PHP_DIR }),
};

function prepareData() {
    ['app/public', 'app/backups', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs']
        .forEach(dir => fs.mkdirSync(path.join(STORAGE, dir), { recursive: true }));

    fs.rmSync(path.join(STORAGE, 'app', 'heartbeat.at'), { force: true }); // مزامنة الترخيص فور فتح البرنامج
    if (!fs.existsSync(DB)) {
        const template = path.join(LARAVEL, 'database', 'database.sqlite');
        fs.existsSync(template) ? fs.copyFileSync(template, DB) : fs.writeFileSync(DB, '');
    }

    // صور الشعار والمرفوعات: public/storage → userData/storage/app/public (junction لا يحتاج صلاحيات أدمن)
    if (isDev) return;
    const link = path.join(LARAVEL, 'public', 'storage');
    try {
        if (fs.lstatSync(link, { throwIfNoEntry: false })) fs.rmdirSync(link);
        fs.symlinkSync(path.join(STORAGE, 'app', 'public'), link, 'junction');
    } catch (e) {
        console.warn('storage link:', e.message);
    }
}

function artisan(...args) {
    const r = spawnSync(PHP, ['artisan', ...args], { cwd: LARAVEL, env, windowsHide: true, encoding: 'utf8' });
    if (r.status !== 0) throw new Error((r.stderr || r.stdout || r.error?.message || 'artisan failed').slice(0, 2000));
}

function freePort(p) {
    return new Promise(resolve => {
        const srv = net.createServer().once('error', () => resolve(freePort(p + 1)))
            .once('listening', () => srv.close(() => resolve(p))).listen(p, '127.0.0.1');
    });
}

function waitForServer(timeoutMs = 20000) {
    const start = Date.now();
    return new Promise((resolve, reject) => {
        const tryOnce = () => http.get({ host: '127.0.0.1', port, path: '/login', timeout: 1500 }, res => { res.resume(); resolve(); })
            .on('error', () => (Date.now() - start > timeoutMs ? reject(new Error('PHP server did not start')) : setTimeout(tryOnce, 300)));
        tryOnce();
    });
}

function startPhp() {
    // راوتر لارافيل يتوقع أن يعمل السيرفر من داخل public (زي artisan serve)
    php = spawn(PHP, ['-S', `127.0.0.1:${port}`, ROUTER], { cwd: path.join(LARAVEL, 'public'), env, windowsHide: true, stdio: 'ignore' });
    php.on('exit', code => {
        php = null;
        if (!app.isQuitting && win) dialog.showErrorBox('NextStore', `توقف سيرفر النظام (code ${code}). أعد تشغيل البرنامج.`);
    });
}

function stopPhp() {
    if (!php) return;
    spawnSync('taskkill', ['/pid', String(php.pid), '/T', '/F'], { windowsHide: true });
    php = null;
}

let forceClose = false;
async function createWindow() {
    win = new BrowserWindow({
        width: 1366, height: 800, show: false, autoHideMenuBar: true, title: 'NextStore POS', backgroundColor: '#f8fafc',
        webPreferences: { preload: path.join(__dirname, 'preload.js'), contextIsolation: true, nodeIntegration: false },
    });
    win.maximize();
    // زرار X: الصفحة هي اللي تقرر (تقفل نافذة / تخرج من الصفحة / تسأل عن إغلاق البرنامج)
    win.on('close', e => {
        if (forceClose || app.isQuitting) return;
        e.preventDefault();
        win.webContents.executeJavaScript('window.NextStoreNav ? NextStoreNav.requestClose() : false')
            .then(handled => { if (!handled) { forceClose = true; win.close(); } })
            .catch(() => { forceClose = true; win.close(); });
    });
    win.on('closed', () => app.quit());
    win.webContents.on('did-finish-load', () => win.webContents.focus()); // خانة الباركود تاخد الكيبورد من غير ضغطة ماوس // نافذة الطباعة المخفية ما تخليش البرنامج مفتوح
    win.loadURL('data:text/html;charset=utf-8,' + encodeURIComponent(
        '<body style="margin:0;display:grid;place-items:center;height:100vh;background:#f8fafc;font:700 20px Cairo,Tahoma;color:#17283d" dir="rtl">جاري تشغيل NextStore...</body>'));
    win.once('ready-to-show', () => win.show());

    const origin = () => `http://127.0.0.1:${port}`;
    win.webContents.setWindowOpenHandler(({ url }) => {
        if (url.startsWith(origin()) || url === 'about:blank') return { action: 'allow' };
        shell.openExternal(url);
        return { action: 'deny' };
    });

    try {
        // كل فتحة للبرنامج تبدأ بتسجيل دخول جديد (مفيش جلسة محفوظة من المرة اللي فاتت)
        await session.defaultSession.clearStorageData({ storages: ['cookies'] });
        prepareData();
        artisan('migrate', '--force');
        port = await freePort(PREFERRED_PORT);
        env.NS_DEVICE_FP = (await machineId()).fingerprint; // بصمة الجهاز تمر للسيرفر المحلي بدون واجهة
        startPhp();
        await waitForServer();
        win.loadURL(`${origin()}/login`);
    } catch (e) {
        dialog.showErrorBox('NextStore - خطأ في التشغيل', String(e.message || e));
        app.quit();
    }
}

// ================= IPC: الطابعات والطباعة الصامتة وبصمة الجهاز =================
ipcMain.handle('ns:get-printers', async () => (await win.webContents.getPrintersAsync()).map(p => p.name));

// نافذة طباعة واحدة مخفية بنعيد استخدامها + طابور، بدل فتح نافذة جديدة مع كل فاتورة (ده كان سبب البطء)
const FEED_MM = 14; // ورق فاضي بعد آخر سطر عشان الكاتر يقص بعد الفاتورة مش قبلها
let printWin = null;
let printQueue = Promise.resolve();
const printFile = path.join(os.tmpdir(), 'nextstore-print.html');

function getPrintWin() {
    if (!printWin || printWin.isDestroyed()) {
        printWin = new BrowserWindow({ show: false, width: 300, height: 1200, webPreferences: { sandbox: true, backgroundThrottling: false } });
    }
    return printWin;
}

async function silentPrint(p) {
    const paper = String(p.paper || '80mm').toLowerCase();
    const sheet = { a4: 'A4', a5: 'A5' }[paper];
    const mm = paper === '58mm' ? 48 : 72; // المنطقة المطبوعة فعلياً في رول 80/58
    const t0 = Date.now();
    fs.writeFileSync(printFile, `<!doctype html><html><head><meta charset="utf-8"><style>${p.css || ''}</style></head><body>${localImages(p.html || '')}</body></html>`);
    const pw = getPrintWin();
    pw.setContentSize(sheet ? 900 : Math.round(mm * 96 / 25.4), 1200);
    await pw.loadFile(printFile);
    const heightPx = await pw.webContents.executeJavaScript(
        'Promise.all([...document.images].map(i => (i.decode ? i.decode() : Promise.resolve()).catch(() => i.remove()))).then(() => document.body.scrollHeight)');
    const pageSize = sheet || { width: mm * 1000, height: Math.max(40000, Math.ceil(heightPx * 25400 / 96) + FEED_MM * 1000) };
    await new Promise((resolve, reject) => pw.webContents.print(
        { silent: true, deviceName: p.printerName || '', printBackground: true, margins: { marginType: 'none' }, pageSize },
        (ok, reason) => (ok ? resolve() : reject(new Error(reason || 'print failed')))));
    fs.appendFile(path.join(DATA, 'print.log'), `${new Date().toISOString()} ${p.printerName || '-'} ${Date.now() - t0}ms
`, () => {});
    return { ok: true };
}

// سيرفر PHP بيخدم طلب واحد في المرة، فاللوجو بيتقرا من الديسك مباشرة بدل ما يستنى ورا طلبات الصفحة
function localImages(html) {
    return html.replace(/(src=")https?:\/\/127\.0\.0\.1:\d+\/([^"?#]+)/g, (m, attr, rel) => {
        rel = decodeURIComponent(rel);
        const file = rel.startsWith('storage/') ? path.join(STORAGE, 'app', 'public', rel.slice(8)) : path.join(LARAVEL, 'public', rel);
        return fs.existsSync(file) ? attr + pathToFileURL(file).href : m;
    });
}

ipcMain.handle('ns:silent-print', (_e, p = {}) => {
    const job = printQueue.then(() => silentPrint(p));
    printQueue = job.catch(() => {});
    return job;
});

function run(cmd, args) {
    return new Promise(resolve => execFile(cmd, args, { windowsHide: true }, (err, out) => resolve(err ? '' : String(out).trim())));
}

async function machineId() {
    const uuid = await run('powershell', ['-NoProfile', '-Command', '(Get-CimInstance Win32_ComputerSystemProduct).UUID']);
    const guid = ((await run('reg', ['query', 'HKLM\SOFTWARE\Microsoft\Cryptography', '/v', 'MachineGuid'])).match(/MachineGuid\s+REG_SZ\s+(\S+)/) || [])[1] || '';
    return { uuid, machineGuid: guid, fingerprint: crypto.createHash('sha256').update(`${uuid}|${guid}`).digest('hex') };
}

ipcMain.handle('ns:machine-id', machineId);

ipcMain.handle('ns:quit', () => { forceClose = true; win && win.close(); });
ipcMain.handle('ns:app-info', () => {
    let code = {};
    try { code = JSON.parse(fs.readFileSync(path.join(LARAVEL, 'version.json'), 'utf8')); } catch (_) { /* ignore */ }
    return { shellVersion: app.getVersion(), codeVersion: code.version || null, dataDir: DATA, port };
});

// ================= دورة حياة التطبيق =================
if (!app.requestSingleInstanceLock()) {
    app.quit();
} else {
    app.on('second-instance', () => { if (win) { win.isMinimized() && win.restore(); win.focus(); } });
    app.whenReady().then(createWindow);
    app.on('before-quit', () => { app.isQuitting = true; stopPhp(); });
    app.on('window-all-closed', () => app.quit());
    process.on('exit', stopPhp);
}
