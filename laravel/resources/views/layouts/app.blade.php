<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cabinet QHSE — @yield('titre', 'Tableau de bord')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.css" onerror="this.remove()">
    <style>
        :root {
            --c-projets: #6366f1;
            --c-clients: #06b6d4;
            --c-devis: #f59e0b;
            --c-factures: #10b981;
            --c-recouvrement: #ef4444;
            --c-audits: #8b5cf6;
            --c-formation: #ec4899;
            --c-documents: #64748b;
            --c-dashboard: #0ea5e9;
            --c-taches: #22c55e;
            --c-parametrage: #94a3b8;
            --bg-app: #f3f5f9;
            --sidebar-bg: #151c2c;
            --sidebar-bg-alt: #1b2438;
        }

        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        body { background: var(--bg-app); color:#1f2937; }

        .app-wrapper { display:flex; min-height:100vh; }

        .sidebar {
            width:252px; background:var(--sidebar-bg); color:#fff; flex-shrink:0;
            display:flex; flex-direction:column; box-shadow: 2px 0 12px rgba(0,0,0,.15);
        }
        .sidebar .brand {
            padding:24px 22px; font-weight:700; font-size:1.15rem; letter-spacing:.3px;
            border-bottom:1px solid rgba(255,255,255,.08); display:flex; align-items:center; gap:10px;
        }
        .sidebar .brand .dot { width:10px; height:10px; border-radius:50%; background:linear-gradient(135deg,#6366f1,#06b6d4); display:inline-block; }
        .sidebar .nav-links { flex:1; padding:14px 10px; overflow-y:auto; }
        .sidebar a {
            color:#aab4c8; display:flex; align-items:center; gap:12px; padding:10px 14px; margin:2px 0;
            text-decoration:none; border-radius:8px; font-size:.92rem; font-weight:500;
            border-left:3px solid transparent; transition: all .15s ease;
        }
        .sidebar a svg { width:17px; height:17px; flex-shrink:0; opacity:.85; }
        .sidebar a:hover { background:var(--sidebar-bg-alt); color:#fff; }
        .sidebar a.active { background:var(--sidebar-bg-alt); color:#fff; border-left-color: var(--accent); }
        .sidebar a.active svg, .sidebar a:hover svg { opacity:1; color: var(--accent); stroke: var(--accent); }

        .sidebar .logout-form { border-top:1px solid rgba(255,255,255,.08); padding:12px 10px; }
        .sidebar .logout-form button {
            background:none; border:none; color:#aab4c8; padding:10px 14px; width:100%;
            display:flex; align-items:center; gap:12px; text-align:left; cursor:pointer; font-size:.92rem; border-radius:8px;
        }
        .sidebar .logout-form button:hover { background:#3a1f28; color:#f87171; }

        .main-content { flex:1; padding:28px 32px; max-width:100%; }

        .section-header {
            display:flex; justify-content:space-between; align-items:center; margin-bottom:22px;
            padding-bottom:14px; border-bottom:1px solid #e5e9f0;
        }
        .section-header h1 { font-size:1.45rem; font-weight:700; color:#111827; margin:0; }
        .section-header-button { display:flex; gap:8px; flex-wrap:wrap; }

        .card {
            border:none; border-radius:12px; box-shadow: 0 1px 3px rgba(16,24,40,.06), 0 1px 2px rgba(16,24,40,.04);
            margin-bottom:22px;
        }
        .card-header { background:#fff; border-bottom:1px solid #eef0f4; border-radius:12px 12px 0 0 !important; font-weight:600; }
        .card-body { padding:22px; }

        .btn { border-radius:8px; font-weight:500; font-size:.9rem; padding:8px 16px; }
        .btn-sm { padding:5px 10px; font-size:.82rem; }
        .btn-primary { background:#4f46e5; border-color:#4f46e5; }
        .btn-primary:hover { background:#4338ca; border-color:#4338ca; }
        .btn-success { background:#10b981; border-color:#10b981; }
        .btn-light { background:#f3f4f6; border-color:#e5e7eb; color:#374151; }
        .btn-icon {
            padding:4px 6px; background:transparent; border:1px solid #e5e7eb; border-radius:6px;
            line-height:1; display:inline-flex; align-items:center; justify-content:center;
        }
        .btn-icon:hover { background:#f3f4f6; }
        .btn-icon svg { width:14px; height:14px; }
        td .btn-icon, td form.d-inline { margin-right:2px; }
        td form.d-inline:last-child, td .btn-icon:last-child { margin-right:0; }
        td .btn-icon, td form.d-inline > .btn-icon { vertical-align:middle; }

        .table thead th { border-top:none; border-bottom:2px solid #eef0f4; font-size:.8rem; text-transform:uppercase; letter-spacing:.03em; color:#6b7280; font-weight:600; }
        .table td { vertical-align:middle; font-size:.9rem; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #fafbfc; }
        .table-hover tbody tr:hover { background-color:#f3f4f6; }
        .table-danger, .table-danger > td { background-color:#fef2f2 !important; }
        .table-warning, .table-warning > td { background-color:#fffbeb !important; }

        .badge { font-weight:600; padding:5px 10px; border-radius:6px; font-size:.75rem; }
        .badge-info { background:#e0e7ff; color:#4338ca; }
        .badge-light { background:#f3f4f6; color:#4b5563; }

        .alert { border:none; border-radius:10px; font-size:.9rem; }
        .alert-success { background:#ecfdf5; color:#047857; }
        .alert-danger { background:#fef2f2; color:#b91c1c; }

        .form-control { border-radius:8px; border:1px solid #d1d5db; font-size:.9rem; }
        .form-control:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.15); }
        label { font-weight:600; font-size:.82rem; color:#374151; }

        /* --- Indicateurs (KPI) en haut de module --- */
        .kpi-row { display:flex; gap:16px; margin-bottom:22px; flex-wrap:wrap; }
        .kpi-card {
            flex:1; min-width:150px; background:#fff; border-radius:12px; padding:16px 20px;
            box-shadow:0 1px 3px rgba(16,24,40,.06); border-left:4px solid var(--accent, #6366f1);
        }
        .kpi-card .kpi-value { font-size:1.6rem; font-weight:700; color:#111827; line-height:1.2; }
        .kpi-card .kpi-label { font-size:.75rem; color:#6b7280; text-transform:uppercase; letter-spacing:.03em; font-weight:600; margin-top:2px; }

        /* --- Filtres sous forme de boutons --- */
        .filter-btns { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
        .filter-btns a { padding:6px 14px; border-radius:20px; background:#f3f4f6; color:#4b5563; font-size:.84rem; font-weight:600; text-decoration:none; }
        .filter-btns a.active { background:#4f46e5; color:#fff; }
        .filter-btns a:hover:not(.active) { background:#e5e7eb; }

        /* --- En-têtes de rubrique colorés (pour distinguer les sections d'une page) --- */
        .card-header-indigo { background:#eef2ff !important; color:#4338ca !important; border-bottom-color:#e0e7ff !important; }
        .card-header-amber  { background:#fffbeb !important; color:#b45309 !important; border-bottom-color:#fef3c7 !important; }
        .card-header-cyan   { background:#ecfeff !important; color:#0e7490 !important; border-bottom-color:#cffafe !important; }
        .card-header-green  { background:#f0fdf4 !important; color:#15803d !important; border-bottom-color:#dcfce7 !important; }
        .card-header-pink   { background:#fdf2f8 !important; color:#be185d !important; border-bottom-color:#fce7f3 !important; }
        .card-header-violet { background:#f5f3ff !important; color:#6d28d9 !important; border-bottom-color:#ede9fe !important; }
        .card-header-red    { background:#fef2f2 !important; color:#b91c1c !important; border-bottom-color:#fee2e2 !important; }

        /* --- Responsive / mobile --- */
        .mobile-topbar { display:none; }
        .sidebar-overlay { display:none; }

        @media (max-width: 900px) {
            .mobile-topbar {
                display:flex; align-items:center; justify-content:space-between;
                background:var(--sidebar-bg); color:#fff; padding:14px 18px; position:sticky; top:0; z-index:40;
            }
            .mobile-topbar .brand-mini { font-weight:700; display:flex; align-items:center; gap:8px; }
            .mobile-topbar button { background:none; border:none; color:#fff; font-size:1.4rem; line-height:1; padding:4px 8px; }
            .app-wrapper { flex-direction:column; }
            .sidebar {
                position:fixed; top:0; left:0; height:100vh; z-index:60; width:250px;
                transform:translateX(-100%); transition:transform .2s ease;
            }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.show { display:block; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:50; }
            .main-content { padding:16px; }
            .section-header { flex-direction:column; align-items:flex-start; gap:10px; }
            .section-header-button { width:100%; }
            .section-header-button .btn { flex:1; text-align:center; }
            .kpi-row { gap:10px; }
            .kpi-card { min-width: calc(50% - 10px); padding:12px 14px; }
            .kpi-card .kpi-value { font-size:1.25rem; }
            .card-body { overflow-x:auto; padding:16px; }
            .card-body table { min-width:600px; }
            form.form-inline { flex-direction:column; align-items:stretch !important; }
            form.form-inline > * { width:100% !important; margin:0 0 8px 0 !important; }
            .filter-btns { overflow-x:auto; flex-wrap:nowrap; padding-bottom:4px; }
            .filter-btns a { flex-shrink:0; }
        }

        @media (max-width: 480px) {
            .kpi-card { min-width:100%; }
        }
    </style>
</head>
<body>
    <div class="mobile-topbar">
        <div class="brand-mini"><span style="width:8px;height:8px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#06b6d4);display:inline-block;"></span> Cabinet QHSE</div>
        <button type="button" onclick="document.querySelector('.sidebar').classList.add('open'); document.querySelector('.sidebar-overlay').classList.add('show');">☰</button>
    </div>
    <div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('open'); this.classList.remove('show');"></div>

    <div class="app-wrapper">
        <nav class="sidebar">
            <div class="brand"><span class="dot"></span> Cabinet QHSE</div>
            <div class="nav-links">
                <a href="{{ route('projets.index') }}" style="--accent:var(--c-projets)" class="{{ request()->routeIs('projets.*') ? 'active' : '' }}"><i data-feather="briefcase"></i> Projets</a>
                <a href="{{ route('clients.index') }}" style="--accent:var(--c-clients)" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}"><i data-feather="users"></i> Clients</a>
                <a href="{{ route('devis.index') }}" style="--accent:var(--c-devis)" class="{{ request()->routeIs('devis.*') ? 'active' : '' }}"><i data-feather="file-text"></i> Devis</a>
                <a href="{{ route('factures.index') }}" style="--accent:var(--c-factures)" class="{{ request()->routeIs('factures.*') ? 'active' : '' }}"><i data-feather="file"></i> Factures</a>
                <a href="{{ route('recouvrement.index') }}" style="--accent:var(--c-recouvrement)" class="{{ request()->routeIs('recouvrement.*') ? 'active' : '' }}"><i data-feather="credit-card"></i> Recouvrement</a>
                <a href="{{ route('audits.index') }}" style="--accent:var(--c-audits)" class="{{ request()->routeIs('audits.*') || request()->routeIs('normes.*') ? 'active' : '' }}"><i data-feather="shield"></i> Audits</a>
                <a href="{{ route('formations.index') }}" style="--accent:var(--c-formation)" class="{{ request()->routeIs('formations.*') ? 'active' : '' }}"><i data-feather="book-open"></i> Formation</a>
                <a href="{{ route('documents.index') }}" style="--accent:var(--c-documents)" class="{{ request()->routeIs('documents.*') ? 'active' : '' }}"><i data-feather="folder"></i> Documents</a>
                <a href="{{ route('indicateurs.index') }}" style="--accent:var(--c-dashboard)" class="{{ request()->routeIs('indicateurs.*') ? 'active' : '' }}"><i data-feather="bar-chart-2"></i> Tableau de bord</a>
                <a href="{{ route('taches.index') }}" style="--accent:var(--c-taches)" class="{{ request()->routeIs('taches.*') ? 'active' : '' }}"><i data-feather="check-circle"></i> Tâches</a>
                <a href="{{ route('parametrage.index') }}" style="--accent:var(--c-parametrage)" class="{{ request()->routeIs('parametrage.*') || request()->routeIs('prestations.*') ? 'active' : '' }}"><i data-feather="settings"></i> Paramétrage</a>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit"><i data-feather="log-out"></i> Déconnexion</button>
            </form>
        </nav>

        <div class="main-content">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
    <script>if (window.feather) feather.replace({ width: 14, height: 14 });</script>
    <script>
        document.querySelectorAll('.sidebar a, .sidebar .logout-form button').forEach(function (el) {
            el.addEventListener('click', function () {
                document.querySelector('.sidebar').classList.remove('open');
                document.querySelector('.sidebar-overlay').classList.remove('show');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
