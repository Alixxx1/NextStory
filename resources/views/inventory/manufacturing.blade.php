<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المخزن | التصنيع والعروض التجميعية - NextStore SaaS</title>

    <!-- خط Cairo المعتمد -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- أيقونات FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- مكتبة SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --ns-green: #119744;
            --ns-green-hover: #0d7a36;
            --ns-green-soft: #ecfdf5;
            --ns-navy: #17283d;
            --ns-navy-dark: #0f1c2c;
            --ns-bg: #eef2f5;
            --ns-card: #ffffff;
            --ns-border: #d5dde7;
            --ns-text: #1e293b;
            --ns-muted: #64748b;
            --ns-danger: #dc2626;
            --ns-warning: #d97706;
            --ns-info: #0284c7;
            --ns-purple: #7c3aed;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
            user-select: none;
        }

        body {
            background-color: var(--ns-bg);
            color: var(--ns-text);
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            font-size: 13px;
        }

        /* ================= 1. الهيدر العلوي المدمج ================= */
        .ns-header {
            background: linear-gradient(135deg, var(--ns-navy) 0%, var(--ns-navy-dark) 100%);
            color: #fff;
            height: 48px;
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--ns-green);
            flex-shrink: 0;
        }

        .ns-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ns-logo-icon {
            background: var(--ns-green);
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .ns-header-right h1 {
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .module-pill {
            background: rgba(17, 151, 68, 0.25);
            border: 1px solid #4ade80;
            color: #4ade80;
            font-size: 10.5px;
            padding: 1px 8px;
            border-radius: 5px;
        }

        .top-nav-links {
            display: flex;
            gap: 6px;
            margin-right: 6px;
        }

        .btn-top-link {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.16);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
        }

        .btn-top-link:hover {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
        }

        .btn-exit {
            background: var(--ns-danger);
            color: #fff;
            border: none;
            padding: 5px 15px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ================= 2. مساحة العمل الرئيسية ================= */
        .manufacturing-workspace {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 410px;
            gap: 12px;
            padding: 10px 14px;
            overflow: hidden;
        }

        .ns-panel {
            background: var(--ns-card);
            border: 1px solid var(--ns-border);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .ns-panel-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .mode-badge {
            background: var(--ns-green);
            color: #fff;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11px;
        }

        .mode-badge.edit {
            background: var(--ns-warning);
        }

        /* ================= القسم الأيمن: شاشة التصنيع الواسعة والمنظمة ================= */
        .builder-body {
            flex: 1;
            padding: 10px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            overflow: hidden;
        }

        .top-config-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .product-fields-row {
            display: grid;
            grid-template-columns: 2.2fr 1.3fr 1fr 1.1fr;
            gap: 8px;
            align-items: end;
        }

        .field-col {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .field-col label {
            font-size: 11.5px;
            font-weight: 800;
            color: var(--ns-navy);
        }

        .ns-input,
        .ns-select {
            height: 33px;
            padding: 0 10px;
            border: 1.5px solid var(--ns-border);
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--ns-text);
            background: #fff;
            width: 100%;
            transition: all 0.15s;
        }

        .ns-input:focus,
        .ns-select:focus {
            outline: none;
            border-color: var(--ns-green);
            box-shadow: 0 0 0 3px rgba(17, 151, 68, 0.12);
        }

        .search-and-mode-row {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 10px;
            align-items: center;
        }

        .raw-search-input-wrap {
            position: relative;
            width: 100%;
        }

        .raw-search-input-wrap i {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ns-green);
            font-size: 13px;
        }

        .raw-search-input-wrap input {
            padding-right: 32px;
            background: #fff;
            border-color: #93c5fd;
        }

        .raw-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            right: 0;
            left: 0;
            background: #fff;
            border: 2px solid var(--ns-navy);
            border-radius: 10px;
            max-height: 240px;
            overflow-y: auto;
            z-index: 1500;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.18);
            display: none;
        }

        .raw-dropdown-item {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            font-weight: 700;
        }

        .raw-dropdown-item:hover,
        .raw-dropdown-item.active {
            background: var(--ns-green-soft);
        }

        .deduction-mode-switch {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            background: #e2e8f0;
            padding: 3px;
            border-radius: 8px;
        }

        .deduct-mode-option {
            position: relative;
            cursor: pointer;
        }

        .deduct-mode-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .deduct-mode-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 29px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 800;
            color: #475569;
            transition: all 0.15s ease;
        }

        .deduct-mode-option input[value="immediate"]:checked + .deduct-mode-label {
            background: var(--ns-green);
            color: #fff;
            box-shadow: 0 2px 6px rgba(17, 151, 68, 0.25);
        }

        .deduct-mode-option input[value="on_sale"]:checked + .deduct-mode-label {
            background: var(--ns-purple);
            color: #fff;
            box-shadow: 0 2px 6px rgba(124, 58, 237, 0.25);
        }

        /* ================= سلة المكونات الواسعة والمريحة للعين ================= */
        .components-basket-container {
            flex: 1;
            border: 1.5px solid var(--ns-border);
            border-radius: 10px;
            overflow-y: auto;
            background: #fff;
            display: flex;
            flex-direction: column;
        }

        .ns-basket-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        .ns-basket-table thead th {
            background: var(--ns-navy);
            color: #fff;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
            position: sticky;
            top: 0;
            z-index: 10;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            white-space: nowrap;
        }

        .ns-basket-table tbody tr {
            border-bottom: 1px solid #eef2f6;
            height: 43px;
            cursor: pointer;
            transition: background 0.12s;
        }

        .ns-basket-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .ns-basket-table tbody tr:hover {
            background: #f0f9ff;
        }

        .ns-basket-table tbody tr.active-basket-row {
            background: #dcfce7 !important;
            outline: 2px solid var(--ns-green);
            outline-offset: -2px;
        }

        .ns-basket-table tbody td {
            padding: 5px 12px;
            font-size: 13px;
            font-weight: 700;
            border-left: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        .comp-name-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--ns-navy);
            font-weight: 800;
            text-decoration: none;
            padding: 2px 6px;
            border-radius: 6px;
            transition: all 0.12s;
        }

        .comp-name-btn:hover {
            background: #e0f2fe;
            color: var(--ns-info);
        }

        .comp-name-btn i {
            font-size: 11px;
            color: var(--ns-muted);
        }

        .qty-inline-group {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid #cbd5e1;
            border-radius: 7px;
            overflow: hidden;
            background: #fff;
        }

        .qty-inline-group:focus-within {
            border-color: var(--ns-green);
        }

        .qty-input-cell {
            width: 75px;
            height: 29px;
            text-align: center;
            font-weight: 900;
            font-size: 13.5px;
            color: var(--ns-navy);
            border: none;
            outline: none;
            direction: ltr;
        }

        .unit-select-cell {
            height: 29px;
            border: none;
            border-right: 1px solid #cbd5e1;
            font-weight: 800;
            font-size: 11.5px;
            padding: 0 6px;
            background: #f8fafc;
            color: var(--ns-navy);
            outline: none;
            cursor: pointer;
        }

        .ratio-pill {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 1px 8px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 800;
            direction: ltr;
            display: inline-block;
        }

        .btn-remove-comp {
            background: #fef2f2;
            color: var(--ns-danger);
            border: 1px solid #fecaca;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.12s;
        }

        .btn-remove-comp:hover {
            background: var(--ns-danger);
            color: #fff;
        }

        /* ================= الشريط السفلي المودرن للتكلفة والتسعير ================= */
        .modern-pricing-Ribbon {
            background: #ffffff;
            border: 1.5px solid var(--ns-border);
            border-radius: 10px;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .summary-stats-cluster {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            padding-left: 16px;
            border-left: 1px solid #e2e8f0;
        }

        .stat-item:last-child {
            border-left: none;
            padding-left: 0;
        }

        .stat-item span {
            font-size: 11px;
            font-weight: 700;
            color: var(--ns-muted);
        }

        .stat-item strong {
            font-size: 14.5px;
            font-weight: 900;
            color: var(--ns-navy);
            direction: ltr;
        }

        .sleek-pricing-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .modern-field-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 0 8px;
            height: 36px;
        }

        .modern-field-pill.highlight-green {
            border-color: var(--ns-green);
            background: var(--ns-green-soft);
        }

        .modern-field-pill label {
            font-size: 11.5px;
            font-weight: 800;
            color: var(--ns-muted);
            white-space: nowrap;
        }

        .modern-field-pill.highlight-green label {
            color: var(--ns-green-hover);
        }

        .modern-field-pill input {
            border: none;
            background: transparent;
            outline: none;
            text-align: center;
            font-weight: 900;
            direction: ltr;
        }

        .input-percent-clean {
            width: 52px;
            font-size: 14px;
            color: var(--ns-navy);
        }

        .input-price-clean {
            width: 90px;
            font-size: 17px;
            color: var(--ns-green-hover);
        }

        .net-profit-chip {
            background: var(--ns-navy);
            color: #4ade80;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .builder-footer {
            background: #f8fafc;
            padding: 8px 14px;
            border-top: 1px solid var(--ns-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-manufacture-save {
            flex: 1;
            height: 38px;
            background: var(--ns-green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s;
        }

        .btn-manufacture-save:hover {
            background: var(--ns-green-hover);
        }

        .btn-reset-builder {
            height: 38px;
            padding: 0 14px;
            background: #fff;
            color: var(--ns-navy);
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
        }

        /* ================= القسم الأيسر: بطاقات الأصناف المصنعة + أزرار التعديل والحذف من الخارج ================= */
        .batches-list-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .batches-filter-bar {
            padding: 8px 10px;
            background: #f8fafc;
            border-bottom: 1px solid var(--ns-border);
            display: flex;
            gap: 6px;
        }

        .batches-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .batch-card {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 12px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .batch-card:hover {
            border-color: var(--ns-green);
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(17, 151, 68, 0.08);
        }

        .batch-card.selected {
            border: 2px solid var(--ns-green);
            background: #f0fdf4;
        }

        .batch-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }

        .batch-title {
            font-weight: 900;
            font-size: 13.5px;
            color: var(--ns-navy);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .deduct-type-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .deduct-type-badge.immediate {
            background: #dcfce7;
            color: #15803d;
        }

        .deduct-type-badge.on-sale {
            background: #f3e8ff;
            color: #6d28d9;
        }

        .ingredient-chips-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            padding: 2px 0;
        }

        .ingredient-pill-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 800;
            transition: all 0.12s;
        }

        .ingredient-pill-chip:hover {
            background: #e0f2fe;
            border-color: #7dd3fc;
            color: #0369a1;
        }

        .ingredient-pill-chip .chip-qty {
            background: var(--ns-navy);
            color: #fff;
            padding: 0 6px;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 900;
        }

        .batch-meta-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 7px;
            border-top: 1px dashed #e2e8f0;
            font-size: 11.5px;
            color: var(--ns-muted);
            font-weight: 700;
        }

        /* أزرار التعديل والحذف للخلطة من الخارج */
        .batch-card-actions {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-card-edit {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s;
        }

        .btn-card-edit:hover {
            background: var(--ns-info);
            color: #fff;
            border-color: var(--ns-info);
        }

        .btn-card-delete {
            background: #fef2f2;
            color: var(--ns-danger);
            border: 1px solid #fecaca;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s;
        }

        .btn-card-delete:hover {
            background: var(--ns-danger);
            color: #fff;
            border-color: var(--ns-danger);
        }

        /* ================= 3. نافذة "صنف مخزن" الأصلية ================= */
        .ns-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            z-index: 3000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 14px;
        }

        .ns-modal-backdrop.open {
            display: flex;
        }

        .ns-modal-window {
            background: #f4f6f9;
            width: 760px;
            max-width: 96vw;
            max-height: 94vh;
            border-radius: 10px;
            border: 2px solid var(--ns-navy);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .ns-modal-header {
            background: var(--ns-navy);
            color: #fff;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14.5px;
            font-weight: 800;
        }

        .btn-close-modal {
            background: none;
            border: none;
            color: #cbd5e1;
            font-size: 18px;
            cursor: pointer;
        }

        .ns-modal-body {
            padding: 14px 18px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .fieldset-box {
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-radius: 8px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .fieldset-title {
            font-size: 12.5px;
            font-weight: 900;
            color: var(--ns-navy);
            border-bottom: 1px dashed var(--ns-border);
            padding-bottom: 4px;
        }

        .modal-form-row {
            display: grid;
            grid-template-columns: 115px 1fr;
            align-items: center;
            gap: 10px;
        }

        .modal-form-row label {
            font-weight: 800;
            color: var(--ns-navy);
            font-size: 12.5px;
        }

        .barcode-navy-bar {
            background: #232784;
            color: #fff;
            font-weight: 900;
            font-size: 15px;
            text-align: center;
            letter-spacing: 1.5px;
            height: 33px;
            border: 2px solid #1e1b4b;
            border-radius: 5px;
            width: 100%;
            direction: ltr;
        }

        .ns-tabs-header {
            display: flex;
            border-bottom: 2px solid var(--ns-navy);
            gap: 4px;
        }

        .ns-tab-btn {
            padding: 6px 14px;
            background: #e2e8f0;
            border: 1.5px solid var(--ns-border);
            border-bottom: none;
            border-radius: 7px 7px 0 0;
            font-size: 12.5px;
            font-weight: 800;
            color: var(--ns-navy);
            cursor: pointer;
        }

        .ns-tab-btn.active {
            background: var(--ns-navy);
            color: #fff;
            border-color: var(--ns-navy);
        }

        .ns-tab-pane {
            display: none;
            background: #fff;
            border: 1.5px solid var(--ns-border);
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 14px;
        }

        .ns-tab-pane.active {
            display: block;
        }

        .ns-modal-footer {
            background: #e2e8f0;
            padding: 10px 18px;
            border-top: 1.5px solid var(--ns-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .ns-action-btn {
            height: 35px;
            padding: 0 14px;
            border-radius: 6px;
            border: 1.5px solid #94a3b8;
            background: #fff;
            color: var(--ns-navy);
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ns-action-btn.primary-green {
            background: var(--ns-green);
            color: #fff;
            border-color: var(--ns-green);
        }
    </style>
</head>
<body>

    <!-- ==================== 1. الهيدر العلوي ==================== -->
    <header class="ns-header">
        <div class="ns-header-right">
            <div class="ns-logo-icon">
                <i class="fa-solid fa-industry"></i>
            </div>
            <h1>
                <span class="module-pill">المخزن</span>
                <span>التصنيع والعروض التجميعية</span>
            </h1>

            <div class="top-nav-links">
                <a href="/inventory/manufacturing-log" class="btn-top-link">
                    <i class="fa-solid fa-boxes-packing"></i> سجل الأصناف المصنعة
                </a>
                <a href="/inventory/items" class="btn-top-link">
                    <i class="fa-solid fa-boxes-stacked"></i> أصناف المخزن
                </a>
                <a href="/inventory/movement" class="btn-top-link">
                    <i class="fa-solid fa-arrows-left-right-to-line"></i> حركة الأصناف
                </a>
            </div>
        </div>

        <button type="button" class="btn-exit" id="btnExitPage">
            <i class="fa-solid fa-xmark"></i>
            <span>خروج (Esc)</span>
        </button>
    </header>

    <!-- ==================== 2. مساحة العمل الرئيسية ==================== -->
    <main class="manufacturing-workspace">

        <!-- ======== اليمين: شاشة التصنيع والعروض والسلة الواسعة ======== -->
        <section class="ns-panel">
            <div class="ns-panel-header">
                <span><i class="fa-solid fa-wand-magic-sparkles"></i> تكوين صنف مُصنَّع أو بوكس تجميعي</span>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:11px; color:#cbd5e1;">(الأسهم ↑↓ للتنقل بالسلة + Enter لتعديل بيانات الصنف نفسه)</span>
                    <span class="mode-badge" id="builderModeBadge">عملية جديدة</span>
                </div>
            </div>

            <div class="builder-body">
                <input type="hidden" id="editingBatchId" value="">

                <!-- البطاقة العلوية المدمجة: اسم الصنف + البحث + اختيار طريقة الخصم -->
                <div class="top-config-card">
                    <div class="product-fields-row">
                        <div class="field-col">
                            <label>اسم الخلطة / البوكس التجميعي: <span style="color:var(--ns-danger)">*</span></label>
                            <input type="text" id="mfgProductName" class="ns-input" placeholder="مثال: السبع بهارات الملكية / بوكس التقوى...">
                        </div>

                        <div class="field-col">
                            <label>التصنيف بالمخزن:</label>
                            <select id="mfgCategorySelect" class="ns-select"></select>
                        </div>

                        <div class="field-col">
                            <label>وحدة البيع:</label>
                            <select id="mfgFinalUnit" class="ns-select">
                                <option value="كيلو" selected>كيلو (1000 جم)</option>
                                <option value="بوكس">بوكس / عرض</option>
                                <option value="علبة">علبة</option>
                                <option value="قطعة">قطعة</option>
                            </select>
                        </div>

                        <div class="field-col">
                            <label>باركود الصنف:</label>
                            <input type="text" id="mfgBarcode" class="ns-input" style="direction:ltr; text-align:center; font-family:monospace;">
                        </div>
                    </div>

                    <div class="search-and-mode-row">
                        <!-- خانة البحث السريع لإضافة مكونات للسلة (تبدأ الفلترة من أول حرف) -->
                        <div class="raw-search-input-wrap">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="rawMaterialSearchInput" class="ns-input"
                                   placeholder="اكتب أول حرف للبحث وإضافة مكون للسلة بالاسم أو الباركود (F3)..." autocomplete="off">
                            <div class="raw-dropdown" id="rawMaterialDropdown"></div>
                        </div>

                        <!-- خيار طريقة الخصم من المخزون (فوري vs عند البيع) -->
                        <div class="deduction-mode-switch" title="اختر متى يتم خصم المكونات من المخزون">
                            <label class="deduct-mode-option">
                                <input type="radio" name="deductionModeRadio" value="immediate" checked>
                                <span class="deduct-mode-label">
                                    <i class="fa-solid fa-bolt"></i> خصم فوري (خلطة جاهزة)
                                </span>
                            </label>
                            <label class="deduct-mode-option">
                                <input type="radio" name="deductionModeRadio" value="on_sale">
                                <span class="deduct-mode-label">
                                    <i class="fa-solid fa-gift"></i> خصم عند البيع (بوكس/عرض)
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- السلة الواسعة والمريحة للعين -->
                <div class="components-basket-container" id="basketContainer">
                    <table class="ns-basket-table">
                        <thead>
                            <tr>
                                <th style="width:42px; text-align:center;">#</th>
                                <th>الصنف المكون (اضغط عليه أو Enter لتعديل سعره واسمه)</th>
                                <th style="text-align:center;">رصيد المخزن</th>
                                <th style="text-align:center;">سعر الشراء (التكلفة)</th>
                                <th style="text-align:center; width:175px;">الكمية الداخلة</th>
                                <th style="text-align:center;">النسبة</th>
                                <th style="text-align:center;">تكلفة المكون</th>
                                <th style="width:45px; text-align:center;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="componentsTableBody">
                            <!-- يملأ ديناميكياً -->
                        </tbody>
                    </table>
                </div>

                <!-- الشريط السفلي المودرن للتكلفة والتسعير ومربع النسبة (%) -->
                <div class="modern-pricing-Ribbon">
                    <div class="summary-stats-cluster">
                        <div class="stat-item">
                            <span>إجمالي الوزن / العدد</span>
                            <strong id="lblTotalProducedWeight">0 كجم</strong>
                        </div>
                        <div class="stat-item">
                            <span>إجمالي تكلفة المكونات</span>
                            <strong id="lblTotalBatchCost" style="color:var(--ns-danger);">0.00 ج.م</strong>
                        </div>
                        <div class="stat-item">
                            <span>تكلفة الوحدة / الكيلو</span>
                            <strong id="lblCostPerKg" style="color:var(--ns-info);">0.00 ج.م</strong>
                        </div>
                    </div>

                    <div class="sleek-pricing-controls">
                        <div class="modern-field-pill" title="نسبة الربح المئوية">
                            <label>الربح %</label>
                            <input type="text" id="mfgProfitPercentInput" class="input-percent-clean" value="25">
                        </div>

                        <div class="modern-field-pill highlight-green">
                            <label>سعر البيع (ج.م):</label>
                            <input type="text" id="mfgSellingPriceInput" class="input-price-clean" value="0.00">
                        </div>

                        <div class="net-profit-chip" id="lblNetProfitPerKg">
                            مكسب: 0.00 ج.م
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار الحفظ -->
            <div class="builder-footer">
                <button type="button" class="btn-manufacture-save" id="btnSaveManufacture">
                    <i class="fa-solid fa-circle-check"></i>
                    <span id="btnSaveManufactureText">حفظ وتأكيد التصنيع (F2)</span>
                </button>
                <button type="button" class="btn-reset-builder" id="btnResetBuilder">
                    <i class="fa-solid fa-plus"></i> جديد
                </button>
            </div>
        </section>

        <!-- ======== اليسار: بطاقات الأصناف المصنعة بالكبسولات الدائرية + زري التعديل والحذف ======== -->
        <aside class="ns-panel">
            <div class="ns-panel-header">
                <span><i class="fa-solid fa-layer-group"></i> الأصناف والبوكسات المُصنَّعة</span>
                <span style="font-size:11px; color:#cbd5e1;">تعديل وحذف مباشر</span>
            </div>

            <div class="batches-list-body">
                <div class="batches-filter-bar">
                    <input type="text" id="searchBatchesInput" class="ns-input" style="height:31px;" placeholder="ابحث في المصنعات أو البوكسات...">
                    <select id="filterBatchesMode" class="ns-select" style="width:130px; height:31px;">
                        <option value="all">الكل</option>
                        <option value="immediate">خلطات فورية</option>
                        <option value="on_sale">بوكسات عند البيع</option>
                    </select>
                </div>

                <div class="batches-scroll" id="batchesListContainer">
                    <!-- يملأ ديناميكياً ببطاقات الكبسولات -->
                </div>
            </div>
        </aside>

    </main>

    <!-- ==================== 3. نافذة "صنف مخزن" الأصلية ==================== -->
    <div class="ns-modal-backdrop" id="itemFormModal">
        <div class="ns-modal-window">
            <div class="ns-modal-header">
                <span id="itemModalTitle"><i class="fa-solid fa-box"></i> صنف مخزن (تعديل بيانات الصنف)</span>
                <button type="button" class="btn-close-modal" id="btnCloseItemModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <div class="ns-modal-body">
                <input type="hidden" id="formItemId" value="">

                <div class="fieldset-box">
                    <div class="fieldset-title">بيانات أساسية</div>

                    <div class="modal-form-row">
                        <label>النوع:</label>
                        <div style="display:flex; gap:16px; font-weight:700;">
                            <label><input type="radio" name="itemTypeRadio" value="صنف مخزني" checked> صنف مخزني</label>
                            <label><input type="radio" name="itemTypeRadio" value="صنف خدمي"> صنف خدمي</label>
                            <label><input type="radio" name="itemTypeRadio" value="خامات"> خامات</label>
                            <label><input type="radio" name="itemTypeRadio" value="صنف مجمع"> صنف مجمع</label>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>التصنيف:</label>
                        <select id="formCategorySelect" class="ns-select"></select>
                    </div>

                    <div class="modal-form-row">
                        <label>اسم الصنف: <span style="color:var(--ns-danger)">*</span></label>
                        <input type="text" id="formItemName" class="ns-input">
                    </div>

                    <div class="modal-form-row">
                        <label>الوصف:</label>
                        <input type="text" id="formItemDesc" class="ns-input">
                    </div>

                    <div class="modal-form-row">
                        <label>وحدة القياس:</label>
                        <div style="display:flex; align-items:center; gap:14px;">
                            <select id="formMainUnit" class="ns-select" style="width:170px;">
                                <option value="كيلو">كيلو</option>
                                <option value="قطعة">قطعة</option>
                                <option value="علبة">علبة</option>
                                <option value="بوكس">بوكس</option>
                                <option value="كرتونة">كرتونة</option>
                            </select>
                            <label style="font-size:12px;"><input type="radio" checked> وحدة شراء افتراضية</label>
                        </div>
                    </div>

                    <div class="modal-form-row">
                        <label>باركود:</label>
                        <input type="text" id="formBarcode" class="barcode-navy-bar">
                    </div>
                </div>

                <div>
                    <div class="ns-tabs-header">
                        <button type="button" class="ns-tab-btn active" data-tab="tabStockAndPricing">المخزون والتسعير</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabSubUnits">وحدات القياس الفرعية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabTaxSettings">إعدادات ضريبية</button>
                        <button type="button" class="ns-tab-btn" data-tab="tabItemOptions">خيارات</button>
                    </div>

                    <div class="ns-tab-pane active" id="tabStockAndPricing">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            <div class="modal-form-row">
                                <label>الكمية:</label>
                                <input type="text" id="formQtyReadonly" class="ns-input" style="background:#e2e8f0; text-align:center;" readonly>
                            </div>
                            <div class="modal-form-row">
                                <label>آخر سعر شراء: *</label>
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <input type="text" id="formLastBuyPrice" class="ns-input" style="text-align:center; font-weight:900;">
                                    <label style="font-size:11.5px;"><input type="checkbox" checked> تحديث</label>
                                </div>
                            </div>
                            <div class="modal-form-row">
                                <label>آخر تاريخ شراء:</label>
                                <input type="text" id="formLastBuyDate" class="ns-input" value="2026/10/19" style="text-align:center;">
                            </div>
                            <div class="modal-form-row">
                                <label>الحد الأدنى للمخزون:</label>
                                <input type="text" id="formMinStock" class="ns-input" value="0" style="text-align:center;">
                            </div>
                            <div class="modal-form-row">
                                <label>الحد الأقصى للمخزون:</label>
                                <input type="text" id="formMaxStock" class="ns-input" value="0" style="text-align:center;">
                            </div>
                        </div>

                        <div style="background:var(--ns-green-soft); border:1.5px solid var(--ns-green); padding:10px 12px; border-radius:8px; margin-top:10px;">
                            <div class="modal-form-row">
                                <label style="color:var(--ns-green-hover); font-size:13.5px;">سعر البيع (سعر قطاعي): *</label>
                                <input type="text" id="formRetailPrice" class="ns-input" style="width:180px; height:36px; font-size:17px; font-weight:900; text-align:center; color:var(--ns-green-hover); border-color:var(--ns-green);">
                            </div>
                        </div>
                    </div>

                    <div class="ns-tab-pane" id="tabSubUnits">
                        <p style="color:var(--ns-muted); font-size:12.5px;">يمكنك تعريف الوحدات الفرعية ومعامل التحويل والباركود الفرعي للصنف.</p>
                    </div>

                    <div class="ns-tab-pane" id="tabTaxSettings">
                        <label style="display:flex; align-items:center; gap:8px; font-weight:800;">
                            <input type="checkbox" checked> الضريبة متضمنة في السعر
                        </label>
                    </div>

                    <div class="ns-tab-pane" id="tabItemOptions">
                        <div style="display:flex; flex-direction:column; gap:8px; font-weight:800;">
                            <label><input type="checkbox" id="formOptDisabled"> تعطيل الصنف</label>
                            <label><input type="checkbox" id="formOptCommission"> تحديد عمولة أثناء البيع</label>
                            <label><input type="checkbox" id="formOptAllowPriceEdit"> إتاحة تحديد السعر أثناء البيع</label>
                            <label><input type="checkbox" id="formOptPinned"> إضافة إلى القائمة المثبتة في شاشة البيع</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ns-modal-footer">
                <span style="color:var(--ns-muted); font-size:12px; font-weight:700;">يتم تحديث بيانات الصنف في المخزن وسلة التصنيع فوراً</span>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="ns-action-btn primary-green" id="btnSaveProductModal">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>حفظ الصنف</span>
                    </button>
                    <button type="button" class="ns-action-btn" id="btnCancelProductModal">
                        <i class="fa-solid fa-circle-xmark" style="color:var(--ns-danger);"></i>
                        <span>إلغاء الأمر</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 4. الكود البرمجي الكامل (JavaScript) ==================== -->
    <script>
        (function () {
            'use strict';

            const CATEGORIES_KEY = 'nextstore_inventory_categories_v1';
            const PRODUCTS_KEY = 'nextstore_inventory_products_v1';
            const MOVEMENTS_KEY = 'nextstore_inventory_movements_v1';
            const MFG_BATCHES_KEY = 'nextstore_inventory_mfg_batches_v1';

            let categories = [];
            let products = [];
            let movementsSummary = [];
            let mfgBatches = [];

            let selectedComponents = [];
            let activeBasketRowIndex = -1;
            let dropdownMatches = [];
            let dropdownIndex = -1;

            function toEnglishDigits(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                    .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
            }

            function formatMoney(val) {
                return (parseFloat(val) || 0).toFixed(2);
            }

            function generateBarcode() {
                return '200' + String(Math.floor(1000 + Math.random() * 9000));
            }

            // ==================== 1. تحميل البيانات وتجهيز أمثلة ====================
            function loadAllStorage() {
                try { categories = JSON.parse(localStorage.getItem(CATEGORIES_KEY)) || []; } catch (e) { categories = []; }
                if (!categories.some(c => c.id === 'CAT-SPICES')) {
                    categories.unshift(
                        { id: 'CAT-SPICES', code: '088', name: 'عطارة وبهارات وتوابل', parentId: null, itemsCount: 4, isDeleted: false },
                        { id: 'CAT-COSM', code: '089', name: 'عناية وبوكسات وعروض', parentId: null, itemsCount: 3, isDeleted: false }
                    );
                    localStorage.setItem(CATEGORIES_KEY, JSON.stringify(categories));
                }

                try { products = JSON.parse(localStorage.getItem(PRODUCTS_KEY)) || []; } catch (e) { products = []; }

                const sampleItems = [
                    { id: 'RAW-PEPPER', catId: 'CAT-SPICES', catName: 'عطارة وبهارات وتوابل', name: 'فلفل أسود حصى', barcode: '8001', unit: 'كيلو', qty: 12, lastBuyPrice: 200, retailPrice: 250 },
                    { id: 'RAW-CUMIN', catId: 'CAT-SPICES', catName: 'عطارة وبهارات وتوابل', name: 'كمون بلدي', barcode: '8002', unit: 'كيلو', qty: 10, lastBuyPrice: 180, retailPrice: 230 },
                    { id: 'RAW-CORIANDER', catId: 'CAT-SPICES', catName: 'عطارة وبهارات وتوابل', name: 'كزبرة جافة', barcode: '8003', unit: 'كيلو', qty: 15, lastBuyPrice: 80, retailPrice: 110 },
                    { id: 'RAW-CHILI', catId: 'CAT-SPICES', catName: 'عطارة وبهارات وتوابل', name: 'شطة سوداني', barcode: '8004', unit: 'كيلو', qty: 8, lastBuyPrice: 140, retailPrice: 180 },
                    { id: 'BOX-ALOE', catId: 'CAT-COSM', catName: 'عناية وبوكسات وعروض', name: 'علبة جل صبار كوري', barcode: '9001', unit: 'قطعة', qty: 20, lastBuyPrice: 45, retailPrice: 65 },
                    { id: 'BOX-SHAMP', catId: 'CAT-COSM', catName: 'عناية وبوكسات وعروض', name: 'علبة شامبو أرجان', barcode: '9002', unit: 'قطعة', qty: 18, lastBuyPrice: 75, retailPrice: 110 },
                    { id: 'BOX-CREAM', catId: 'CAT-COSM', catName: 'عناية وبوكسات وعروض', name: 'علبة كريم ترطيب', barcode: '9003', unit: 'قطعة', qty: 25, lastBuyPrice: 50, retailPrice: 80 }
                ];

                let prodChanged = false;
                sampleItems.forEach(sp => {
                    if (!products.some(p => p.id === sp.id)) {
                        products.unshift({
                            id: sp.id, itemType: 'صنف مخزني', categoryId: sp.catId, categoryName: sp.catName,
                            name: sp.name, description: '', barcode: sp.barcode, unit: sp.unit, qty: sp.qty,
                            supplier: 'مورد رئيسي', lastBuyPrice: sp.lastBuyPrice, avgCost: sp.lastBuyPrice,
                            lastBuyDate: '2026/10/19', retailPrice: sp.retailPrice, minStock: 1, maxStock: 50,
                            isPinned: false, isDisabled: false, isDeleted: false
                        });
                        prodChanged = true;
                    }
                });
                if (prodChanged) localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));

                try { movementsSummary = JSON.parse(localStorage.getItem(MOVEMENTS_KEY)) || []; } catch (e) { movementsSummary = []; }
                try { mfgBatches = JSON.parse(localStorage.getItem(MFG_BATCHES_KEY)) || []; } catch (e) { mfgBatches = []; }

                if (mfgBatches.length === 0) {
                    mfgBatches = [
                        {
                            id: 'BATCH-DEMO-1',
                            productId: 'PRD-MFG-1',
                            productName: 'السبع بهارات الملكية',
                            categoryId: 'CAT-SPICES',
                            categoryName: 'عطارة وبهارات وتوابل',
                            finalUnit: 'كيلو',
                            barcode: '2007711',
                            deductionMode: 'immediate',
                            totalWeightKg: 1,
                            totalBatchCost: 150,
                            costPerKg: 150,
                            profitPercent: 25,
                            sellingPrice: 187.50,
                            components: [
                                { productId: 'RAW-PEPPER', name: 'فلفل أسود حصى', availableQty: 12, baseUnit: 'كيلو', costPerKg: 200, inputQty: 250, inputUnit: 'gram' },
                                { productId: 'RAW-CUMIN', name: 'كمون بلدي', availableQty: 10, baseUnit: 'كيلو', costPerKg: 180, inputQty: 250, inputUnit: 'gram' },
                                { productId: 'RAW-CORIANDER', name: 'كزبرة جافة', availableQty: 15, baseUnit: 'كيلو', costPerKg: 80, inputQty: 250, inputUnit: 'gram' },
                                { productId: 'RAW-CHILI', name: 'شطة سوداني', availableQty: 8, baseUnit: 'كيلو', costPerKg: 140, inputQty: 250, inputUnit: 'gram' }
                            ]
                        },
                        {
                            id: 'BATCH-DEMO-2',
                            productId: 'PRD-MFG-2',
                            productName: 'عرض بوكس التقوى المتكامل',
                            categoryId: 'CAT-COSM',
                            categoryName: 'عناية وبوكسات وعروض',
                            finalUnit: 'بوكس',
                            barcode: '2008822',
                            deductionMode: 'on_sale',
                            totalWeightKg: 3,
                            totalBatchCost: 170,
                            costPerKg: 170,
                            profitPercent: 20,
                            sellingPrice: 204.00,
                            components: [
                                { productId: 'BOX-ALOE', name: 'علبة جل صبار كوري', availableQty: 20, baseUnit: 'قطعة', costPerKg: 45, inputQty: 1, inputUnit: 'kg' },
                                { productId: 'BOX-SHAMP', name: 'علبة شامبو أرجان', availableQty: 18, baseUnit: 'قطعة', costPerKg: 75, inputQty: 1, inputUnit: 'kg' },
                                { productId: 'BOX-CREAM', name: 'علبة كريم ترطيب', availableQty: 25, baseUnit: 'قطعة', costPerKg: 50, inputQty: 1, inputUnit: 'kg' }
                            ]
                        }
                    ];
                    localStorage.setItem(MFG_BATCHES_KEY, JSON.stringify(mfgBatches));
                }

                populateCategorySelects();
            }

            function saveAllToStorage() {
                localStorage.setItem(PRODUCTS_KEY, JSON.stringify(products));
                localStorage.setItem(MOVEMENTS_KEY, JSON.stringify(movementsSummary));
                localStorage.setItem(MFG_BATCHES_KEY, JSON.stringify(mfgBatches));
            }

            function populateCategorySelects() {
                const activeCats = categories.filter(c => !c.isDeleted);
                const html = activeCats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                document.getElementById('mfgCategorySelect').innerHTML = html;
                document.getElementById('formCategorySelect').innerHTML = html;
            }

            // ==================== 2. البحث عن المكونات (لا يظهر أي شيء إلا بعد كتابة أول حرف) ====================
            const rawSearchInput = document.getElementById('rawMaterialSearchInput');
            const rawDropdown = document.getElementById('rawMaterialDropdown');

            function renderRawDropdown(query = '') {
                const cleanQ = toEnglishDigits(query).trim().toLowerCase();

                // عدم إظهار أي منتجات طالما الحقل فارغ ولم يكتب المستخدم أول حرف
                if (!cleanQ) {
                    dropdownMatches = [];
                    rawDropdown.style.display = 'none';
                    return;
                }

                dropdownMatches = products.filter(p => !p.isDeleted && !p.isDisabled).filter(p => {
                    return (p.name || '').toLowerCase().includes(cleanQ) || String(p.barcode || '').includes(cleanQ);
                });

                if (dropdownMatches.length === 0) {
                    rawDropdown.innerHTML = `<div style="padding:10px; text-align:center; color:var(--ns-muted);">لا توجد أصناف مطابقة</div>`;
                    rawDropdown.style.display = 'block';
                    return;
                }

                rawDropdown.innerHTML = dropdownMatches.map((item, idx) => `
                    <div class="raw-dropdown-item ${idx === dropdownIndex ? 'active' : ''}" data-index="${idx}">
                        <div>
                            <strong style="color:var(--ns-navy);">${item.name}</strong>
                            <small style="color:var(--ns-muted); margin-right:6px;">(${item.unit} | باركود: ${item.barcode})</small>
                        </div>
                        <span style="background:var(--ns-green-soft); color:var(--ns-green-hover); padding:2px 8px; border-radius:999px; font-size:11.5px;">
                            المتاح: ${item.qty} | شراء: ${item.lastBuyPrice} ج.م
                        </span>
                    </div>
                `).join('');

                rawDropdown.style.display = 'block';

                rawDropdown.querySelectorAll('.raw-dropdown-item').forEach(el => {
                    el.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        const i = parseInt(this.getAttribute('data-index'), 10);
                        addComponentToBasket(dropdownMatches[i]);
                    });
                });
            }

            function addComponentToBasket(product) {
                if (!product) return;
                const existingIdx = selectedComponents.findIndex(c => c.productId === product.id);
                if (existingIdx !== -1) {
                    activeBasketRowIndex = existingIdx;
                    renderComponentsTable();
                    rawDropdown.style.display = 'none';
                    rawSearchInput.value = '';
                    return;
                }

                const isPiece = product.unit === 'قطعة' || product.unit === 'علبة';
                selectedComponents.push({
                    productId: product.id,
                    name: product.name,
                    availableQty: parseFloat(product.qty) || 0,
                    baseUnit: product.unit || 'كيلو',
                    costPerKg: parseFloat(product.lastBuyPrice) || 0,
                    inputQty: isPiece ? 1 : 250,
                    inputUnit: isPiece ? 'kg' : 'gram'
                });

                activeBasketRowIndex = selectedComponents.length - 1;
                rawDropdown.style.display = 'none';
                rawSearchInput.value = '';
                renderComponentsTable();
                recalculateCostAndPrice(true);
            }

            function getComponentNormalizedQty(comp) {
                const q = parseFloat(comp.inputQty) || 0;
                return comp.inputUnit === 'gram' ? (q / 1000) : q;
            }

            // ==================== 3. رسم سلة المكونات الواسعة + فتح نافذة تعديل الصنف ====================
            function renderComponentsTable() {
                const tbody = document.getElementById('componentsTableBody');
                if (selectedComponents.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" style="text-align:center; padding:55px 15px; color:var(--ns-muted);">
                                <i class="fa-solid fa-basket-shopping" style="font-size:34px; color:#cbd5e1; margin-bottom:8px; display:block;"></i>
                                <div style="font-weight:800; font-size:13.5px;">سلة التصنيع والتجميع فارغة حالياً</div>
                                <small>اكتب أول حرف في خانة البحث فوق (F3) لإضافة الأصناف ويمكنك الضغط على أي صنف لتعديل سعره واسمه</small>
                            </td>
                        </tr>
                    `;
                    activeBasketRowIndex = -1;
                    return;
                }

                if (activeBasketRowIndex < 0 || activeBasketRowIndex >= selectedComponents.length) {
                    activeBasketRowIndex = 0;
                }

                const totalNormWeight = selectedComponents.reduce((sum, c) => sum + getComponentNormalizedQty(c), 0);

                tbody.innerHTML = selectedComponents.map((comp, idx) => {
                    const normQty = getComponentNormalizedQty(comp);
                    const ratioPercent = totalNormWeight > 0 ? ((normQty / totalNormWeight) * 100) : 0;
                    const rowCost = normQty * comp.costPerKg;
                    const isActive = idx === activeBasketRowIndex;

                    return `
                        <tr data-index="${idx}" class="${isActive ? 'active-basket-row' : ''}">
                            <td style="text-align:center; color:var(--ns-muted);">${idx + 1}</td>
                            <td>
                                <a href="javascript:void(0)" class="comp-name-btn" data-index="${idx}" title="اضغط لتعديل بيانات الصنف نفسه وسعره في المخزن">
                                    <span>${comp.name}</span>
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </td>
                            <td style="text-align:center;">${comp.availableQty} ${comp.baseUnit}</td>
                            <td style="text-align:center;">${formatMoney(comp.costPerKg)} ج.م</td>
                            <td style="text-align:center;">
                                <div class="qty-inline-group">
                                    <input type="text" class="qty-input-cell comp-qty-input" data-index="${idx}" value="${comp.inputQty}">
                                    <select class="unit-select-cell comp-unit-select" data-index="${idx}">
                                        <option value="gram" ${comp.inputUnit === 'gram' ? 'selected' : ''}>جرام</option>
                                        <option value="kg" ${comp.inputUnit === 'kg' ? 'selected' : ''}>${comp.baseUnit === 'كيلو' ? 'كيلو' : 'قطعة/وحدة'}</option>
                                    </select>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span class="ratio-pill">${ratioPercent.toFixed(1)}%</span>
                            </td>
                            <td style="text-align:center; font-weight:900; color:var(--ns-danger);">${formatMoney(rowCost)} ج.م</td>
                            <td style="text-align:center;">
                                <button type="button" class="btn-remove-comp" data-index="${idx}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                tbody.querySelectorAll('tr[data-index]').forEach(tr => {
                    tr.addEventListener('click', function (e) {
                        if (e.target.closest('.qty-inline-group') || e.target.closest('.btn-remove-comp')) return;
                        activeBasketRowIndex = parseInt(this.getAttribute('data-index'), 10);
                        highlightBasketRow();
                    });
                });

                tbody.querySelectorAll('.comp-name-btn').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const idx = parseInt(this.getAttribute('data-index'), 10);
                        activeBasketRowIndex = idx;
                        highlightBasketRow();
                        openProductEditModalByProductId(selectedComponents[idx].productId);
                    });
                });

                tbody.querySelectorAll('.comp-qty-input').forEach(inp => {
                    inp.addEventListener('input', function () {
                        const idx = parseInt(this.getAttribute('data-index'), 10);
                        this.value = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
                        selectedComponents[idx].inputQty = parseFloat(this.value) || 0;
                        renderComponentsTable();
                        recalculateCostAndPrice(true);
                    });
                });

                tbody.querySelectorAll('.comp-unit-select').forEach(sel => {
                    sel.addEventListener('change', function () {
                        const idx = parseInt(this.getAttribute('data-index'), 10);
                        selectedComponents[idx].inputUnit = this.value;
                        if (this.value === 'kg' && selectedComponents[idx].inputQty >= 10) {
                            selectedComponents[idx].inputQty = selectedComponents[idx].inputQty / 1000;
                        } else if (this.value === 'gram' && selectedComponents[idx].inputQty <= 5) {
                            selectedComponents[idx].inputQty = selectedComponents[idx].inputQty * 1000;
                        }
                        renderComponentsTable();
                        recalculateCostAndPrice(true);
                    });
                });

                tbody.querySelectorAll('.btn-remove-comp').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(this.getAttribute('data-index'), 10);
                        selectedComponents.splice(idx, 1);
                        renderComponentsTable();
                        recalculateCostAndPrice(true);
                    });
                });
            }

            function highlightBasketRow() {
                document.querySelectorAll('#componentsTableBody tr[data-index]').forEach((tr, idx) => {
                    if (idx === activeBasketRowIndex) {
                        tr.classList.add('active-basket-row');
                        tr.scrollIntoView({ block: 'nearest' });
                    } else {
                        tr.classList.remove('active-basket-row');
                    }
                });
            }

            // ==================== 4. فتح نافذة "صنف مخزن" الأصلية لتعديل المنتج نفسه ====================
            function openProductEditModalByProductId(productId) {
                const item = products.find(p => p.id === productId);
                if (!item) return;

                document.getElementById('itemModalTitle').innerHTML = `<i class="fa-solid fa-pen-to-square"></i> صنف مخزن — تعديل (${item.name})`;
                document.getElementById('formItemId').value = item.id;

                const typeRadio = document.querySelector(`input[name="itemTypeRadio"][value="${item.itemType || 'صنف مخزني'}"]`);
                if (typeRadio) typeRadio.checked = true;

                document.getElementById('formCategorySelect').value = item.categoryId || '';
                document.getElementById('formItemName').value = item.name || '';
                document.getElementById('formItemDesc').value = item.description || '';
                document.getElementById('formMainUnit').value = item.unit || 'كيلو';
                document.getElementById('formBarcode').value = item.barcode || '';

                document.getElementById('formQtyReadonly').value = item.qty || 0;
                document.getElementById('formLastBuyPrice').value = item.lastBuyPrice || 0;
                document.getElementById('formLastBuyDate').value = item.lastBuyDate || '2026/10/19';
                document.getElementById('formMinStock').value = item.minStock || 0;
                document.getElementById('formMaxStock').value = item.maxStock || 0;
                document.getElementById('formRetailPrice').value = item.retailPrice || 0;

                document.getElementById('formOptDisabled').checked = Boolean(item.isDisabled);
                document.getElementById('formOptCommission').checked = Boolean(item.hasCommission);
                document.getElementById('formOptAllowPriceEdit').checked = Boolean(item.allowPriceEdit);
                document.getElementById('formOptPinned').checked = Boolean(item.isPinned);

                document.getElementById('itemFormModal').classList.add('open');
                setTimeout(() => document.getElementById('formItemName').focus(), 60);
            }

            function saveEditedProductFromModal() {
                const id = document.getElementById('formItemId').value;
                const pIdx = products.findIndex(p => p.id === id);
                if (pIdx === -1) return;

                const newName = toEnglishDigits(document.getElementById('formItemName').value).trim();
                const newBuyPrice = parseFloat(toEnglishDigits(document.getElementById('formLastBuyPrice').value)) || 0;
                const newRetailPrice = parseFloat(toEnglishDigits(document.getElementById('formRetailPrice').value)) || 0;
                const newUnit = document.getElementById('formMainUnit').value;
                const newCatId = document.getElementById('formCategorySelect').value;
                const catObj = categories.find(c => String(c.id) === String(newCatId));

                if (!newName) return;

                products[pIdx] = {
                    ...products[pIdx],
                    itemType: document.querySelector('input[name="itemTypeRadio"]:checked')?.value || 'صنف مخزني',
                    categoryId: newCatId,
                    categoryName: catObj ? catObj.name : products[pIdx].categoryName,
                    name: newName,
                    description: toEnglishDigits(document.getElementById('formItemDesc').value).trim(),
                    unit: newUnit,
                    barcode: toEnglishDigits(document.getElementById('formBarcode').value).trim(),
                    lastBuyPrice: newBuyPrice,
                    avgCost: newBuyPrice,
                    retailPrice: newRetailPrice,
                    minStock: parseFloat(toEnglishDigits(document.getElementById('formMinStock').value)) || 0,
                    maxStock: parseFloat(toEnglishDigits(document.getElementById('formMaxStock').value)) || 0,
                    isDisabled: document.getElementById('formOptDisabled').checked,
                    hasCommission: document.getElementById('formOptCommission').checked,
                    allowPriceEdit: document.getElementById('formOptAllowPriceEdit').checked,
                    isPinned: document.getElementById('formOptPinned').checked
                };

                selectedComponents.forEach(c => {
                    if (c.productId === id) {
                        c.name = newName;
                        c.costPerKg = newBuyPrice;
                        c.baseUnit = newUnit;
                    }
                });

                mfgBatches.forEach(b => {
                    if (b.productId === id) {
                        b.productName = newName;
                        b.sellingPrice = newRetailPrice;
                    }
                    (b.components || []).forEach(c => {
                        if (c.productId === id) {
                            c.name = newName;
                            c.costPerKg = newBuyPrice;
                        }
                    });
                });

                saveAllToStorage();
                document.getElementById('itemFormModal').classList.remove('open');
                renderComponentsTable();
                recalculateCostAndPrice(true);
                renderBatchesList();

                Swal.fire({
                    toast: true,
                    position: 'bottom-start',
                    icon: 'success',
                    title: `تم تحديث بيانات وسعر "${newName}" في المخزن والسلة`,
                    showConfirmButton: false,
                    timer: 2200
                });
            }

            // ==================== 5. حساب التكلفة ونسبة الربح (%) وسعر البيع ====================
            const profitPercentInput = document.getElementById('mfgProfitPercentInput');
            const sellingPriceInput = document.getElementById('mfgSellingPriceInput');

            function getRecipeTotals() {
                let totalWeightKg = 0;
                let totalBatchCost = 0;
                const finalUnit = document.getElementById('mfgFinalUnit').value;

                selectedComponents.forEach(comp => {
                    const wKg = getComponentNormalizedQty(comp);
                    totalWeightKg += wKg;
                    totalBatchCost += (wKg * comp.costPerKg);
                });

                const costPerUnit = (finalUnit === 'بوكس' || finalUnit === 'علبة' || finalUnit === 'قطعة')
                    ? totalBatchCost
                    : (totalWeightKg > 0 ? (totalBatchCost / totalWeightKg) : 0);

                return { totalWeightKg, totalBatchCost, costPerKg: costPerUnit };
            }

            function recalculateCostAndPrice(applyPercentToPrice = true) {
                const { totalWeightKg, totalBatchCost, costPerKg } = getRecipeTotals();
                const finalUnit = document.getElementById('mfgFinalUnit').value;

                document.getElementById('lblTotalProducedWeight').textContent = finalUnit === 'كيلو'
                    ? `${Number(totalWeightKg.toFixed(3))} كجم`
                    : `${selectedComponents.length} أصناف بالعرض`;
                document.getElementById('lblTotalBatchCost').textContent = `${formatMoney(totalBatchCost)} ج.م`;
                document.getElementById('lblCostPerKg').textContent = `${formatMoney(costPerKg)} ج.م`;

                if (applyPercentToPrice) {
                    const pct = parseFloat(toEnglishDigits(profitPercentInput.value)) || 0;
                    const calculatedPrice = costPerKg + (costPerKg * (pct / 100));
                    sellingPriceInput.value = formatMoney(calculatedPrice);
                } else {
                    const manualPrice = parseFloat(toEnglishDigits(sellingPriceInput.value)) || 0;
                    const newPct = costPerKg > 0 ? (((manualPrice - costPerKg) / costPerKg) * 100) : 0;
                    profitPercentInput.value = Number(newPct.toFixed(1));
                }

                const finalSellPrice = parseFloat(toEnglishDigits(sellingPriceInput.value)) || 0;
                const netProfit = finalSellPrice - costPerKg;
                document.getElementById('lblNetProfitPerKg').textContent = `مكسب: ${formatMoney(netProfit)} ج.م`;
            }

            profitPercentInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value).replace(/[^0-9.-]/g, '');
                recalculateCostAndPrice(true);
            });

            sellingPriceInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value).replace(/[^0-9.]/g, '');
                recalculateCostAndPrice(false);
            });

            document.getElementById('mfgFinalUnit').addEventListener('change', () => recalculateCostAndPrice(true));

            // ==================== 6. حفظ التصنيع (مفتوح للتعديل في أي وقت) ====================
            function saveAndManufactureBatch() {
                const productName = toEnglishDigits(document.getElementById('mfgProductName').value).trim();
                const categoryId = document.getElementById('mfgCategorySelect').value;
                const finalUnit = document.getElementById('mfgFinalUnit').value;
                const barcode = toEnglishDigits(document.getElementById('mfgBarcode').value).trim() || generateBarcode();
                const deductionMode = document.querySelector('input[name="deductionModeRadio"]:checked')?.value || 'immediate';
                const editingBatchId = document.getElementById('editingBatchId').value;

                if (!productName) {
                    Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى كتابة اسم الصنف المصنع أو البوكس', confirmButtonColor: '#119744' });
                    return;
                }
                if (selectedComponents.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى إضافة مكونات للسلة أولاً', confirmButtonColor: '#119744' });
                    return;
                }

                const { totalWeightKg, totalBatchCost, costPerKg } = getRecipeTotals();
                const sellingPrice = parseFloat(toEnglishDigits(sellingPriceInput.value)) || 0;

                if (editingBatchId) {
                    const oldBatch = mfgBatches.find(b => b.id === editingBatchId);
                    if (oldBatch && oldBatch.deductionMode === 'immediate') {
                        reverseBatchStockImpact(oldBatch);
                    }
                }

                if (deductionMode === 'immediate') {
                    selectedComponents.forEach(comp => {
                        const deductQty = getComponentNormalizedQty(comp);
                        const pIdx = products.findIndex(p => p.id === comp.productId);
                        if (pIdx !== -1) {
                            products[pIdx].qty = Number(((parseFloat(products[pIdx].qty) || 0) - deductQty).toFixed(3));
                        }
                    });
                }

                const catObj = categories.find(c => String(c.id) === String(categoryId));
                let mfgProduct = products.find(p => p.name === productName || (p.barcode === barcode && barcode !== ''));
                const addedFinalQty = deductionMode === 'immediate' ? Number(totalWeightKg.toFixed(3)) : 0;

                if (mfgProduct) {
                    if (deductionMode === 'immediate') {
                        mfgProduct.qty = Number(((parseFloat(mfgProduct.qty) || 0) + addedFinalQty).toFixed(3));
                    }
                    mfgProduct.lastBuyPrice = Number(costPerKg.toFixed(2));
                    mfgProduct.avgCost = Number(costPerKg.toFixed(2));
                    mfgProduct.retailPrice = sellingPrice;
                    mfgProduct.deductionMode = deductionMode;
                    mfgProduct.recipeComponents = JSON.parse(JSON.stringify(selectedComponents));
                } else {
                    mfgProduct = {
                        id: 'PRD-MFG-' + Date.now(),
                        itemType: 'صنف مجمع',
                        categoryId: categoryId,
                        categoryName: catObj ? catObj.name : 'عام',
                        name: productName,
                        description: deductionMode === 'immediate' ? 'خلطة مصنعة جاهزة' : 'عرض تجميعي (يخصم عند البيع)',
                        barcode: barcode,
                        unit: finalUnit,
                        qty: addedFinalQty,
                        supplier: 'تصنيع داخلي',
                        lastBuyPrice: Number(costPerKg.toFixed(2)),
                        avgCost: Number(costPerKg.toFixed(2)),
                        lastBuyDate: '2026/10/19',
                        retailPrice: sellingPrice,
                        deductionMode: deductionMode,
                        recipeComponents: JSON.parse(JSON.stringify(selectedComponents)),
                        isPinned: true,
                        isDisabled: false,
                        isDeleted: false
                    };
                    products.unshift(mfgProduct);
                }

                const batchPayload = {
                    id: editingBatchId || ('BATCH-' + Date.now()),
                    productId: mfgProduct.id,
                    productName: productName,
                    categoryId: categoryId,
                    categoryName: catObj ? catObj.name : 'عام',
                    finalUnit: finalUnit,
                    barcode: barcode,
                    deductionMode: deductionMode,
                    totalWeightKg: Number(totalWeightKg.toFixed(3)),
                    totalBatchCost: Number(totalBatchCost.toFixed(2)),
                    costPerKg: Number(costPerKg.toFixed(2)),
                    profitPercent: parseFloat(toEnglishDigits(profitPercentInput.value)) || 0,
                    sellingPrice: sellingPrice,
                    components: JSON.parse(JSON.stringify(selectedComponents))
                };

                if (editingBatchId) {
                    const bIdx = mfgBatches.findIndex(b => b.id === editingBatchId);
                    if (bIdx !== -1) mfgBatches[bIdx] = batchPayload;
                } else {
                    mfgBatches.unshift(batchPayload);
                }

                saveAllToStorage();
                resetBuilderForm();
                renderBatchesList();

                Swal.fire({
                    toast: true,
                    position: 'bottom-start',
                    icon: 'success',
                    title: deductionMode === 'immediate'
                        ? `تم تصنيع "${productName}" وخصم المكونات من المخزن فوراً`
                        : `تم حفظ العرض "${productName}" (سيتم خصم مكوناته تلقائياً عند البيع)`,
                    showConfirmButton: false,
                    timer: 2800
                });
            }

            function reverseBatchStockImpact(oldBatch) {
                (oldBatch.components || []).forEach(comp => {
                    const wKg = getComponentNormalizedQty(comp);
                    const pIdx = products.findIndex(p => p.id === comp.productId);
                    if (pIdx !== -1) {
                        products[pIdx].qty = Number(((parseFloat(products[pIdx].qty) || 0) + wKg).toFixed(3));
                    }
                });
                const mfgIdx = products.findIndex(p => p.id === oldBatch.productId || p.name === oldBatch.productName);
                if (mfgIdx !== -1) {
                    products[mfgIdx].qty = Number(((parseFloat(products[mfgIdx].qty) || 0) - (parseFloat(oldBatch.totalWeightKg) || 0)).toFixed(3));
                }
            }

            function resetBuilderForm() {
                document.getElementById('editingBatchId').value = '';
                document.getElementById('builderModeBadge').textContent = 'عملية جديدة';
                document.getElementById('builderModeBadge').classList.remove('edit');
                document.getElementById('btnSaveManufactureText').textContent = 'حفظ وتأكيد التصنيع (F2)';

                document.getElementById('mfgProductName').value = '';
                document.getElementById('mfgBarcode').value = generateBarcode();
                document.querySelector('input[name="deductionModeRadio"][value="immediate"]').checked = true;
                profitPercentInput.value = '25';
                sellingPriceInput.value = '0.00';
                selectedComponents = [];
                activeBasketRowIndex = -1;
                renderComponentsTable();
                recalculateCostAndPrice(true);
            }

            // ==================== 7. عرض البطاقات بالكبسولات الدائرية + أزرار التعديل والحذف من الخارج ====================
            function renderBatchesList() {
                const container = document.getElementById('batchesListContainer');
                const q = toEnglishDigits(document.getElementById('searchBatchesInput').value).trim().toLowerCase();
                const modeFilter = document.getElementById('filterBatchesMode').value;

                const list = mfgBatches.filter(b => {
                    if (modeFilter !== 'all' && b.deductionMode !== modeFilter) return false;
                    if (q && !(b.productName || '').toLowerCase().includes(q)) return false;
                    return true;
                });

                if (list.length === 0) {
                    container.innerHTML = `<div style="text-align:center; padding:40px 10px; color:var(--ns-muted);">لا توجد عناصر مطابقة</div>`;
                    return;
                }

                const editingId = document.getElementById('editingBatchId').value;

                container.innerHTML = list.map(b => {
                    const isImmediate = b.deductionMode !== 'on_sale';

                    const chipsHtml = (b.components || []).map(c => {
                        const unitLabel = c.inputUnit === 'gram' ? 'جم' : (c.baseUnit === 'كيلو' ? 'كجم' : 'قطعة');
                        return `
                            <span class="ingredient-pill-chip" data-prod-id="${c.productId}" title="اضغط لتعديل بيانات هذا الصنف">
                                <span>${c.name}</span>
                                <span class="chip-qty">${c.inputQty} ${unitLabel}</span>
                            </span>
                        `;
                    }).join('');

                    return `
                        <div class="batch-card ${b.id === editingId ? 'selected' : ''}" data-id="${b.id}">
                            <div class="batch-card-top">
                                <span class="batch-title">
                                    <span>${b.productName}</span>
                                </span>
                                <span class="deduct-type-badge ${isImmediate ? 'immediate' : 'on-sale'}">
                                    ${isImmediate ? '⚡ خصم فوري' : '🎁 يخصم عند البيع'}
                                </span>
                            </div>

                            <div class="ingredient-chips-wrap">
                                ${chipsHtml}
                            </div>

                            <div class="batch-meta-footer">
                                <span>تكلفة: <b style="color:var(--ns-danger)">${formatMoney(b.costPerKg)} ج</b> | بيع: <b style="color:var(--ns-green)">${formatMoney(b.sellingPrice)} ج</b></span>
                                <div class="batch-card-actions">
                                    <button type="button" class="btn-card-edit" data-edit-id="${b.id}" title="تعديل هذه الخلطة">
                                        <i class="fa-solid fa-pen-to-square"></i> تعديل
                                    </button>
                                    <button type="button" class="btn-card-delete" data-delete-id="${b.id}" title="حذف هذه الخلطة">
                                        <i class="fa-solid fa-trash-can"></i> حذف
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                container.querySelectorAll('.batch-card').forEach(card => {
                    card.addEventListener('click', function (e) {
                        const editBtn = e.target.closest('.btn-card-edit');
                        if (editBtn) {
                            e.stopPropagation();
                            loadBatchForEdit(editBtn.getAttribute('data-edit-id'));
                            return;
                        }

                        const delBtn = e.target.closest('.btn-card-delete');
                        if (delBtn) {
                            e.stopPropagation();
                            deleteBatchFromList(delBtn.getAttribute('data-delete-id'));
                            return;
                        }

                        const chip = e.target.closest('.ingredient-pill-chip');
                        if (chip) {
                            e.stopPropagation();
                            openProductEditModalByProductId(chip.getAttribute('data-prod-id'));
                            return;
                        }

                        loadBatchForEdit(this.getAttribute('data-id'));
                    });

                    card.addEventListener('dblclick', function () {
                        const batch = mfgBatches.find(b => b.id === this.getAttribute('data-id'));
                        if (batch && batch.productId) {
                            openProductEditModalByProductId(batch.productId);
                        }
                    });
                });
            }

            function loadBatchForEdit(batchId) {
                const batch = mfgBatches.find(b => b.id === batchId);
                if (!batch) return;

                document.getElementById('editingBatchId').value = batch.id;
                document.getElementById('builderModeBadge').textContent = `تعديل (${batch.productName})`;
                document.getElementById('builderModeBadge').classList.add('edit');
                document.getElementById('btnSaveManufactureText').textContent = 'حفظ التعديلات (F2)';

                document.getElementById('mfgProductName').value = batch.productName;
                document.getElementById('mfgCategorySelect').value = batch.categoryId;
                document.getElementById('mfgFinalUnit').value = batch.finalUnit || 'كيلو';
                document.getElementById('mfgBarcode').value = batch.barcode;

                const modeRadio = document.querySelector(`input[name="deductionModeRadio"][value="${batch.deductionMode || 'immediate'}"]`);
                if (modeRadio) modeRadio.checked = true;

                profitPercentInput.value = batch.profitPercent;
                sellingPriceInput.value = formatMoney(batch.sellingPrice);

                selectedComponents = JSON.parse(JSON.stringify(batch.components || []));
                activeBasketRowIndex = 0;
                renderComponentsTable();
                recalculateCostAndPrice(false);
                renderBatchesList();
            }

            function deleteBatchFromList(batchId) {
                const batch = mfgBatches.find(b => b.id === batchId);
                if (!batch) return;

                Swal.fire({
                    title: 'حذف الخلطة / العرض؟',
                    html: `هل أنت متأكد من حذف <b>"${batch.productName}"</b> نهائياً؟`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then(res => {
                    if (res.isConfirmed) {
                        if (batch.deductionMode === 'immediate') {
                            reverseBatchStockImpact(batch);
                        }
                        mfgBatches = mfgBatches.filter(b => b.id !== batchId);
                        if (document.getElementById('editingBatchId').value === batchId) {
                            resetBuilderForm();
                        }
                        saveAllToStorage();
                        renderBatchesList();

                        Swal.fire({
                            toast: true,
                            position: 'bottom-start',
                            icon: 'success',
                            title: `تم حذف "${batch.productName}" بنجاح`,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }

            // ==================== 8. الأحداث واختصارات الكيبورد ====================
            // عند التركيز لا يفتح القائمة إلا إذا كان هناك نص مكتوب بالفعل
            rawSearchInput.addEventListener('focus', () => {
                if (rawSearchInput.value.trim().length > 0) {
                    renderRawDropdown(rawSearchInput.value);
                }
            });

            rawSearchInput.addEventListener('input', function () {
                this.value = toEnglishDigits(this.value);
                dropdownIndex = 0;
                renderRawDropdown(this.value);
            });

            rawSearchInput.addEventListener('keydown', (e) => {
                if (rawDropdown.style.display !== 'block' || dropdownMatches.length === 0) return;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    dropdownIndex = (dropdownIndex + 1) % dropdownMatches.length;
                    renderRawDropdown(rawSearchInput.value);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    dropdownIndex = (dropdownIndex - 1 + dropdownMatches.length) % dropdownMatches.length;
                    renderRawDropdown(rawSearchInput.value);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const chosen = dropdownMatches[dropdownIndex >= 0 ? dropdownIndex : 0];
                    if (chosen) addComponentToBasket(chosen);
                }
            });

            document.addEventListener('click', (e) => {
                if (!e.target.closest('.raw-search-input-wrap')) rawDropdown.style.display = 'none';
            });

            document.querySelectorAll('.ns-tab-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const tabId = this.getAttribute('data-tab');
                    document.querySelectorAll('.ns-tab-btn').forEach(b => b.classList.toggle('active', b === this));
                    document.querySelectorAll('.ns-tab-pane').forEach(p => p.classList.toggle('active', p.id === tabId));
                });
            });

            document.getElementById('btnCloseItemModal').addEventListener('click', () => document.getElementById('itemFormModal').classList.remove('open'));
            document.getElementById('btnCancelProductModal').addEventListener('click', () => document.getElementById('itemFormModal').classList.remove('open'));
            document.getElementById('btnSaveProductModal').addEventListener('click', saveEditedProductFromModal);

            document.getElementById('btnSaveManufacture').addEventListener('click', saveAndManufactureBatch);
            document.getElementById('btnResetBuilder').addEventListener('click', resetBuilderForm);
            document.getElementById('searchBatchesInput').addEventListener('input', renderBatchesList);
            document.getElementById('filterBatchesMode').addEventListener('change', renderBatchesList);

            function handleExit() {
                if (window.history.length > 1) window.history.back();
                else window.location.href = '/inventory/items';
            }
            document.getElementById('btnExitPage').addEventListener('click', handleExit);

            document.addEventListener('keydown', (e) => {
                if (Swal.isVisible()) return;
                const modalOpen = document.getElementById('itemFormModal').classList.contains('open');

                if (e.key === 'Escape') {
                    if (modalOpen) {
                        document.getElementById('itemFormModal').classList.remove('open');
                        return;
                    }
                    if (rawDropdown.style.display === 'block') {
                        rawDropdown.style.display = 'none';
                        return;
                    }
                    e.preventDefault();
                    handleExit();
                    return;
                }

                if (modalOpen) return;

                if (e.key === 'F3') {
                    e.preventDefault();
                    rawSearchInput.focus();
                    rawSearchInput.select();
                    return;
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    saveAndManufactureBatch();
                    return;
                }

                if (rawDropdown.style.display !== 'block' && selectedComponents.length > 0 && document.activeElement.tagName !== 'INPUT') {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        activeBasketRowIndex = Math.min(selectedComponents.length - 1, activeBasketRowIndex + 1);
                        highlightBasketRow();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        activeBasketRowIndex = Math.max(0, activeBasketRowIndex - 1);
                        highlightBasketRow();
                    } else if (e.key === 'Enter' && activeBasketRowIndex >= 0) {
                        e.preventDefault();
                        openProductEditModalByProductId(selectedComponents[activeBasketRowIndex].productId);
                    }
                }
            });

            function init() {
                loadAllStorage();
                resetBuilderForm();
                renderBatchesList();

                // دعم فتح التعديل المباشر عند القدوم من صفحة الأرشيف ?batch=ID
                const params = new URLSearchParams(window.location.search);
                const batchIdParam = params.get('batch');
                if (batchIdParam) {
                    loadBatchForEdit(batchIdParam);
                }
            }

            init();
        })();
    </script>
</body>
</html>
