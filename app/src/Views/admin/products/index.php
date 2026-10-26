<div class="page-header">
    <div class="page-title-group">
        <h1>Product Management</h1>
        <p>Manage coir mat styles, patterns, roll catalog, and descriptions</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
        <a href="/admin/categories" class="btn-secondary">🏷️ Manage Categories</a>
        <?php if (!empty($products)): ?>
            <form action="/admin/products/delete-all" method="POST" style="display: inline; margin: 0;" onsubmit="return confirm('⚠️ WARNING: Are you sure you want to delete ALL products from your catalog? This cannot be undone.');">
                <button type="submit" class="btn-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid var(--admin-danger); color: #ef4444;">
                    🗑️ Delete All Products
                </button>
            </form>
        <?php endif; ?>
        <a href="/admin/products/create" class="btn-primary">+ Add New Product</a>
    </div>
</div>

<?php if (!empty($flash_success)): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--admin-primary); color: var(--admin-primary); padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
        ✓ <?= htmlspecialchars($flash_success) ?>
    </div>
<?php endif; ?>

<!-- Category Filter Pills -->
<div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.25rem; align-items: center;">
    <span style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase; margin-right: 0.25rem;">Filter by Category:</span>
    <a href="/admin/products" class="badge" style="text-decoration: none; padding: 6px 12px; font-size: 0.82rem; <?= empty($selectedCategory) || $selectedCategory === 'all' ? 'background: var(--admin-primary); color: #000; font-weight: 700;' : 'background: rgba(255,255,255,0.05); color: var(--admin-text-muted);' ?>">
        All (<?= $allProductsCount ?? count($products) ?>)
    </a>
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
            <?php if ($cat['slug'] === 'all') continue; ?>
            <a href="/admin/products?category=<?= urlencode($cat['slug']) ?>" class="badge" style="text-decoration: none; padding: 6px 12px; font-size: 0.82rem; <?= ($selectedCategory ?? '') === $cat['slug'] ? 'background: var(--admin-primary); color: #000; font-weight: 700;' : 'background: rgba(255,255,255,0.05); color: var(--admin-text-muted);' ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="card-panel">
    <!-- Bulk Actions Toolbar (shows when items are checked) -->
    <form id="bulkDeleteForm" action="/admin/products/bulk-delete" method="POST">
        <div id="bulkActionsBar" style="display: none; align-items: center; justify-content: space-between; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <span style="font-size: 0.9rem; color: var(--admin-text-main); font-weight: 600;">
                <span id="selectedCount">0</span> product(s) selected
            </span>
            <button type="submit" class="btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete the selected products?');">
                🗑️ Delete Selected
            </button>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAllCheckbox" title="Select all products" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--admin-primary);">
                        </th>
                        <th>Image</th>
                        <th>Product Name & ID</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--admin-text-muted); padding: 3.5rem 1rem;">
                                <div style="font-size: 2.2rem; margin-bottom: 0.75rem;">📦</div>
                                <h3 style="font-size: 1.15rem; color: var(--admin-text-main); margin-bottom: 0.5rem;">No Products Found</h3>
                                <p style="font-size: 0.88rem; max-width: 420px; margin: 0 auto 1.5rem auto;">
                                    <?= !empty($selectedCategory) && $selectedCategory !== 'all' ? 'No products match this category filter.' : 'Your product catalog is currently empty. You can add new products or restore the sample catalog.' ?>
                                </p>
                                <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                                    <a href="/admin/products/create" class="btn-primary">+ Add New Product</a>
                                    <?php if (empty($selectedCategory) || $selectedCategory === 'all'): ?>
                                        <button type="submit" form="resetDefaultsForm" class="btn-secondary">🔄 Restore Sample Products</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="selected_ids[]" value="<?= htmlspecialchars($product['id']) ?>" class="product-select-checkbox" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--admin-primary);">
                                </td>
                                <td style="width: 70px;">
                                    <img src="<?= htmlspecialchars($product['image'] ?? '/assets/images/01_plain_handloom.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border);">
                                </td>
                                <td>
                                    <div>
                                        <strong style="font-size: 0.95rem; color: var(--admin-text-main);"><?= htmlspecialchars($product['name']) ?></strong>
                                        <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 0.15rem;">
                                            ID: <code><?= htmlspecialchars($product['id']) ?></code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="/admin/products?category=<?= urlencode($product['category_slug'] ?? '') ?>" title="Filter by this category" style="text-decoration: none;">
                                        <span class="badge badge-contact">
                                            <?= htmlspecialchars($product['category']) ?>
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-<?= ($product['status'] ?? 'active') === 'active' ? 'published' : 'draft' ?>">
                                        <?= ucfirst(htmlspecialchars($product['status'] ?? 'active')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="max-width: 300px; font-size: 0.82rem; color: var(--admin-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= htmlspecialchars($product['description'] ?? '') ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.4rem;">
                                        <a href="/admin/products/<?= $product['id'] ?>/edit" class="btn-secondary btn-sm">Edit</a>
                                        <form action="/admin/products/<?= $product['id'] ?>/delete" method="POST" class="form-delete-confirm" data-item-name="product">
                                            <button type="submit" class="btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Hidden form for resetting default products -->
    <form id="resetDefaultsForm" action="/admin/products/reset-defaults" method="POST" style="display: none;" onsubmit="return confirm('Restore all default sample coir products?');"></form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.product-select-checkbox');
    const bulkBar = document.getElementById('bulkActionsBar');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.product-select-checkbox:checked');
        if (selectedCount) {
            selectedCount.textContent = checked.length;
        }
        if (bulkBar) {
            bulkBar.style.display = checked.length > 0 ? 'flex' : 'none';
        }
        if (selectAll && checkboxes.length > 0) {
            selectAll.checked = checked.length === checkboxes.length;
            selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });
});
</script>
