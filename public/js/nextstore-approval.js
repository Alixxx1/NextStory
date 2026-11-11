(function () {
    'use strict';
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    async function send(url, options) {
        const res = await fetch(url, {
            ...options,
            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', ...(options.headers || {}) }
        });
        const data = await res.json().catch(() => ({}));
        return { res, data };
    }

    async function askManager(actionName) {
        const r = await Swal.fire({
            title: 'تصريح المدير مطلوب', text: actionName, input: 'password',
            inputPlaceholder: 'كلمة مرور المدير', inputAttributes: { autocomplete: 'off' },
            showCancelButton: true, confirmButtonText: 'تصريح', cancelButtonText: 'إلغاء', confirmButtonColor: '#119744'
        });
        return r.isConfirmed ? r.value : null;
    }

    /**
     * طلب API، ولو رجع 403 بسبب صلاحية: يطلب كلمة مرور المدير، يسجل التصريح ثم يعيد الطلب مرة واحدة.
     * يرجع البيانات أو يرمي Error برسالة السيرفر.
     */
    window.nsFetchWithApproval = async function (url, options = {}, actionName = 'عملية تحتاج صلاحية') {
        let { res, data } = await send(url, options);
        if (res.status === 403 && data.permission) {
            const password = await askManager(actionName);
            if (!password) throw new Error('تم إلغاء العملية.');
            const auth = await send('/api/auth/authorize-manager', { method: 'POST', body: JSON.stringify({ password, permission: data.permission, action_name: actionName }) });
            if (!auth.res.ok) throw new Error(auth.data.message || 'كلمة مرور المدير غير صحيحة.');
            ({ res, data } = await send(url, options));
        }
        if (!res.ok) throw new Error(data.message || 'حدث خطأ أثناء الاتصال بالخادم.');
        return data;
    };
})();
