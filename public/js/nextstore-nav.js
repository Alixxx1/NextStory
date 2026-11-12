/* التحكم المركزي في الإغلاق: ESC يقفل أعلى نافذة مفتوحة، ولو مفيش يخرج من الصفحة (بتأكيد لو في بيانات)،
   وفي الشاشة الرئيسية يسأل عن الخروج من البرنامج. زرار X بتاع Electron بيعدّي من هنا (requestClose). */
(() => {
    const HOME = ['/pos/archive', '/'];
    const isHome = () => HOME.includes(location.pathname);
    const EXIT_BTN = '[id^="btn-exit"],[id^="btnExit"],.exit-btn,.btn-exit-screen';
    const POPUPS = '.modal.show,.ns-modal-backdrop.open,.custom-modal-backdrop,.quick-modal-backdrop,[class*="modal"][class*="open"],[class*="overlay"][class*="show"],[class*="overlay"][class*="open"],[id*="Overlay"],[id*="overlay"],[id*="Modal"],[id*="modal"]';
    let dirty = false, dlg = null;

    // ---- سلسلة الصفحات: الخروج يرجّع للصفحة اللي قبلها بالترتيب، وآخرها الشاشة الرئيسية ----
    const cur = location.pathname + location.search;
    let stack = [];
    try { stack = JSON.parse(sessionStorage.getItem('ns.nav') || '[]'); } catch (e) {}
    const at = stack.lastIndexOf(cur);
    if (isHome()) stack = [cur];
    else if (at >= 0) stack = stack.slice(0, at + 1);
    else stack.push(cur);
    try { sessionStorage.setItem('ns.nav', JSON.stringify(stack.slice(-30))); } catch (e) {}
    const backTarget = () => (stack.length > 1 ? stack[stack.length - 2] : '/pos/archive');

    // ---- بيانات غير محفوظة: أي تعديل في حقل (غير البحث/الفلاتر) لحد أول حفظ ناجح ----
    document.addEventListener('input', e => {
        const t = e.target;
        if (!e.isTrusted || !t.matches || !t.matches('input,textarea,select') || t.type === 'search' || t.type === 'file') return;
        if (/search|filter|date|barcode|find|query/i.test(`${t.id} ${t.name} ${t.className}`)) return;
        dirty = true;
    }, true);
    const _fetch = window.fetch;
    // منع التكرار: مفتاح جديد لكل عملية إرسال؛ الضغطة المزدوجة وإعادة الإرسال بعد قطع الشبكة بياخدوا نفس المفتاح
    // (فالسيرفر بيرجّع الرد الأول)، وبعد ما العملية تنجح بثانيتين المفتاح بيتشال فأي عملية جديدة مقصودة بتتسجل عادي حتى لو مطابقة
    const idemKeys = new Map();
    const newKey = () => (crypto.randomUUID ? crypto.randomUUID() : Date.now() + '-' + Math.random().toString(36).slice(2));
    window.fetch = function (url, opts) {
        let sig = null;
        if (opts && /^(POST|PUT|PATCH|DELETE)$/i.test(opts.method || '') && typeof opts.body === 'string' && !/\/api\/(auth|security)\//.test(String(url))) {
            const headers = new Headers(opts.headers || {});
            if (!headers.has('X-Idempotency-Key')) {
                sig = `${opts.method} ${url} ${opts.body}`;
                idemKeys.has(sig) || idemKeys.set(sig, newKey());
                headers.set('X-Idempotency-Key', idemKeys.get(sig));
                arguments[1] = { ...opts, headers };
            }
        }
        const p = _fetch.apply(this, arguments);
        sig && p.then(r => { if (r.ok) setTimeout(() => idemKeys.delete(sig), 2000); }).catch(() => {}); // قطع الشبكة: المفتاح يفضل لإعادة المحاولة
        if (opts && /^(POST|PUT|PATCH|DELETE)$/i.test(opts.method || '')) p.then(r => { if (r.ok) dirty = false; }).catch(() => {});
        return p;
    };
    const hasData = () => { try { return dirty || (typeof window.nsDirty === 'function' && !!window.nsDirty()); } catch (e) { return dirty; } };

    // ---- نافذة تأكيد خفيفة (Enter موافق / Esc إلغاء) ----
    function confirmBox(text, okLabel, onOk) {
        if (dlg) return;
        dlg = document.createElement('div');
        dlg.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:2147483000;display:flex;align-items:center;justify-content:center;font-family:Cairo,Tahoma,sans-serif;direction:rtl';
        dlg.innerHTML = `<div style="background:#fff;border:1px solid #cbd5e1;border-radius:10px;width:min(420px,92vw);overflow:hidden">
            <div style="background:#17283d;color:#fff;padding:10px 16px;font-weight:800">تأكيد الخروج</div>
            <div style="padding:18px 16px;font-weight:700;color:#0f172a;line-height:1.8">${text}</div>
            <div style="display:flex;gap:8px;justify-content:flex-start;padding:0 16px 16px">
                <button type="button" data-ok style="height:38px;padding:0 18px;border-radius:7px;border:1px solid #fecaca;background:#fff;color:#dc2626;font-weight:800;cursor:pointer">${okLabel} (Enter)</button>
                <button type="button" data-no style="height:38px;padding:0 18px;border-radius:7px;border:1px solid #cbd5e1;background:#fff;color:#1e293b;font-weight:800;cursor:pointer">بقاء (Esc)</button></div></div>`;
        const close = () => { dlg && dlg.remove(); dlg = null; };
        dlg.querySelector('[data-ok]').onclick = () => { close(); onOk(); };
        dlg.querySelector('[data-no]').onclick = close;
        document.body.appendChild(dlg);
        dlg.querySelector('[data-ok]').focus();
    }

    // ---- أعلى نافذة مفتوحة ----
    const shown = el => {
        const s = getComputedStyle(el), r = el.getBoundingClientRect();
        return s.display !== 'none' && s.visibility !== 'hidden' && +s.opacity > 0 && r.width > 80 && r.height > 80 && (el.classList.contains('modal') || s.position === 'fixed' || s.position === 'absolute');
    };
    function topPopup() {
        if (window.Swal && Swal.isVisible && Swal.isVisible()) return { swal: true };
        // نستبعد خلفية/حاوية بوتستراب بس (modal-backdrop بالظبط)، مش ns-modal-backdrop و custom-modal-backdrop اللي هي النوافذ نفسها
        const vis = [...document.querySelectorAll(POPUPS)].filter(el => el !== dlg && !el.classList.contains('modal-backdrop') && !el.classList.contains('modal-dialog') && shown(el));
        vis.sort((a, b) => (parseInt(getComputedStyle(a).zIndex) || 0) - (parseInt(getComputedStyle(b).zIndex) || 0));
        return vis.length ? { el: vis[vis.length - 1] } : null;
    }
    function closePopup(p) {
        if (p.swal) return Swal.close();
        const el = p.el;
        if (el.classList.contains('modal') && window.bootstrap) return bootstrap.Modal.getOrCreateInstance(el).hide();
        const btn = el.querySelector('[data-bs-dismiss="modal"],.btn-close,[data-close],.modal-close,[id*="Close"],[id*="close"],[id*="Cancel"],[id*="cancel"],[class*="close"]');
        if (btn) return btn.click();
        ['open', 'show', 'active', 'is-open'].forEach(c => el.classList.remove(c));
        if (getComputedStyle(el).display !== 'none') el.style.display = 'none';
    }

    // ---- الخروج ----
    function leavePage(discard) { if (discard === true && typeof window.nsOnDiscard === 'function') { try { window.nsOnDiscard(); } catch (e) {} } location.href = backTarget(); }
    function quitApp() { window.electronAPI && electronAPI.quit ? electronAPI.quit() : (location.href = '/logout'); }
    function requestExit() {
        if (isHome()) return confirmBox('هل أنت متأكد أنك تريد الخروج من البرنامج؟', 'خروج', quitApp);
        if (!hasData()) return leavePage();
        confirmBox(window.nsExitText || 'هل تريد الخروج من هذه الصفحة؟<br><small style="color:#dc2626">سيؤدي ذلك إلى فقد البيانات غير المحفوظة.</small>', 'خروج', () => leavePage(true));
    }

    window.NextStoreNav = { requestClose() { if (dlg) return true; const p = topPopup(); if (p) closePopup(p); else requestExit(); return true; } };

    const keys = window.NS_NAV_KEYS !== false; // شاشة البيع بتدير ESC بنفسها
    // أزرار الخروج في الصفحات بتمر على نفس المنطق
    document.addEventListener('click', e => {
        const b = e.target.closest && e.target.closest(EXIT_BTN);
        if (keys && b && !dlg) { e.preventDefault(); e.stopImmediatePropagation(); requestExit(); }
    }, true);
    window.addEventListener('keydown', e => {
        if (dlg) { // نافذة التأكيد مفتوحة
            if (e.key === 'Escape') { e.preventDefault(); e.stopImmediatePropagation(); dlg.querySelector('[data-no]').click(); }
            else if (e.key === 'Enter') { e.preventDefault(); e.stopImmediatePropagation(); dlg.querySelector('[data-ok]').click(); }
            return;
        }
        // بوتستراب بيقفل نافذته قبل ما هاندلر الصفحة يشتغل، فبنسجل هنا (capture) إن كان في نافذة مفتوحة وقت الضغط
        if (e.key === 'Escape') window.nsEscHadPopup = !!topPopup();
        if (!keys || e.key !== 'Escape' || e.defaultPrevented) return;
        const t = e.target;
        if (t && t.matches && t.matches('input,textarea') && t.value && !t.readOnly && !t.closest(POPUPS) && (t.type === 'search' || /search|find|query/i.test(`${t.id} ${t.name} ${t.className}`))) { // أول Esc يمسح البحث
            e.preventDefault(); e.stopImmediatePropagation(); t.value = ''; t.dispatchEvent(new Event('input', { bubbles: true })); return;
        }
        e.preventDefault(); e.stopImmediatePropagation();
        const p = topPopup();
        p ? closePopup(p) : requestExit();
    }, true);
})();
