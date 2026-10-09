<div class="page-header">
    <div class="page-title-group">
        <h1>Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($admin['name'] ?? 'Admin') ?> 👋 | FEEBLE EXPORTS Management Overview</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green">📩</div>
        <div>
            <div class="stat-value"><?= $enquiryStats['total'] ?></div>
            <div class="stat-label">Total Enquiries</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon gold">⚡</div>
        <div>
            <div class="stat-value"><?= $enquiryStats['new'] ?></div>
            <div class="stat-label">New Unread Leads</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">📦</div>
        <div>
            <div class="stat-value"><?= $totalProducts ?></div>
            <div class="stat-label">Coir Products</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">📝</div>
        <div>
            <div class="stat-value"><?= $totalBlogs ?></div>
            <div class="stat-label">Blog Articles</div>
        </div>
    </div>
</div>

<!-- Recent Enquiries Section -->
<div class="card-panel">
    <div class="panel-header">
        <div class="panel-title">
            <span>📩</span>
            <span>Recent Inquiries & Quote Requests</span>
        </div>
        <a href="/admin/enquiries" class="btn-secondary btn-sm">View All Enquiries</a>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentEnquiries)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--admin-text-muted); padding: 2rem;">
                            No inquiries recorded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentEnquiries as $enquiry): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($enquiry['id']) ?></strong></td>
                            <td>
                                <span class="badge <?= $enquiry['type'] === 'quote' ? 'badge-quote' : 'badge-contact' ?>">
                                    <?= htmlspecialchars($enquiry['type_label']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($enquiry['name']) ?></td>
                            <td><?= htmlspecialchars($enquiry['email']) ?></td>
                            <td><?= htmlspecialchars($enquiry['country'] ?: 'N/A') ?></td>
                            <td><?= date('M d, Y H:i', strtotime($enquiry['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($enquiry['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($enquiry['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/enquiries/<?= $enquiry['id'] ?>" class="btn-secondary btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions Panel -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
    <div class="card-panel" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--admin-primary);">📦 Product Catalog</h3>
        <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 1.25rem;">Add or modify coir mats, tufted patterns, and custom roll collections.</p>
        <div style="display: flex; gap: 0.75rem;">
            <a href="/admin/products/create" class="btn-primary btn-sm">+ New Product</a>
            <a href="/admin/products" class="btn-secondary btn-sm">Manage Catalog</a>
        </div>
    </div>

    <div class="card-panel" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #a855f7;">📝 Export Blog</h3>
        <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 1.25rem;">Publish industry insights, export announcements, and eco trends.</p>
        <div style="display: flex; gap: 0.75rem;">
            <a href="/admin/blogs/create" class="btn-primary btn-sm" style="background-color: #8b5cf6;">+ New Blog</a>
            <a href="/admin/blogs" class="btn-secondary btn-sm">Manage Blogs</a>
        </div>
    </div>

    <div class="card-panel" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: #3b82f6;">🔍 SEO Metadata</h3>
        <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 1.25rem;">Optimize page titles, descriptions, and OpenGraph tags for search engines.</p>
        <div style="display: flex; gap: 0.75rem;">
            <a href="/admin/seo" class="btn-secondary btn-sm">Configure SEO</a>
        </div>
    </div>
</div>
