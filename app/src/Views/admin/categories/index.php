<div class="page-header">
    <div class="page-title-group">
        <h1>Category Management</h1>
        <p>Define product categories and manage product catalog mappings</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="/admin/products" class="btn-secondary">📦 View All Products (<?= $totalProducts ?>)</a>
        <a href="/admin/categories/create" class="btn-primary">+ Add New Category</a>
    </div>
</div>

<?php if (!empty($flash_success)): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--admin-primary); color: var(--admin-primary); padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
        ✓ <?= htmlspecialchars($flash_success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($flash_error)): ?>
    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid var(--admin-danger); color: var(--admin-danger); padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
        ⚠️ <?= htmlspecialchars($flash_error) ?>
    </div>
<?php endif; ?>

<!-- Quick Summary Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
    <div class="card-panel" style="padding: 1.25rem;">
        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700; letter-spacing: 0.5px;">Active Categories</span>
        <div style="font-size: 1.8rem; font-weight: 800; color: var(--admin-text-main); margin-top: 0.25rem;">
            <?= count($categories) ?>
        </div>
    </div>
    <div class="card-panel" style="padding: 1.25rem;">
        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700; letter-spacing: 0.5px;">Mapped Products</span>
        <div style="font-size: 1.8rem; font-weight: 800; color: var(--admin-primary); margin-top: 0.25rem;">
            <?= $totalProducts ?>
        </div>
    </div>
    <div class="card-panel" style="padding: 1.25rem;">
        <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700; letter-spacing: 0.5px;">Filter Sync</span>
        <div style="font-size: 0.9rem; font-weight: 600; color: #10b981; margin-top: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
            <span>🟢</span> Synchronized with Public Catalog
        </div>
    </div>
</div>

<div class="card-panel">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Order</th>
                    <th>Category Name & Slug</th>
                    <th>Description</th>
                    <th style="text-align: center;">Mapped Products</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--admin-text-muted); padding: 3rem;">
                            No categories found. Click "+ Add New Category" to create your first one.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: var(--admin-text-muted); font-size: 0.9rem;">
                                    #<?= htmlspecialchars($cat['display_order'] ?? 0) ?>
                                </span>
                            </td>
                            <td>
                                <div>
                                    <strong style="font-size: 1rem; color: var(--admin-text-main);">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </strong>
                                    <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 0.15rem;">
                                        Slug: <code style="background: rgba(0,0,0,0.25); padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($cat['slug']) ?></code>
                                    </div>
                                </div>
                            </td>
                            <td style="max-width: 340px;">
                                <div style="font-size: 0.85rem; color: var(--admin-text-muted); line-height: 1.4;">
                                    <?= htmlspecialchars($cat['description'] ?: 'No description provided') ?>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <a href="/admin/products?category=<?= urlencode($cat['slug']) ?>" title="View products in this category" style="text-decoration: none;">
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--admin-primary); border: 1px solid rgba(16, 185, 129, 0.3); font-weight: 700; padding: 4px 10px;">
                                        📦 <?= (int)($cat['product_count'] ?? 0) ?> Products
                                    </span>
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="/admin/products?category=<?= urlencode($cat['slug']) ?>" class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" title="View Products in this category">
                                        View Products
                                    </a>
                                    <a href="/admin/categories/<?= urlencode($cat['id']) ?>/edit" class="btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;">
                                        Edit
                                    </a>
                                    <form action="/admin/categories/<?= urlencode($cat['id']) ?>/delete" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'?');">
                                        <button type="submit" class="btn-danger" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;" <?= ($cat['product_count'] ?? 0) > 0 ? 'title="Has mapped products"' : '' ?>>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
