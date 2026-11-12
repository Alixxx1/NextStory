/* NextStore - ترتيب نتائج البحث في كل الصفحات: المطابق بالظبط أولاً.
   NSSearch.sort(list, query, item => ({ code, name, phones: [...], text })) ← نفس العناصر مرتبة (الترتيب الأصلي جوه كل درجة).
   الدرجات: كود مطابق (022 = 22) ← تليفون/باركود مطابق ← اسم مطابق ← كود يبدأ بيه ← اسم يبدأ بيه ← تليفون/باركود يبدأ بيه ← كلمة في الاسم تبدأ بيه ← الاسم فيه ← الباقي */
window.NSSearch = (() => {
    const norm = v => String(v ?? '')
        .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d)).replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d))
        .replace(/[ً-ٰٟـ]/g, '').replace(/[أإآ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي')
        .toLowerCase().replace(/\s+/g, ' ').trim();
    const sameNumber = (a, b) => /^\d+$/.test(a) && /^\d+$/.test(b) && a.replace(/^0+/, '') === b.replace(/^0+/, '');

    function rank(q, f) {
        const code = norm(f.code), name = norm(f.name), phones = (f.phones || []).map(norm).filter(Boolean);
        if (code && (code === q || sameNumber(code, q))) return 0;
        if (phones.some(p => p === q || p.replace(/\D/g, '') === q)) return 1;
        if (name === q) return 2;
        if (code.startsWith(q)) return 3;
        if (name.startsWith(q)) return 4;
        if (phones.some(p => p.startsWith(q))) return 5;
        if ((' ' + name).includes(' ' + q)) return 6;
        if (name.includes(q)) return 7;
        return 8;
    }

    function sort(list, query, fields) {
        const q = norm(query);
        if (!q || !Array.isArray(list) || list.length < 2) return list;
        return list.map((item, i) => ({ item, i, r: rank(q, fields(item) || {}) }))
            .sort((a, b) => a.r - b.r || a.i - b.i).map(x => x.item);
    }

    return { norm, rank: (query, f) => rank(norm(query), f), sort };
})();
