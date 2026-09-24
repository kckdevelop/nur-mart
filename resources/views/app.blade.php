<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NURMART - Sistem Manajemen Toko Kelontong & Kasir POS</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Canvas Confetti for Checkout celebrations -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

    <style>
        :root {
            --primary: #059669;
            --primary-light: #10b981;
            --primary-dark: #047857;
            --primary-bg: #ecfdf5;
            --accent: #f59e0b;
            --accent-light: #fef3c7;
            --danger: #ef4444;
            --danger-bg: #fee2e2;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-border: #334155;
            --light-bg: #f8fafc;
            --light-card: #ffffff;
            --light-border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.08), 0 2px 4px -2px rgba(0,0,0,0.04);
            --shadow-lg: 0 10px 25px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.05);
            --shadow-glow: 0 0 20px rgba(16, 185, 129, 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Top Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--light-border);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), #0284c7);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
        }

        .brand-text h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, #047857, #0284c7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-text p {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .nav-center {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 4px;
            border-radius: var(--radius-lg);
        }

        .nav-btn {
            background: transparent;
            border: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-btn:hover {
            color: var(--text-main);
        }

        .nav-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .user-status {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .role-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-pemilik {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .role-kasir {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .btn-switch-role {
            background: #f1f5f9;
            border: 1px solid var(--light-border);
            padding: 6px 12px;
            border-radius: var(--radius-md);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-switch-role:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        /* Main Container */
        .container {
            max-width: 1380px;
            margin: 0 auto;
            padding: 24px;
            width: 100%;
            flex: 1;
        }

        /* Tab Content Control */
        .tab-panel {
            display: none;
            animation: fadeIn 0.25s ease-out;
        }

        .tab-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Metric Cards */
        .grid-metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .metric-card {
            background: white;
            padding: 20px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--light-border);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .metric-card.green::before { background: var(--primary); }
        .metric-card.blue::before { background: #0284c7; }
        .metric-card.amber::before { background: var(--accent); }
        .metric-card.red::before { background: var(--danger); }

        .metric-info h4 {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-info .value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .metric-card.green .metric-icon { background: var(--primary-bg); color: var(--primary); }
        .metric-card.blue .metric-icon { background: #e0f2fe; color: #0284c7; }
        .metric-card.amber .metric-icon { background: var(--accent-light); color: var(--accent); }
        .metric-card.red .metric-icon { background: var(--danger-bg); color: var(--danger); }

        /* Quick Warning Banner */
        .warning-banner {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1px solid #fde68a;
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            color: #92400e;
        }

        .warning-banner .content {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .warning-banner i {
            font-size: 24px;
            color: #d97706;
        }

        /* POS Layout */
        .pos-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 992px) {
            .pos-grid {
                grid-template-columns: 1fr;
            }
        }

        .pos-products-panel {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--light-border);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .pos-cart-panel {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--light-border);
            padding: 20px;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 85px;
        }

        .search-bar-wrapper {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }

        .search-input-box {
            position: relative;
            flex: 1;
        }

        .search-input-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border-radius: var(--radius-md);
            border: 1px solid var(--light-border);
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        }

        /* Category Chips */
        .category-chips {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .cat-chip {
            padding: 7px 16px;
            background: #f1f5f9;
            border: 1px solid transparent;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .cat-chip:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        .cat-chip.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3);
        }

        /* Product Cards Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
            max-height: 580px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .pos-item-card {
            background: #ffffff;
            border: 1px solid var(--light-border);
            border-radius: var(--radius-md);
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .pos-item-card:hover {
            border-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .pos-item-sku {
            font-size: 11px;
            font-family: monospace;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .pos-item-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.3;
            margin-bottom: 8px;
            min-height: 36px;
        }

        .pos-item-price {
            font-size: 15px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .pos-item-stock {
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
        }

        .stock-safe { background: var(--primary-bg); color: var(--primary-dark); }
        .stock-low { background: var(--accent-light); color: #b45309; }
        .stock-empty { background: var(--danger-bg); color: var(--danger); }

        /* Cart Styles */
        .cart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--light-border);
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .cart-header h3 {
            font-size: 17px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-items-container {
            max-height: 320px;
            overflow-y: auto;
            margin-bottom: 16px;
        }

        .cart-empty-state {
            text-align: center;
            padding: 36px 12px;
            color: var(--text-muted);
        }

        .cart-empty-state i {
            font-size: 40px;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .cart-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--light-border);
        }

        .cart-row-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .cart-row-price {
            font-size: 12px;
            color: var(--text-muted);
        }

        .cart-qty-ctrl {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-qty {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            border: 1px solid var(--light-border);
            background: #f8fafc;
            color: var(--text-main);
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-qty:hover {
            background: #e2e8f0;
        }

        .cart-subtotal-section {
            background: #f8fafc;
            padding: 14px;
            border-radius: var(--radius-md);
            margin-bottom: 16px;
        }

        .cart-summary-line {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .cart-total-line {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            border-top: 1px dashed var(--light-border);
            padding-top: 8px;
            margin-top: 6px;
        }

        .btn-checkout {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 14px;
            font-size: 15px;
            font-weight: 700;
            border-radius: var(--radius-md);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
        }

        .btn-checkout:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.4);
        }

        .btn-checkout:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Generic Table Card */
        .table-card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--light-border);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .table-header h3 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-md);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .app-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .app-table th {
            text-align: left;
            padding: 12px 14px;
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--light-border);
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.5px;
        }

        .app-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--light-border);
            color: var(--text-main);
            vertical-align: middle;
        }

        .app-table tr:hover td {
            background: #fbfcfe;
        }

        .btn-sm-action {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            border: 1px solid var(--light-border);
            background: white;
            cursor: pointer;
            color: var(--text-main);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s;
        }

        .btn-sm-action:hover {
            background: #f1f5f9;
        }

        .btn-sm-action.danger:hover {
            background: var(--danger-bg);
            color: var(--danger);
            border-color: #fca5a5;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 540px;
            box-shadow: var(--shadow-lg);
            animation: modalPop 0.2s ease-out;
            overflow: hidden;
        }

        @keyframes modalPop {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--light-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 18px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .modal-body {
            padding: 24px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--light-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: #f8fafc;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--light-border);
            border-radius: var(--radius-md);
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        }

        /* Quick cash shortcut chips in checkout */
        .quick-cash-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .cash-chip {
            padding: 6px 12px;
            background: #f1f5f9;
            border: 1px solid var(--light-border);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .cash-chip:hover {
            background: #e2e8f0;
        }

        /* Toast notification */
        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: #1e293b;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-lg);
            animation: slideUp 0.3s ease;
        }

        .toast.success { background: #065f46; border-left: 4px solid #10b981; }
        .toast.error { background: #881337; border-left: 4px solid #f43f5e; }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ========================================== */
        /* LOGIN SCREEN STYLES                        */
        /* ========================================== */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: radial-gradient(circle at 10% 20%, rgba(5, 150, 105, 0.08) 0%, rgba(2, 132, 199, 0.05) 50%, #f8fafc 100%);
            position: relative;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 24px;
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.02);
            width: 100%;
            max-width: 440px;
            padding: 40px 36px;
            animation: fadeIn 0.3s ease-out;
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-logo {
            width: 62px;
            height: 62px;
            background: linear-gradient(135deg, var(--primary), #0284c7);
            color: white;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.28);
        }

        .login-header h2 {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 13.5px;
            margin-top: 6px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i.prefix-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 15px;
        }

        .input-with-icon .form-control {
            padding-left: 42px;
            padding-right: 42px;
            height: 46px;
            font-size: 14px;
        }

        .toggle-password-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 15px;
            padding: 6px;
            border-radius: 4px;
            transition: color 0.2s;
        }

        .toggle-password-btn:hover {
            color: var(--text-main);
        }

        .quick-accounts-box {
            background: #f8fafc;
            border: 1px dashed var(--light-border);
            border-radius: var(--radius-md);
            padding: 14px;
            margin-top: 24px;
        }

        .quick-accounts-box span {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 10px;
        }

        .quick-acc-btns {
            display: flex;
            gap: 10px;
        }

        .btn-quick-acc {
            flex: 1;
            padding: 9px 12px;
            font-size: 12.5px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            border: 1px solid var(--light-border);
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-quick-acc:hover {
            border-color: var(--primary);
            background: var(--primary-bg);
            color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .login-alert {
            display: none;
            background: #fef2f2;
            border-left: 4px solid var(--danger);
            color: #991b1b;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            margin-bottom: 20px;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.2s ease-out;
        }

        .btn-logout {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid #fee2e2;
            background: #fef2f2;
            color: #b91c1c;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <!-- ========================================== -->
    <!-- SCREEN 1: LOGIN VIEW                       -->
    <!-- ========================================== -->
    <div id="screen-login" class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h2>NURMART</h2>
                <p>Sistem Kasir POS & Pengelola Toko Modern</p>
            </div>

            <div id="login-alert-box" class="login-alert">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span id="login-alert-msg">Email atau password salah.</span>
            </div>

            <form id="form-login" onsubmit="handleFormLogin(event)">
                <div class="form-group">
                    <label for="login-email">Alamat Email</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-envelope prefix-icon"></i>
                        <input type="email" id="login-email" class="form-control" placeholder="nama@nurmart.com" value="pemilik@nurmart.com" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-lock prefix-icon"></i>
                        <input type="password" id="login-password" class="form-control" placeholder="Masukkan password" value="password123" required autocomplete="current-password">
                        <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility()" title="Lihat password">
                            <i id="toggle-password-icon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btn-submit-login" class="btn-primary" style="width: 100%; height: 46px; justify-content: center; font-size: 14.5px; font-weight: 700; margin-top: 10px;">
                    <i class="fa-solid fa-right-to-bracket"></i> <span id="login-btn-text">Masuk ke Sistem</span>
                </button>
            </form>

            <div class="quick-accounts-box">
                <span><i class="fa-solid fa-bolt" style="color: var(--accent);"></i> Akses Cepat Akun Demo (1-Klik)</span>
                <div class="quick-acc-btns">
                    <button type="button" class="btn-quick-acc" onclick="fillQuickLogin('pemilik')">
                        👑 Pemilik Toko
                    </button>
                    <button type="button" class="btn-quick-acc" onclick="fillQuickLogin('kasir')">
                        ⚡ Kasir Toko
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCREEN 2: MAIN DASHBOARD & POS             -->
    <!-- ========================================== -->
    <div id="screen-main" style="display: none; flex-direction: column; min-height: 100vh;">

        <!-- Top Navbar -->
        <header class="navbar">
            <a href="#" class="brand-logo">
                <div class="brand-icon">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div class="brand-text">
                    <h1>NURMART</h1>
                    <p>Aplikasi Kasir & Pengelola Toko Kelontong Modern</p>
                </div>
            </a>

            <!-- Navigation Tabs -->
            <nav class="nav-center">
                <button class="nav-btn active" onclick="switchTab('dasbor')">
                    <i class="fa-solid fa-chart-pie"></i> Dasbor
                </button>
                <button class="nav-btn" onclick="switchTab('pos')">
                    <i class="fa-solid fa-cash-register"></i> Kasir POS
                </button>
                <button class="nav-btn" onclick="switchTab('barang')">
                    <i class="fa-solid fa-boxes-stacked"></i> Produk
                </button>
                <button class="nav-btn" onclick="switchTab('belanja')">
                    <i class="fa-solid fa-truck-ramp-box"></i> Belanja Stok
                </button>
                <button class="nav-btn" onclick="switchTab('laporan')">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Laba Rugi
                </button>
            </nav>

            <!-- Current User Role & Switcher -->
            <div class="user-status">
                <div style="text-align: right;">
                    <div id="user-display-name" style="font-weight: 700; font-size: 13px;">Haji Mansyur</div>
                    <span id="user-display-role" class="role-badge role-pemilik">👑 Pemilik Toko</span>
                </div>
                <button class="btn-switch-role" onclick="toggleRole()" title="Klik untuk beralih mode Kasir / Pemilik">
                    <i class="fa-solid fa-arrows-rotate"></i> Ganti Role
                </button>
                <button class="btn-logout" onclick="logoutUser()" title="Logout dari sistem">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </div>
        </header>

        <!-- Main Workspace Container -->
        <main class="container">

        <!-- ========================================== -->
        <!-- TAB 1: DASBOR RINGKASAN                    -->
        <!-- ========================================== -->
        <section id="tab-dasbor" class="tab-panel active">
            <!-- Warning Banner for Low Stock -->
            <div id="banner-stok-menipis" class="warning-banner" style="display: none;">
                <div class="content">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        <strong>Peringatan Stok Menipis!</strong>
                        <p id="text-stok-menipis" style="font-size: 13px; margin-top: 2px;">Terdapat beberapa produk yang stoknya hampir habis.</p>
                    </div>
                </div>
                <button class="btn-primary" style="background: #d97706;" onclick="switchTab('belanja')">
                    <i class="fa-solid fa-cart-flatbed"></i> Kulakan Sekarang
                </button>
            </div>

            <!-- Metrics Grid -->
            <div class="grid-metrics">
                <div class="metric-card green">
                    <div class="metric-info">
                        <h4>Omset Hari Ini</h4>
                        <div class="value" id="val-omset-hari">Rp 0</div>
                        <small id="val-transaksi-hari" style="color: var(--text-muted); font-size: 12px;">0 Transaksi</small>
                    </div>
                    <div class="metric-icon">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>

                <div class="metric-card blue">
                    <div class="metric-info">
                        <h4>Omset Bulan Ini</h4>
                        <div class="value" id="val-omset-bulan">Rp 0</div>
                        <small id="val-transaksi-bulan" style="color: var(--text-muted); font-size: 12px;">Bulan Berjalan</small>
                    </div>
                    <div class="metric-icon">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>

                <div class="metric-card amber">
                    <div class="metric-info">
                        <h4>Margin Laba Kotor</h4>
                        <div class="value" id="val-margin-bulan">Rp 0</div>
                        <small style="color: var(--text-muted); font-size: 12px;">Keuntungan Penjualan</small>
                    </div>
                    <div class="metric-icon">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                </div>

                <div class="metric-card red">
                    <div class="metric-info">
                        <h4>Stok Menipis (&le; 10)</h4>
                        <div class="value" id="val-total-menipis">0</div>
                        <small id="val-total-produk" style="color: var(--text-muted); font-size: 12px;">Dari total 0 produk</small>
                    </div>
                    <div class="metric-icon">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards & Low Stock Table -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fa-solid fa-bell" style="color: var(--accent);"></i> Daftar Barang yang Perlu Segera Dibelanjakan</h3>
                        <button class="btn-sm-action" onclick="loadDashboard()"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
                    </div>
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Sisa Stok</th>
                                <th>Harga Beli Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-low-stock-body">
                            <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Memuat data stok...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div class="table-header">
                            <h3><i class="fa-solid fa-bolt" style="color: var(--primary);"></i> Aksi Cepat</h3>
                        </div>
                        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">
                            Jalankan proses operasional Toko Kelontong langsung dari panel cepat di bawah ini.
                        </p>
                        
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <button class="btn-primary" style="justify-content: flex-start; padding: 14px;" onclick="switchTab('pos')">
                                <i class="fa-solid fa-cash-register" style="font-size: 18px; width: 28px;"></i>
                                <div style="text-align: left;">
                                    <div style="font-weight: 700;">Buka Kasir POS</div>
                                    <small style="opacity: 0.9;">Layani transaksi penjualan pelanggan</small>
                                </div>
                            </button>

                            <button class="btn-primary" style="justify-content: flex-start; padding: 14px; background: #0284c7;" onclick="switchTab('belanja')">
                                <i class="fa-solid fa-dolly" style="font-size: 18px; width: 28px;"></i>
                                <div style="text-align: left;">
                                    <div style="font-weight: 700;">Input Belanja Kulakan</div>
                                    <small style="opacity: 0.9;">Tambah stok barang dari supplier</small>
                                </div>
                            </button>

                            <button class="btn-primary" style="justify-content: flex-start; padding: 14px; background: #64748b;" onclick="openTambahBarangModal()">
                                <i class="fa-solid fa-plus" style="font-size: 18px; width: 28px;"></i>
                                <div style="text-align: left;">
                                    <div style="font-weight: 700;">Tambah Produk Baru</div>
                                    <small style="opacity: 0.9;">Daftarkan barang dagangan baru</small>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div style="background: #f1f5f9; padding: 14px; border-radius: var(--radius-md); margin-top: 20px; font-size: 12px; color: var(--text-muted);">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                        <strong>Terhubung ke API Laravel 12:</strong> Server aktif pada port <code>8000</code>. Siap diakses oleh mobile Flutter.
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- TAB 2: KASIR / POS (POINT OF SALE)         -->
        <!-- ========================================== -->
        <section id="tab-pos" class="tab-panel">
            <div class="pos-grid">
                <!-- Left Panel: Products Browser -->
                <div class="pos-products-panel">
                    <div class="search-bar-wrapper">
                        <div class="search-input-box">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="pos-search-input" class="search-input" placeholder="Ketik nama barang, scan barcode, atau SKU..." oninput="handlePosSearch()">
                        </div>
                    </div>

                    <!-- Category Chips Filter -->
                    <div id="pos-category-chips" class="category-chips">
                        <button class="cat-chip active" onclick="filterPosCategory(null, this)">Semua Kategori</button>
                    </div>

                    <!-- Product Grid -->
                    <div id="pos-product-grid" class="product-grid">
                        <!-- Loaded dynamically -->
                    </div>
                </div>

                <!-- Right Panel: Shopping Cart -->
                <div class="pos-cart-panel">
                    <div class="cart-header">
                        <h3><i class="fa-solid fa-basket-shopping" style="color: var(--primary);"></i> Keranjang Belanja</h3>
                        <button class="btn-sm-action danger" onclick="clearCart()"><i class="fa-solid fa-trash-can"></i> Kosongkan</button>
                    </div>

                    <div id="pos-cart-items" class="cart-items-container">
                        <div class="cart-empty-state">
                            <i class="fa-solid fa-cart-plus"></i>
                            <p>Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.</p>
                        </div>
                    </div>

                    <div class="cart-subtotal-section">
                        <div class="cart-summary-line">
                            <span>Total Item</span>
                            <span id="pos-cart-total-qty">0 pcs</span>
                        </div>
                        <div class="cart-total-line">
                            <span>TOTAL BAYAR</span>
                            <span id="pos-cart-total-rp" style="color: var(--primary);">Rp 0</span>
                        </div>
                    </div>

                    <button id="btn-open-payment" class="btn-checkout" onclick="openPaymentModal()" disabled>
                        <i class="fa-solid fa-money-bill-wave"></i> Bayar Sekarang (F8)
                    </button>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- TAB 3: MANAJEMEN MASTER PRODUK / BARANG    -->
        <!-- ========================================== -->
        <section id="tab-barang" class="tab-panel">
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i> Katalog Produk Toko Kelontong</h3>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="barang-search-input" class="form-control" style="width: 250px; padding: 8px 12px;" placeholder="Cari nama / barcode..." oninput="loadMasterBarang()">
                        <button class="btn-primary" onclick="openTambahBarangModal()">
                            <i class="fa-solid fa-plus"></i> Tambah Produk
                        </button>
                    </div>
                </div>

                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>SKU</th>
                            <th>Barcode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="master-barang-tbody">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- TAB 4: BELANJA BARANG / PURCHASING         -->
        <!-- ========================================== -->
        <section id="tab-belanja" class="tab-panel">
            <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px;">
                <!-- Form Input Belanja -->
                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fa-solid fa-cart-flatbed" style="color: var(--primary);"></i> Input Belanja Kulakan (Tambah Stok)</h3>
                    </div>

                    <form id="form-belanja" onsubmit="handleSimpanBelanja(event)">
                        <div class="form-group">
                            <label>Pilih Supplier *</label>
                            <select id="belanja-supplier-select" class="form-control" required>
                                <option value="">-- Pilih Supplier --</option>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div class="form-group">
                                <label>Tanggal Belanja *</label>
                                <input type="date" id="belanja-tanggal" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>No. Faktur (Opsional)</label>
                                <input type="text" id="belanja-no-faktur" class="form-control" placeholder="Otomatis jika kosong">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Catatan Pembelian</label>
                            <input type="text" id="belanja-catatan" class="form-control" placeholder="Contoh: Kulakan beras dan minyak">
                        </div>

                        <hr style="border-top: 1px dashed var(--light-border); margin: 16px 0;">

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <label style="font-weight: 700; font-size: 13px; text-transform: uppercase;">Daftar Item yang Dibelanjakan</label>
                            <button type="button" class="btn-sm-action" onclick="addBelanjaRow()">
                                <i class="fa-solid fa-plus"></i> Tambah Item
                            </button>
                        </div>

                        <div id="belanja-items-list" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                            <!-- Dynamic Belanja Rows -->
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; font-size: 15px; justify-content: center;">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Transaksi Belanja & Tambah Stok
                        </button>
                    </form>
                </div>

                <!-- Riwayat Belanja Terbaru -->
                <div class="table-card">
                    <div class="table-header">
                        <h3><i class="fa-solid fa-clock-rotate-left" style="color: #0284c7;"></i> Riwayat Belanja Masuk</h3>
                        <button class="btn-sm-action" onclick="loadRiwayatBelanja()"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
                    </div>

                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>No. Faktur</th>
                                <th>Supplier</th>
                                <th>Tanggal</th>
                                <th>Total Belanja</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-belanja-tbody">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- TAB 5: LAPORAN LABA RUGI & RIWAYAT NOTA    -->
        <!-- ========================================== -->
        <section id="tab-laporan" class="tab-panel">
            <!-- Filter Bar -->
            <div class="table-card" style="margin-bottom: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <h3 style="font-size: 17px; font-weight: 700;"><i class="fa-solid fa-filter" style="color: var(--primary);"></i> Filter Periode Laporan Laba Rugi</h3>
                        <p style="font-size: 12px; color: var(--text-muted);">Hitung omset penjualan, modal HPP produk, dan laba kotor toko.</p>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="date" id="laporan-start-date" class="form-control" style="width: auto;">
                        <span>s/d</span>
                        <input type="date" id="laporan-end-date" class="form-control" style="width: auto;">
                        <button class="btn-primary" onclick="loadLabaRugiReport()">
                            <i class="fa-solid fa-chart-column"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="grid-metrics">
                <div class="metric-card green">
                    <div class="metric-info">
                        <h4>Total Omset Penjualan</h4>
                        <div class="value" id="lap-val-penjualan">Rp 0</div>
                        <small id="lap-val-breakdown" style="color: var(--text-muted); font-size: 12px;">Tunai / QRIS</small>
                    </div>
                    <div class="metric-icon"><i class="fa-solid fa-cash-register"></i></div>
                </div>

                <div class="metric-card amber">
                    <div class="metric-info">
                        <h4>HPP Barang Terjual</h4>
                        <div class="value" id="lap-val-hpp">Rp 0</div>
                        <small style="color: var(--text-muted); font-size: 12px;">Modal Pokok Barang</small>
                    </div>
                    <div class="metric-icon"><i class="fa-solid fa-tags"></i></div>
                </div>

                <div class="metric-card blue">
                    <div class="metric-info">
                        <h4>Laba Kotor Toko</h4>
                        <div class="value" id="lap-val-laba">Rp 0</div>
                        <small style="color: var(--text-muted); font-size: 12px;">Omset - HPP Terjual</small>
                    </div>
                    <div class="metric-icon"><i class="fa-solid fa-wallet"></i></div>
                </div>

                <div class="metric-card red">
                    <div class="metric-info">
                        <h4>Total Belanja Kulakan</h4>
                        <div class="value" id="lap-val-belanja">Rp 0</div>
                        <small style="color: var(--text-muted); font-size: 12px;">Pengeluaran ke Supplier</small>
                    </div>
                    <div class="metric-icon"><i class="fa-solid fa-truck-moving"></i></div>
                </div>
            </div>

            <!-- Riwayat Penjualan / Nota -->
            <div class="table-card">
                <div class="table-header">
                    <h3><i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Riwayat Transaksi Penjualan & Cetak Struk PDF</h3>
                    <button class="btn-sm-action" onclick="loadRiwayatPenjualan()"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
                </div>

                <table class="app-table">
                    <thead>
                        <tr>
                            <th>No. Nota</th>
                            <th>Tanggal & Waktu</th>
                            <th>Kasir</th>
                            <th>Metode Bayar</th>
                            <th>Total Belanja</th>
                            <th>Bayar</th>
                            <th>Kembalian</th>
                            <th>Struk PDF</th>
                        </tr>
                    </thead>
                    <tbody id="riwayat-penjualan-tbody">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- ========================================== -->
    <!-- MODAL 1: PEMBAYARAN KASIR POS             -->
    <!-- ========================================== -->
    <div id="modal-payment" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3><i class="fa-solid fa-wallet" style="color: var(--primary);"></i> Konfirmasi Pembayaran POS</h3>
                <button class="modal-close" onclick="closeModal('modal-payment')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="background: var(--primary-bg); padding: 16px; border-radius: var(--radius-md); text-align: center; margin-bottom: 20px;">
                    <span style="font-size: 13px; color: var(--primary-dark); font-weight: 600;">TOTAL HARUS DIBAYAR</span>
                    <div id="modal-payment-total" style="font-size: 28px; font-weight: 800; color: var(--primary-dark); margin-top: 4px;">Rp 0</div>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" id="pay-mode-tunai" class="btn-primary" style="flex: 1; justify-content: center;" onclick="setPaymentMode('tunai')">
                            <i class="fa-solid fa-money-bill-1-wave"></i> TUNAI
                        </button>
                        <button type="button" id="pay-mode-qris" class="btn-sm-action" style="flex: 1; justify-content: center; font-weight: 700;" onclick="setPaymentMode('qris')">
                            <i class="fa-solid fa-qrcode"></i> QRIS
                        </button>
                    </div>
                </div>

                <!-- Cash input section -->
                <div id="section-pay-tunai">
                    <div class="form-group">
                        <label>Jumlah Uang Diterima (Rp)</label>
                        <input type="number" id="input-jumlah-bayar" class="form-control" style="font-size: 18px; font-weight: 700;" placeholder="0" oninput="calculateKembalian()">
                        
                        <!-- Quick Cash Shortcut Chips -->
                        <div class="quick-cash-chips">
                            <button type="button" class="cash-chip" onclick="setCashExact()">Uang Pas</button>
                            <button type="button" class="cash-chip" onclick="addCash(10000)">+10.000</button>
                            <button type="button" class="cash-chip" onclick="addCash(20000)">+20.000</button>
                            <button type="button" class="cash-chip" onclick="addCash(50000)">+50.000</button>
                            <button type="button" class="cash-chip" onclick="addCash(100000)">+100.000</button>
                        </div>
                    </div>

                    <div style="background: #f8fafc; border: 1px dashed var(--light-border); padding: 14px; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; font-size: 14px;">KEMBALIAN:</span>
                        <span id="display-kembalian" style="font-size: 20px; font-weight: 800; color: var(--primary);">Rp 0</span>
                    </div>
                </div>

                <!-- QRIS Section -->
                <div id="section-pay-qris" style="display: none; text-align: center; padding: 14px; background: #f8fafc; border-radius: var(--radius-md);">
                    <i class="fa-solid fa-qrcode" style="font-size: 80px; color: var(--text-main); margin-bottom: 8px;"></i>
                    <p style="font-size: 13px; font-weight: 600;">Scan QRIS Toko Kelontong NURMART</p>
                    <small style="color: var(--text-muted);">Mendukung GoPay, OVO, Dana, ShopeePay, BCA, dll.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sm-action" onclick="closeModal('modal-payment')">Batal</button>
                <button type="button" id="btn-submit-penjualan" class="btn-primary" onclick="submitPenjualan()">
                    <i class="fa-solid fa-check"></i> Selesaikan Transaksi
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: NOTA SELESAI & CETAK STRUK PDF   -->
    <!-- ========================================== -->
    <div id="modal-success-nota" class="modal-overlay">
        <div class="modal-box" style="text-align: center;">
            <div class="modal-body" style="padding: 36px 24px;">
                <div style="width: 72px; height: 72px; background: var(--primary-bg); color: var(--primary); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 32px; margin-bottom: 16px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 style="font-size: 22px; font-weight: 800; margin-bottom: 4px;">Transaksi Berhasil Disimpan!</h3>
                <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">
                    Stok barang otomatis berkurang dan transaksi tercatat di database.
                </p>

                <div style="background: #f8fafc; border: 1px solid var(--light-border); border-radius: var(--radius-md); padding: 16px; margin-bottom: 24px; text-align: left;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                        <span style="color: var(--text-muted);">No. Nota:</span>
                        <strong id="success-no-nota">PJ-XXXXXXXX-XXXX</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                        <span style="color: var(--text-muted);">Total Belanja:</span>
                        <strong id="success-total">Rp 0</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--text-muted);">Kembalian:</span>
                        <strong id="success-kembalian" style="color: var(--primary);">Rp 0</strong>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a id="btn-download-pdf-receipt" href="#" target="_blank" class="btn-primary" style="justify-content: center; padding: 13px; font-size: 14px;">
                        <i class="fa-solid fa-file-pdf"></i> Unduh / Cetak Struk PDF
                    </a>
                    <button class="btn-sm-action" style="justify-content: center; padding: 11px;" onclick="closeModal('modal-success-nota')">
                        Kembali ke Kasir (Transaksi Baru)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: TAMBAH / EDIT BARANG             -->
    <!-- ========================================== -->
    <div id="modal-barang" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-barang-title"><i class="fa-solid fa-box-open" style="color: var(--primary);"></i> Tambah Produk Baru</h3>
                <button class="modal-close" onclick="closeModal('modal-barang')">&times;</button>
            </div>
            <form id="form-barang" onsubmit="handleSaveBarang(event)">
                <input type="hidden" id="barang-id">
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label>Kode SKU *</label>
                            <input type="text" id="input-barang-sku" class="form-control" required placeholder="Contoh: BRG-0006">
                        </div>
                        <div class="form-group">
                            <label>Barcode Scanner</label>
                            <input type="text" id="input-barang-barcode" class="form-control" placeholder="Contoh: 89912345678">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Produk / Barang *</label>
                        <input type="text" id="input-barang-nama" class="form-control" required placeholder="Contoh: Kopi Kapal Api 65g">
                    </div>

                    <div class="form-group">
                        <label>Kategori Produk *</label>
                        <select id="input-barang-kategori" class="form-control" required>
                            <!-- Dynamic Categories -->
                        </select>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label>Harga Beli (Modal) *</label>
                            <input type="number" id="input-barang-beli" class="form-control" required placeholder="0">
                        </div>
                        <div class="form-group">
                            <label>Harga Jual *</label>
                            <input type="number" id="input-barang-jual" class="form-control" required placeholder="0">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input type="number" id="input-barang-stok" class="form-control" value="0">
                        </div>
                        <div class="form-group">
                            <label>Satuan *</label>
                            <input type="text" id="input-barang-satuan" class="form-control" required value="pcs" placeholder="pcs, kg, dus, botol">
                        </div>
                    </div>

                    <!-- Upload Gambar Produk -->
                    <div class="form-group" style="margin-top: 4px;">
                        <label>Foto Produk</label>
                        <div id="gambar-upload-area" style="border: 2px dashed var(--light-border); border-radius: var(--radius-md); padding: 16px; text-align: center; cursor: pointer; transition: border-color 0.2s;" onclick="document.getElementById('input-barang-gambar').click()" ondragover="event.preventDefault(); this.style.borderColor='var(--primary)'" ondragleave="this.style.borderColor='var(--light-border)'" ondrop="handleGambarDrop(event)">
                            <div id="gambar-preview-wrap" style="display:none; position:relative; display:inline-block;">
                                <img id="gambar-preview-img" src="" alt="Preview" style="max-height:120px; max-width:100%; border-radius:8px; object-fit:cover;">
                                <button type="button" onclick="clearGambarInput(event)" style="position:absolute;top:-8px;right:-8px;background:var(--danger);color:white;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div id="gambar-upload-placeholder">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size:28px; color:var(--text-muted); margin-bottom:8px;"></i>
                                <p style="color:var(--text-muted); font-size:13px; margin:0;">Klik atau drag &amp; drop gambar di sini</p>
                                <p style="color:var(--text-muted); font-size:11px; margin:4px 0 0;">JPG, PNG, WebP — maks. 2MB</p>
                            </div>
                        </div>
                        <input type="file" id="input-barang-gambar" accept="image/jpeg,image/png,image/jpg,image/webp" style="display:none;" onchange="handleGambarChange(event)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sm-action" onclick="closeModal('modal-barang')">Batal</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-save"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    </div><!-- END screen-main -->
    <!-- END OF SCREEN 2: MAIN DASHBOARD & POS -->

    <!-- Toast Notification Container (global, outside both screens) -->
    <div id="toast-container" class="toast-container"></div>

    <!-- ========================================== -->
    <!-- JAVASCRIPT APPLICATION CORE                -->
    <!-- ========================================== -->
    <script>
        // State Management
        const API_BASE = '/api';
        let currentRole = 'pemilik'; // 'pemilik' or 'kasir'
        let authToken = '';
        let allProducts = [];
        let allCategories = [];
        let allSuppliers = [];
        let currentPosCategory = null;
        let cart = []; // [{barang: {...}, jumlah: 1}]
        let currentPaymentMethod = 'tunai';
        let currentPenjualanId = null;

        // Login & Authentication Management
        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('login-password');
            const icon = document.getElementById('toggle-password-icon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                pwdInput.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        }

        function fillQuickLogin(role) {
            const emailInput = document.getElementById('login-email');
            const pwdInput = document.getElementById('login-password');
            
            if (role === 'pemilik') {
                emailInput.value = 'pemilik@nurmart.com';
                pwdInput.value = 'password123';
            } else {
                emailInput.value = 'kasir@nurmart.com';
                pwdInput.value = 'password123';
            }
            
            submitLogin(emailInput.value, pwdInput.value);
        }

        function handleFormLogin(e) {
            e.preventDefault();
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;
            submitLogin(email, password);
        }

        async function submitLogin(email, password) {
            const btn = document.getElementById('btn-submit-login');
            const btnText = document.getElementById('login-btn-text');
            const alertBox = document.getElementById('login-alert-box');
            const alertMsg = document.getElementById('login-alert-msg');

            alertBox.style.display = 'none';
            btn.disabled = true;
            btnText.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';

            try {
                const res = await fetch(`${API_BASE}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email, password, device_name: 'Web Dashboard' })
                });

                const data = await res.json();
                if (data.status) {
                    authToken = data.data.token;
                    currentRole = data.data.user.role;
                    localStorage.setItem('nurmart_token', authToken);
                    localStorage.setItem('nurmart_user', JSON.stringify(data.data.user));

                    updateUserHeader(data.data.user);
                    showToast(`Selamat datang, ${data.data.user.name}!`, 'success');

                    // Switch screen to main app
                    document.getElementById('screen-login').style.display = 'none';
                    document.getElementById('screen-main').style.display = 'flex';

                    refreshAllData();
                } else {
                    alertBox.style.display = 'flex';
                    alertMsg.innerText = data.message || 'Email atau password salah.';
                }
            } catch (err) {
                console.error('Auth error:', err);
                alertBox.style.display = 'flex';
                alertMsg.innerText = 'Gagal terhubung ke server backend.';
            } finally {
                btn.disabled = false;
                btnText.innerHTML = 'Masuk ke Sistem';
            }
        }

        async function logoutUser() {
            if (!confirm('Apakah Anda yakin ingin keluar dari sistem NURMART?')) return;

            try {
                await fetch(`${API_BASE}/logout`, {
                    method: 'POST',
                    headers: apiHeaders()
                });
            } catch (e) {
                console.warn('Logout API warning:', e);
            }

            localStorage.removeItem('nurmart_token');
            localStorage.removeItem('nurmart_user');
            authToken = '';

            document.getElementById('screen-main').style.display = 'none';
            document.getElementById('screen-login').style.display = 'flex';
            showToast('Anda telah berhasil keluar dari akun.', 'success');
        }

        function toggleRole() {
            const nextRole = currentRole === 'pemilik' ? 'kasir' : 'pemilik';
            fillQuickLogin(nextRole);
        }

        async function checkAuthSession() {
            const savedToken = localStorage.getItem('nurmart_token');

            if (savedToken) {
                authToken = savedToken;
                try {
                    const res = await fetch(`${API_BASE}/profile`, { headers: apiHeaders() });
                    const json = await res.json();
                    if (json.status) {
                        currentRole = json.data.role;
                        updateUserHeader(json.data);
                        document.getElementById('screen-login').style.display = 'none';
                        document.getElementById('screen-main').style.display = 'flex';
                        refreshAllData();
                        return;
                    }
                } catch (e) {
                    console.warn('Session verification failed:', e);
                }
            }

            // If no token or verification failed, show login screen
            localStorage.removeItem('nurmart_token');
            localStorage.removeItem('nurmart_user');
            authToken = '';
            document.getElementById('screen-main').style.display = 'none';
            document.getElementById('screen-login').style.display = 'flex';
        }

        function updateUserHeader(user) {
            const nameEl = document.getElementById('user-display-name');
            const roleEl = document.getElementById('user-display-role');

            nameEl.innerText = user.name;
            if (user.role === 'pemilik') {
                roleEl.className = 'role-badge role-pemilik';
                roleEl.innerHTML = '👑 Pemilik Toko';
            } else {
                roleEl.className = 'role-badge role-kasir';
                roleEl.innerHTML = '⚡ Kasir Toko';
            }
        }

        // Tab Navigation
        function switchTab(tabId) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));

            const targetPanel = document.getElementById(`tab-${tabId}`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }

            // Find matching nav button
            const navButtons = document.querySelectorAll('.nav-btn');
            navButtons.forEach(btn => {
                if (btn.getAttribute('onclick')?.includes(tabId)) {
                    btn.classList.add('active');
                }
            });

            // Trigger specific loaders
            if (tabId === 'dasbor') loadDashboard();
            if (tabId === 'pos') loadPosProducts();
            if (tabId === 'barang') loadMasterBarang();
            if (tabId === 'belanja') loadBelanjaTab();
            if (tabId === 'laporan') loadLabaRugiReport();
        }

        // API Helpers
        function apiHeaders() {
            return {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${authToken}`
            };
        }

        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const icon = type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation';
            toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3500);
        }

        // Modal Helpers
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // ============================================
        // 1. DATA LOADER & DASHBOARD
        // ============================================
        async function refreshAllData() {
            await Promise.all([
                loadDashboard(),
                loadCategories(),
                loadSuppliers(),
                loadPosProducts(),
                loadMasterBarang()
            ]);
        }

        async function loadCategories() {
            try {
                const res = await fetch(`${API_BASE}/kategori`, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    allCategories = json.data;
                    renderCategoryChips();
                    renderCategorySelectOptions();
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function loadSuppliers() {
            try {
                const res = await fetch(`${API_BASE}/supplier`, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    allSuppliers = json.data;
                    renderSupplierSelectOptions();
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function loadDashboard() {
            if (currentRole !== 'pemilik') {
                // Kasir doesn't have access to owner dashboard report
                document.getElementById('val-omset-hari').innerText = 'Akses Terproteksi';
                document.getElementById('val-omset-bulan').innerText = 'Khusus Pemilik';
                document.getElementById('val-margin-bulan').innerText = '-';
                return;
            }

            try {
                const res = await fetch(`${API_BASE}/laporan/dasbor`, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    const d = json.data;
                    document.getElementById('val-omset-hari').innerText = formatRupiah(d.hari_ini.total_omset);
                    document.getElementById('val-transaksi-hari').innerText = `${d.hari_ini.total_transaksi} Transaksi`;

                    document.getElementById('val-omset-bulan').innerText = formatRupiah(d.bulan_ini.total_omset);
                    document.getElementById('val-transaksi-bulan').innerText = `${d.bulan_ini.total_transaksi} Transaksi`;
                    document.getElementById('val-margin-bulan').innerText = formatRupiah(d.bulan_ini.total_keuntungan_margin);

                    document.getElementById('val-total-menipis').innerText = d.inventaris.total_stok_menipis;
                    document.getElementById('val-total-produk').innerText = `Dari total ${d.inventaris.total_produk} produk`;

                    // Low stock warning banner
                    const banner = document.getElementById('banner-stok-menipis');
                    if (d.inventaris.total_stok_menipis > 0) {
                        banner.style.display = 'flex';
                        document.getElementById('text-stok-menipis').innerText = 
                            `Ada ${d.inventaris.total_stok_menipis} produk yang stoknya ≤ 10 pcs. Segera lakukan kulakan ke supplier.`;
                    } else {
                        banner.style.display = 'none';
                    }

                    // Render Low Stock Table
                    const tbody = document.getElementById('table-low-stock-body');
                    if (d.inventaris.daftar_stok_menipis.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; color: var(--primary);">Semua stok produk dalam kondisi aman (&gt; 10 pcs).</td></tr>`;
                    } else {
                        tbody.innerHTML = d.inventaris.daftar_stok_menipis.map(b => `
                            <tr>
                                <td><code>${b.kode_sku}</code></td>
                                <td style="font-weight: 700;">${b.nama_barang}</td>
                                <td><span class="role-badge role-kasir">${b.kategori?.nama_kategori || '-'}</span></td>
                                <td><span class="pos-item-stock stock-low">${b.stok} ${b.satuan}</span></td>
                                <td>${formatRupiah(b.harga_beli)}</td>
                                <td>
                                    <button class="btn-sm-action" onclick="quickReorder(${b.id})">
                                        <i class="fa-solid fa-cart-plus"></i> Kulakan
                                    </button>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        // ============================================
        // 2. KASIR / POS LOGIC
        // ============================================
        async function loadPosProducts() {
            try {
                const res = await fetch(`${API_BASE}/barang?all=true`, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    allProducts = json.data;
                    renderPosProducts();
                }
            } catch (e) {
                console.error(e);
            }
        }

        function renderCategoryChips() {
            const container = document.getElementById('pos-category-chips');
            let html = `<button class="cat-chip ${currentPosCategory === null ? 'active' : ''}" onclick="filterPosCategory(null, this)">Semua Kategori</button>`;
            
            allCategories.forEach(cat => {
                html += `<button class="cat-chip ${currentPosCategory === cat.id ? 'active' : ''}" onclick="filterPosCategory(${cat.id}, this)">${cat.nama_kategori}</button>`;
            });
            container.innerHTML = html;
        }

        function filterPosCategory(catId, btn) {
            currentPosCategory = catId;
            document.querySelectorAll('#pos-category-chips .cat-chip').forEach(c => c.classList.remove('active'));
            btn.classList.add('active');
            renderPosProducts();
        }

        function handlePosSearch() {
            renderPosProducts();
        }

        function renderPosProducts() {
            const grid = document.getElementById('pos-product-grid');
            const searchKeyword = (document.getElementById('pos-search-input')?.value || '').toLowerCase().trim();

            const filtered = allProducts.filter(item => {
                const matchCat = currentPosCategory === null || item.kategori_id === currentPosCategory;
                const matchSearch = !searchKeyword || 
                    item.nama_barang.toLowerCase().includes(searchKeyword) ||
                    item.kode_sku.toLowerCase().includes(searchKeyword) ||
                    (item.barcode && item.barcode.toLowerCase().includes(searchKeyword));
                return matchCat && matchSearch;
            });

            if (filtered.length === 0) {
                grid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);">Tidak ada produk yang cocok dengan pencarian.</div>`;
                return;
            }

            grid.innerHTML = filtered.map(item => {
                const stockClass = item.stok <= 0 ? 'stock-empty' : (item.stok <= 10 ? 'stock-low' : 'stock-safe');
                const stockLabel = item.stok <= 0 ? 'Habis' : `Stok: ${item.stok} ${item.satuan}`;
                return `
                    <div class="pos-item-card" onclick="addToCart(${item.id})">
                        <div>
                            <div class="pos-item-sku">${item.kode_sku}</div>
                            <div class="pos-item-name">${item.nama_barang}</div>
                        </div>
                        <div>
                            <div class="pos-item-price">${formatRupiah(item.harga_jual)}</div>
                            <span class="pos-item-stock ${stockClass}">${stockLabel}</span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function addToCart(productId) {
            const product = allProducts.find(p => p.id === productId);
            if (!product) return;

            if (product.stok <= 0) {
                showToast(`Stok ${product.nama_barang} habis!`, 'error');
                return;
            }

            const existingIndex = cart.findIndex(c => c.barang.id === productId);
            if (existingIndex >= 0) {
                if (cart[existingIndex].jumlah + 1 > product.stok) {
                    showToast(`Jumlah melebihi stok yang ada (${product.stok})!`, 'error');
                    return;
                }
                cart[existingIndex].jumlah += 1;
            } else {
                cart.push({ barang: product, jumlah: 1 });
            }

            renderCart();
        }

        function updateCartQty(productId, delta) {
            const index = cart.findIndex(c => c.barang.id === productId);
            if (index < 0) return;

            const newQty = cart[index].jumlah + delta;
            if (newQty <= 0) {
                cart.splice(index, 1);
            } else {
                if (newQty > cart[index].barang.stok) {
                    showToast(`Stok hanya tersisa ${cart[index].barang.stok}!`, 'error');
                    return;
                }
                cart[index].jumlah = newQty;
            }
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        function getCartTotal() {
            return cart.reduce((acc, curr) => acc + (curr.jumlah * curr.barang.harga_jual), 0);
        }

        function renderCart() {
            const container = document.getElementById('pos-cart-items');
            const totalQtyEl = document.getElementById('pos-cart-total-qty');
            const totalRpEl = document.getElementById('pos-cart-total-rp');
            const btnCheckout = document.getElementById('btn-open-payment');

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="cart-empty-state">
                        <i class="fa-solid fa-cart-plus"></i>
                        <p>Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.</p>
                    </div>
                `;
                totalQtyEl.innerText = '0 pcs';
                totalRpEl.innerText = 'Rp 0';
                btnCheckout.disabled = true;
                return;
            }

            const totalQty = cart.reduce((a, b) => a + b.jumlah, 0);
            const totalRp = getCartTotal();

            totalQtyEl.innerText = `${totalQty} pcs`;
            totalRpEl.innerText = formatRupiah(totalRp);
            btnCheckout.disabled = false;

            container.innerHTML = cart.map(item => `
                <div class="cart-row">
                    <div style="flex: 1; padding-right: 8px;">
                        <div class="cart-row-title">${item.barang.nama_barang}</div>
                        <div class="cart-row-price">${formatRupiah(item.barang.harga_jual)} x ${item.jumlah} = <strong>${formatRupiah(item.barang.harga_jual * item.jumlah)}</strong></div>
                    </div>
                    <div class="cart-qty-ctrl">
                        <button class="btn-qty" onclick="updateCartQty(${item.barang.id}, -1)">-</button>
                        <span style="font-weight: 700; width: 22px; text-align: center; font-size: 13px;">${item.jumlah}</span>
                        <button class="btn-qty" onclick="updateCartQty(${item.barang.id}, 1)">+</button>
                    </div>
                </div>
            `).join('');
        }

        // POS Checkout & Payment
        function openPaymentModal() {
            if (cart.length === 0) return;

            const total = getCartTotal();
            document.getElementById('modal-payment-total').innerText = formatRupiah(total);
            document.getElementById('input-jumlah-bayar').value = total;
            setPaymentMode('tunai');
            calculateKembalian();
            openModal('modal-payment');
        }

        function setPaymentMode(mode) {
            currentPaymentMethod = mode;
            const btnTunai = document.getElementById('pay-mode-tunai');
            const btnQris = document.getElementById('pay-mode-qris');
            const secTunai = document.getElementById('section-pay-tunai');
            const secQris = document.getElementById('section-pay-qris');

            if (mode === 'tunai') {
                btnTunai.className = 'btn-primary';
                btnQris.className = 'btn-sm-action';
                secTunai.style.display = 'block';
                secQris.style.display = 'none';
            } else {
                btnTunai.className = 'btn-sm-action';
                btnQris.className = 'btn-primary';
                secTunai.style.display = 'none';
                secQris.style.display = 'block';
                // Set exact amount for QRIS
                document.getElementById('input-jumlah-bayar').value = getCartTotal();
                calculateKembalian();
            }
        }

        function setCashExact() {
            document.getElementById('input-jumlah-bayar').value = getCartTotal();
            calculateKembalian();
        }

        function addCash(amount) {
            const input = document.getElementById('input-jumlah-bayar');
            const current = Number(input.value) || 0;
            input.value = current + amount;
            calculateKembalian();
        }

        function calculateKembalian() {
            const total = getCartTotal();
            const bayar = Number(document.getElementById('input-jumlah-bayar').value) || 0;
            const kembalian = bayar - total;

            const el = document.getElementById('display-kembalian');
            if (kembalian < 0) {
                el.innerText = 'Kurang ' + formatRupiah(Math.abs(kembalian));
                el.style.color = 'var(--danger)';
            } else {
                el.innerText = formatRupiah(kembalian);
                el.style.color = 'var(--primary)';
            }
        }

        async function submitPenjualan() {
            const total = getCartTotal();
            const jumlahBayar = Number(document.getElementById('input-jumlah-bayar').value) || 0;

            if (jumlahBayar < total) {
                showToast('Uang pembayaran masih kurang!', 'error');
                return;
            }

            const payload = {
                metode_pembayaran: currentPaymentMethod,
                jumlah_bayar: jumlahBayar,
                items: cart.map(item => ({
                    barang_id: item.barang.id,
                    jumlah: item.jumlah,
                    harga_jual_satuan: item.barang.harga_jual
                }))
            };

            const btnSubmit = document.getElementById('btn-submit-penjualan');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...`;

            try {
                const res = await fetch(`${API_BASE}/penjualan`, {
                    method: 'POST',
                    headers: apiHeaders(),
                    body: JSON.stringify(payload)
                });

                const json = await res.json();
                if (json.status) {
                    closeModal('modal-payment');
                    currentPenjualanId = json.data.id;

                    // Show success receipt modal with Confetti!
                    document.getElementById('success-no-nota').innerText = json.data.no_nota;
                    document.getElementById('success-total').innerText = formatRupiah(json.data.total_belanja);
                    document.getElementById('success-kembalian').innerText = formatRupiah(json.data.kembalian);
                    document.getElementById('btn-download-pdf-receipt').href = `${API_BASE}/penjualan/${json.data.id}/cetak-struk`;

                    confetti({ particleCount: 80, spread: 60, origin: { y: 0.6 } });
                    openModal('modal-success-nota');

                    // Reset cart & refresh
                    cart = [];
                    renderCart();
                    loadPosProducts();
                    loadDashboard();
                } else {
                    showToast('Gagal memproses penjualan: ' + json.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan', 'error');
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<i class="fa-solid fa-check"></i> Selesaikan Transaksi`;
            }
        }

        // ============================================
        // 3. MASTER BARANG / PRODUK
        // ============================================
        async function loadMasterBarang() {
            const searchKeyword = (document.getElementById('barang-search-input')?.value || '').toLowerCase().trim();
            try {
                const res = await fetch(`${API_BASE}/barang?all=true`, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    const tbody = document.getElementById('master-barang-tbody');
                    const filtered = json.data.filter(b => 
                        !searchKeyword ||
                        b.nama_barang.toLowerCase().includes(searchKeyword) ||
                        b.kode_sku.toLowerCase().includes(searchKeyword) ||
                        (b.barcode && b.barcode.toLowerCase().includes(searchKeyword))
                    );

                    if (filtered.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="11" style="text-align: center; color: var(--text-muted);">Tidak ada data produk.</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = filtered.map(b => {
                        const isLow = b.stok <= 10;
                        const statusBadge = b.stok <= 0 ? 
                            `<span class="pos-item-stock stock-empty">Habis</span>` :
                            (isLow ? `<span class="pos-item-stock stock-low">Menipis</span>` : `<span class="pos-item-stock stock-safe">Aman</span>`);

                        const imgHtml = b.gambar_full_url
                            ? `<img src="${b.gambar_full_url}" alt="${b.nama_barang}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:1px solid var(--light-border);cursor:pointer;" onclick="showGambarModal('${b.gambar_full_url}','${b.nama_barang}')">`
                            : `<div style="width:48px;height:48px;border-radius:8px;background:var(--light-bg);border:1px dashed var(--light-border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:18px;"><i class="fa-solid fa-image"></i></div>`;

                        return `
                            <tr>
                                <td style="text-align:center;">${imgHtml}</td>
                                <td><code>${b.kode_sku}</code></td>
                                <td><small>${b.barcode || '-'}</small></td>
                                <td style="font-weight: 700;">${b.nama_barang}</td>
                                <td>${b.kategori?.nama_kategori || '-'}</td>
                                <td>${formatRupiah(b.harga_beli)}</td>
                                <td style="font-weight: 700; color: var(--primary);">${formatRupiah(b.harga_jual)}</td>
                                <td style="font-weight: 700;">${b.stok}</td>
                                <td>${b.satuan}</td>
                                <td>${statusBadge}</td>
                                <td>
                                    <div style="display: flex; gap: 4px;">
                                        <button class="btn-sm-action" onclick="openEditBarangModal(${b.id})"><i class="fa-solid fa-pen"></i></button>
                                        <button class="btn-sm-action danger" onclick="deleteBarang(${b.id})"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                }
            } catch (err) {
                console.error(err);
            }
        }

        function renderCategorySelectOptions() {
            const select = document.getElementById('input-barang-kategori');
            select.innerHTML = allCategories.map(c => `<option value="${c.id}">${c.nama_kategori}</option>`).join('');
        }

        function openTambahBarangModal() {
            document.getElementById('modal-barang-title').innerHTML = `<i class="fa-solid fa-box-open" style="color: var(--primary);"></i> Tambah Produk Baru`;
            document.getElementById('barang-id').value = '';
            document.getElementById('input-barang-sku').value = 'BRG-' + Math.floor(1000 + Math.random() * 9000);
            document.getElementById('input-barang-barcode').value = '';
            document.getElementById('input-barang-nama').value = '';
            document.getElementById('input-barang-beli').value = '';
            document.getElementById('input-barang-jual').value = '';
            document.getElementById('input-barang-stok').value = '10';
            document.getElementById('input-barang-satuan').value = 'pcs';
            clearGambarPreviewOnly();
            openModal('modal-barang');
        }

        // --- Gambar Upload Helpers ---
        function handleGambarChange(event) {
            const file = event.target.files[0];
            if (!file) return;
            setGambarPreview(URL.createObjectURL(file));
        }

        function handleGambarDrop(event) {
            event.preventDefault();
            document.getElementById('gambar-upload-area').style.borderColor = 'var(--light-border)';
            const file = event.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            // Assign to file input
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('input-barang-gambar').files = dt.files;
            setGambarPreview(URL.createObjectURL(file));
        }

        function setGambarPreview(src) {
            const img = document.getElementById('gambar-preview-img');
            const wrap = document.getElementById('gambar-preview-wrap');
            const placeholder = document.getElementById('gambar-upload-placeholder');
            img.src = src;
            wrap.style.display = 'inline-block';
            placeholder.style.display = 'none';
        }

        function clearGambarPreviewOnly() {
            const wrap = document.getElementById('gambar-preview-wrap');
            const placeholder = document.getElementById('gambar-upload-placeholder');
            const img = document.getElementById('gambar-preview-img');
            const fileInput = document.getElementById('input-barang-gambar');
            if (wrap) wrap.style.display = 'none';
            if (placeholder) placeholder.style.display = 'block';
            if (img) img.src = '';
            if (fileInput) fileInput.value = '';
        }

        function clearGambarInput(event) {
            event.stopPropagation();
            clearGambarPreviewOnly();
        }

        // Modal lightbox untuk lihat gambar produk penuh
        function showGambarModal(url, nama) {
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;';
            overlay.onclick = () => overlay.remove();
            overlay.innerHTML = `
                <img src="${url}" alt="${nama}" style="max-width:90vw;max-height:80vh;border-radius:12px;object-fit:contain;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
                <p style="color:white;font-size:15px;font-weight:600;">${nama}</p>
                <button onclick="this.parentElement.remove()" style="background:white;border:none;border-radius:8px;padding:8px 20px;cursor:pointer;font-weight:600;">Tutup</button>
            `;
            document.body.appendChild(overlay);
        }

        function openEditBarangModal(id) {
            const item = allProducts.find(p => p.id === id);
            if (!item) return;

            document.getElementById('modal-barang-title').innerHTML = `<i class="fa-solid fa-pen" style="color: var(--primary);"></i> Edit Produk`;
            document.getElementById('barang-id').value = item.id;
            document.getElementById('input-barang-sku').value = item.kode_sku;
            document.getElementById('input-barang-barcode').value = item.barcode || '';
            document.getElementById('input-barang-nama').value = item.nama_barang;
            document.getElementById('input-barang-kategori').value = item.kategori_id;
            document.getElementById('input-barang-beli').value = item.harga_beli;
            document.getElementById('input-barang-jual').value = item.harga_jual;
            document.getElementById('input-barang-stok').value = item.stok;
            document.getElementById('input-barang-satuan').value = item.satuan;

            // Tampilkan preview gambar yang sudah ada
            if (item.gambar_full_url) {
                setGambarPreview(item.gambar_full_url);
            } else {
                clearGambarPreviewOnly();
            }

            openModal('modal-barang');
        }

        async function handleSaveBarang(e) {
            e.preventDefault();
            const id = document.getElementById('barang-id').value;

            // Gunakan FormData agar bisa upload file gambar (multipart/form-data)
            const formData = new FormData();
            formData.append('kode_sku', document.getElementById('input-barang-sku').value);
            const barcode = document.getElementById('input-barang-barcode').value;
            if (barcode) formData.append('barcode', barcode);
            formData.append('nama_barang', document.getElementById('input-barang-nama').value);
            formData.append('kategori_id', document.getElementById('input-barang-kategori').value);
            formData.append('harga_beli', document.getElementById('input-barang-beli').value);
            formData.append('harga_jual', document.getElementById('input-barang-jual').value);
            formData.append('stok', document.getElementById('input-barang-stok').value || 0);
            formData.append('satuan', document.getElementById('input-barang-satuan').value);

            // Lampirkan file gambar jika ada
            const gambarFile = document.getElementById('input-barang-gambar').files[0];
            if (gambarFile) formData.append('gambar', gambarFile);

            // Selalu pakai POST (server mendukung POST /barang/{id} untuk update multipart)
            const url = id ? `${API_BASE}/barang/${id}` : `${API_BASE}/barang`;

            // Header: jangan set Content-Type — biarkan browser isi boundary otomatis
            const headers = { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' };

            try {
                const res = await fetch(url, { method: 'POST', headers, body: formData });
                const json = await res.json();
                if (json.status) {
                    showToast(json.message, 'success');
                    closeModal('modal-barang');
                    clearGambarPreviewOnly();
                    loadMasterBarang();
                    loadPosProducts();
                    loadDashboard();
                } else {
                    const errMsg = json.errors ? Object.values(json.errors).flat().join(', ') : (json.message || 'Periksa input');
                    showToast('Gagal menyimpan: ' + errMsg, 'error');
                }
            } catch (err) {
                console.error(err);
                showToast('Terjadi kesalahan jaringan', 'error');
            }
        }

        async function deleteBarang(id) {
            if (!confirm('Yakin ingin menghapus produk ini?')) return;

            try {
                const res = await fetch(`${API_BASE}/barang/${id}`, {
                    method: 'DELETE',
                    headers: apiHeaders()
                });
                const json = await res.json();
                if (json.status) {
                    showToast('Produk berhasil dihapus', 'success');
                    loadMasterBarang();
                    loadPosProducts();
                    loadDashboard();
                } else {
                    showToast(json.message, 'error');
                }
            } catch (err) {
                showToast('Gagal menghapus produk', 'error');
            }
        }

        // ============================================
        // 4. BELANJA BARANG / PURCHASING LOGIC
        // ============================================
        function loadBelanjaTab() {
            document.getElementById('belanja-tanggal').value = new Date().toISOString().split('T')[0];
            renderSupplierSelectOptions();
            if (document.getElementById('belanja-items-list').children.length === 0) {
                addBelanjaRow();
            }
            loadRiwayatBelanja();
        }

        function renderSupplierSelectOptions() {
            const select = document.getElementById('belanja-supplier-select');
            select.innerHTML = `<option value="">-- Pilih Supplier --</option>` +
                allSuppliers.map(s => `<option value="${s.id}">${s.nama_supplier}</option>`).join('');
        }

        function addBelanjaRow() {
            const container = document.getElementById('belanja-items-list');
            const row = document.createElement('div');
            row.className = 'belanja-item-row';
            row.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 8px; align-items: center; background: #f8fafc; padding: 8px; border-radius: 8px;';

            row.innerHTML = `
                <select class="form-control belanja-row-barang" required onchange="onBelanjaProductSelect(this)">
                    <option value="">-- Pilih Barang --</option>
                    ${allProducts.map(p => `<option value="${p.id}" data-price="${p.harga_beli}">${p.nama_barang} (Stok: ${p.stok})</option>`).join('')}
                </select>
                <input type="number" class="form-control belanja-row-qty" placeholder="Jumlah" min="1" value="10" required>
                <input type="number" class="form-control belanja-row-price" placeholder="Harga Beli Satuan" min="0" required>
                <button type="button" class="btn-sm-action danger" onclick="this.parentElement.remove()"><i class="fa-solid fa-trash"></i></button>
            `;
            container.appendChild(row);
        }

        function onBelanjaProductSelect(selectEl) {
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const defaultPrice = selectedOption.getAttribute('data-price') || 0;
            const priceInput = selectEl.parentElement.querySelector('.belanja-row-price');
            if (priceInput) priceInput.value = defaultPrice;
        }

        function quickReorder(productId) {
            switchTab('belanja');
            const container = document.getElementById('belanja-items-list');
            container.innerHTML = '';
            addBelanjaRow();

            setTimeout(() => {
                const firstRow = container.querySelector('.belanja-item-row');
                if (firstRow) {
                    const select = firstRow.querySelector('.belanja-row-barang');
                    select.value = productId;
                    onBelanjaProductSelect(select);
                }
            }, 100);
        }

        async function handleSimpanBelanja(e) {
            e.preventDefault();

            const rows = document.querySelectorAll('.belanja-item-row');
            if (rows.length === 0) {
                showToast('Tambahkan minimal 1 item belanjaan', 'error');
                return;
            }

            const items = [];
            rows.forEach(r => {
                const barangId = r.querySelector('.belanja-row-barang').value;
                const qty = Number(r.querySelector('.belanja-row-qty').value) || 0;
                const price = Number(r.querySelector('.belanja-row-price').value) || 0;

                if (barangId && qty > 0) {
                    items.push({ barang_id: Number(barangId), jumlah: qty, harga_beli_satuan: price });
                }
            });

            if (items.length === 0) {
                showToast('Mohon pilih barang yang dibelanjakan', 'error');
                return;
            }

            const payload = {
                supplier_id: Number(document.getElementById('belanja-supplier-select').value),
                no_faktur_pembelian: document.getElementById('belanja-no-faktur').value || null,
                tanggal: document.getElementById('belanja-tanggal').value,
                catatan: document.getElementById('belanja-catatan').value || null,
                items: items
            };

            try {
                const res = await fetch(`${API_BASE}/belanja`, {
                    method: 'POST',
                    headers: apiHeaders(),
                    body: JSON.stringify(payload)
                });
                const json = await res.json();
                if (json.status) {
                    showToast('Belanja berhasil disimpan dan stok otomatis bertambah!', 'success');
                    document.getElementById('form-belanja').reset();
                    document.getElementById('belanja-items-list').innerHTML = '';
                    addBelanjaRow();
                    loadRiwayatBelanja();
                    loadPosProducts();
                    loadDashboard();
                } else {
                    showToast('Gagal: ' + json.message, 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan jaringan', 'error');
            }
        }

        async function loadRiwayatBelanja() {
            try {
                const res = await fetch(`${API_BASE}/belanja`, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    const tbody = document.getElementById('riwayat-belanja-tbody');
                    const list = json.data.data || json.data;

                    if (list.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada riwayat belanja.</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = list.map(b => `
                        <tr>
                            <td><code>${b.no_faktur_pembelian}</code></td>
                            <td style="font-weight: 600;">${b.supplier?.nama_supplier || '-'}</td>
                            <td>${b.tanggal}</td>
                            <td style="font-weight: 700; color: #0284c7;">${formatRupiah(b.total_belanja)}</td>
                            <td><small>${b.details?.length || 0} macam barang</small></td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                console.error(e);
            }
        }

        // ============================================
        // 5. LAPORAN LABA RUGI & RIWAYAT NOTA
        // ============================================
        async function loadLabaRugiReport() {
            if (currentRole !== 'pemilik') {
                showToast('Menu laporan finansial hanya dapat diakses oleh Pemilik Toko.', 'error');
                return;
            }

            const startDate = document.getElementById('laporan-start-date').value;
            const endDate = document.getElementById('laporan-end-date').value;

            let url = `${API_BASE}/laporan/laba-rugi`;
            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (params.toString()) url += `?${params.toString()}`;

            try {
                const res = await fetch(url, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    const d = json.data;
                    document.getElementById('lap-val-penjualan').innerText = formatRupiah(d.pendapatan_penjualan.total_penjualan);
                    document.getElementById('lap-val-breakdown').innerText = 
                        `Tunai: ${formatRupiah(d.pendapatan_penjualan.metode_tunai)} | QRIS: ${formatRupiah(d.pendapatan_penjualan.metode_qris)}`;
                    
                    document.getElementById('lap-val-hpp').innerText = formatRupiah(d.pengeluaran_dan_hpp.hpp_barang_terjual);
                    document.getElementById('lap-val-laba').innerText = formatRupiah(d.laba_rugi.laba_kotor_penjualan);
                    document.getElementById('lap-val-belanja').innerText = formatRupiah(d.pengeluaran_dan_hpp.total_belanja_supplier);

                    loadRiwayatPenjualan();
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function loadRiwayatPenjualan() {
            try {
                const res = await fetch(`${API_BASE}/penjualan`, { headers: apiHeaders() });
                const json = await res.json();
                if (json.status) {
                    const tbody = document.getElementById('riwayat-penjualan-tbody');
                    const list = json.data.data || json.data;

                    if (list.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; color: var(--text-muted);">Belum ada riwayat transaksi penjualan.</td></tr>`;
                        return;
                    }

                    tbody.innerHTML = list.map(p => `
                        <tr>
                            <td><code>${p.no_nota}</code></td>
                            <td>${new Date(p.tanggal).toLocaleString('id-ID')}</td>
                            <td>${p.kasir?.name || 'Kasir'}</td>
                            <td><span class="role-badge ${p.metode_pembayaran === 'qris' ? 'role-pemilik' : 'role-kasir'}">${p.metode_pembayaran.toUpperCase()}</span></td>
                            <td style="font-weight: 700;">${formatRupiah(p.total_belanja)}</td>
                            <td>${formatRupiah(p.jumlah_bayar)}</td>
                            <td>${formatRupiah(p.kembalian)}</td>
                            <td>
                                <a href="${API_BASE}/penjualan/${p.id}/cetak-struk" target="_blank" class="btn-sm-action" style="color: var(--primary);">
                                    <i class="fa-solid fa-file-pdf"></i> Struk PDF
                                </a>
                            </td>
                        </tr>
                    `).join('');
                }
            } catch (e) {
                console.error(e);
            }
        }

        // Initialize Dates on load
        function initDates() {
            const today = new Date().toISOString().split('T')[0];
            const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
            document.getElementById('laporan-start-date').value = firstDay;
            document.getElementById('laporan-end-date').value = today;
        }

        // Initial Boot
        window.addEventListener('DOMContentLoaded', () => {
            initDates();
            checkAuthSession();
        });
    </script>
</body>
</html>
