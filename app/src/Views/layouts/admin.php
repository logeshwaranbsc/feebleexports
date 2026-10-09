<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'FEEBLE EXPORTS - Admin Panel') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="admin-body">

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="/admin" class="sidebar-logo">FEEBLE EXPORTS</a>
            <span class="sidebar-badge">ADMIN</span>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-section-title">Main Menu</span>
            
            <a href="/admin" class="admin-nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>

            <a href="/admin/enquiries" class="admin-nav-item <?= ($currentPage ?? '') === 'enquiries' ? 'active' : '' ?>">
                <span class="nav-icon">📩</span>
                <span>Enquiries</span>
            </a>

            <a href="/admin/blogs" class="admin-nav-item <?= ($currentPage ?? '') === 'blogs' ? 'active' : '' ?>">
                <span class="nav-icon">📝</span>
                <span>Blogs</span>
            </a>

            <a href="/admin/products" class="admin-nav-item <?= ($currentPage ?? '') === 'products' ? 'active' : '' ?>">
                <span class="nav-icon">📦</span>
                <span>Products</span>
            </a>

            <a href="/admin/uploads" class="admin-nav-item <?= ($currentPage ?? '') === 'uploads' ? 'active' : '' ?>">
                <span class="nav-icon">☁️</span>
                <span>Media & Uploads</span>
            </a>

            <span class="nav-section-title">Settings</span>

            <a href="/admin/seo" class="admin-nav-item <?= ($currentPage ?? '') === 'seo' ? 'active' : '' ?>">
                <span class="nav-icon">🔍</span>
                <span>SEO Management</span>
            </a>

            <a href="/admin/profile" class="admin-nav-item <?= ($currentPage ?? '') === 'profile' ? 'active' : '' ?>">
                <span class="nav-icon">👤</span>
                <span>Profile / Account</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-user-card">
                <div class="user-avatar">
                    <?= strtoupper(substr($admin['name'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($admin['name'] ?? 'Kavimayil V.') ?></span>
                    <span class="user-role"><?= htmlspecialchars($admin['role'] ?? 'Proprietress') ?></span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="mobile-sidebar-toggle" id="sidebarToggle">☰</button>
                <span class="topbar-title">Control Center</span>
            </div>

            <div class="topbar-actions">
                <a href="/" target="_blank" class="btn-secondary btn-sm" title="View Public Website">
                    🌐 View Site
                </a>
                <a href="/admin/logout" class="btn-admin-logout">
                    🚪 Logout
                </a>
            </div>
        </header>

        <!-- Main View Outlet -->
        <main class="admin-content">
            <?php 
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
            ?>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert-banner alert-success">
                    <span>✓</span>
                    <div><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert-banner alert-danger">
                    <span>⚠️</span>
                    <div><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <!-- Admin JS -->
    <script src="/js/admin.js"></script>
</body>
</html>
