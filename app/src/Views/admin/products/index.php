<div class="page-header">
    <div class="page-title-group">
        <h1>Product Management</h1>
        <p>Manage coir mat styles, patterns, roll catalog, and descriptions</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="/admin/categories" class="btn-secondary">🏷️ Manage Categories</a>
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
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name & ID</th>
                    <th>Category</th>
                    <th>Price / Offer</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--admin-text-muted); padding: 3rem;">
                            No products found matching this category filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
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
                                <?php 
                                    $hasP = !empty($product['price']) && (float)$product['price'] > 0;
                                    $hasO = !empty($product['offer_price']) && (float)$product['offer_price'] > 0;
                                    $isOff = $hasO && $hasP && (float)$product['offer_price'] < (float)$product['price'];
                                ?>
                                <?php if ($isOff): ?>
                                    <div><span style="text-decoration: line-through; color: var(--admin-text-muted); font-size: 0.8rem;">₹<?= number_format((float)$product['price'], 0) ?></span></div>
                                    <div style="color: var(--admin-primary); font-weight: 700; font-size: 0.9rem;">₹<?= number_format((float)$product['offer_price'], 0) ?></div>
                                <?php elseif ($hasO): ?>
                                    <div style="color: var(--admin-primary); font-weight: 700; font-size: 0.9rem;">₹<?= number_format((float)$product['offer_price'], 0) ?></div>
                                <?php elseif ($hasP): ?>
                                    <div style="font-weight: 600; font-size: 0.9rem;">₹<?= number_format((float)$product['price'], 0) ?></div>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted); font-size: 0.8rem;">N/A</span>
                                <?php endif; ?>
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
</div>
