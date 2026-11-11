/* إضافة مورد جديد بسرعة (F2) وحفظه في قاعدة البيانات — يرجّع بيانات المورد أو null */
window.NextStoreSupplierAdd = async function (name = '') {
    const field = (id, label, attrs = '') => `<label style="display:block;margin:8px 0 3px;font-weight:700">${label}</label><input id="nsSa_${id}" class="swal2-input" style="margin:0;width:100%;height:38px;font-size:14px" ${attrs}>`;
    const val = id => document.getElementById('nsSa_' + id).value.trim();
    const r = await Swal.fire({
        title: 'إضافة مورد جديد', width: 460, focusConfirm: false, showCancelButton: true, confirmButtonColor: '#119744',
        confirmButtonText: 'حفظ (Enter)', cancelButtonText: 'إلغاء (Esc)',
        html: `<div style="text-align:right;font-size:13px">${field('contact', 'اسم المورد <span style="color:#dc2626">*</span>', `value="${String(name).replace(/"/g, '&quot;')}" maxlength="150"`)}${field('company', 'اسم الشركة', 'maxlength="150"')}${field('phone', 'التليفون', 'maxlength="30" inputmode="tel" dir="ltr"')}${field('address', 'العنوان', 'maxlength="255"')}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">${`<div>${field('opening', 'رصيد أول المدة', 'value="0" inputmode="decimal" dir="ltr"')}</div>`}<div><label style="display:block;margin:8px 0 3px;font-weight:700">نوع الرصيد</label><select id="nsSa_type" class="swal2-select" style="margin:0;width:100%;height:38px;font-size:14px"><option value="credit">دائن (له عندنا)</option><option value="debit">مدين (علينا له)</option></select></div></div></div>`,
        didOpen: () => {
            const box = Swal.getHtmlContainer();
            box.addEventListener('keydown', e => { if (e.key === 'Enter' && e.target.tagName === 'INPUT') { e.preventDefault(); Swal.clickConfirm(); } });
            setTimeout(() => document.getElementById('nsSa_contact').focus(), 30);
        },
        preConfirm: async () => {
            if (!val('contact')) return Swal.showValidationMessage('اكتب اسم المورد');
            try {
                const res = await fetch('/api/suppliers/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                    body: JSON.stringify({ contact: val('contact'), company: val('company'), phone: val('phone'), address: val('address'), openingBalance: parseFloat(val('opening')) || 0, balanceType: document.getElementById('nsSa_type').value }),
                });
                const out = await res.json();
                if (!res.ok || out.status !== 'success') return Swal.showValidationMessage(out.message || 'تعذر حفظ المورد');
                return out.supplier;
            } catch (e) { Swal.showValidationMessage('تعذر الاتصال بالسيرفر'); }
        },
    });
    return r.isConfirmed ? r.value : null;
};
