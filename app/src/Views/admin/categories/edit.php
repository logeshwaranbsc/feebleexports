<div class="page-header">
    <div class="page-title-group">
        <a href="/admin/categories" style="color: var(--admin-primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">← Back to Categories</a>
        <h1 style="margin-top: 0.5rem;">Edit Category: <?= htmlspecialchars($category['name']) ?></h1>
        <p>Update category information and review mapped products</p>
    </div>
    <div>
        <a href="/admin/products/create" class="btn-primary">+ Add Product to this Category</a>
    </div>
</div>

<?php if (!empty($flash_success)): ?>
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--admin-primary); color: var(--admin-primary); padding: 0.85rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500;">
        ✓ <?= htmlspecialchars($flash_success) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 1.5rem; align-items: start;">
    
    <!-- Left Column: Edit Form -->
    <div class="card-panel" style="padding: 2rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--admin-primary); border-bottom: 1px solid var(--admin-border); padding-bottom: 0.5rem;">
            Category Details
        </h2>

        <form action="/admin/categories/<?= urlencode($category['id']) ?>/edit" method="POST" class="admin-form">
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
                <?php if (!empty($errors['name'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
                <?php endif; ?>
            </div>

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.25rem;">
                <div class="form-group">
                    <label for="slug">Category Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control" value="<?= htmlspecialchars($category['slug']) ?>" required>
                    <?php if (!empty($errors['slug'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['slug']) ?></span>
                    <?php endif; ?>
                    <small style="color: var(--admin-text-muted); font-size: 0.78rem; display: block; margin-top: 0.25rem;">
                        Updating slug automatically updates mapped products.
                    </small>
                </div>

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" id="display_order" name="display_order" class="form-control" value="<?= htmlspecialchars($category['display_order'] ?? 0) ?>" min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1.75rem; border-top: 1px solid var(--admin-border); padding-top: 1.25rem;">
                <button type="submit" class="btn-primary">
                    ✓ Save Changes
                </button>
                <a href="/admin/categories" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Right Column: Mapped Products -->
    <div class="card-panel" style="padding: 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border); padding-bottom: 0.5rem;">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--admin-text-main); margin: 0;">
                Mapped Products (<?= count($mappedProducts) ?>)
            </h2>
            <a href="/admin/products?category=<?= urlencode($category['slug']) ?>" style="font-size: 0.8rem; color: var(--admin-primary); text-decoration: underline;">
                View in Catalog
            </a>
        </div>

        <?php if (empty($mappedProducts)): ?>
            <div style="text-align: center; padding: 2rem 1rem; color: var(--admin-text-muted);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📦</div>
                <p style="font-size: 0.9rem; margin-bottom: 1rem;">No products are currently mapped to this category.</p>
                <a href="/admin/products/create" class="btn-secondary btn-sm">+ Map a New Product</a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 480px; overflow-y: auto;">
                <?php foreach ($mappedProducts as $p): ?>
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--admin-border); border-radius: 6px;">
                        <img src="<?= htmlspecialchars($p['image'] ?? '/assets/images/01_plain_handloom.png') ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid var(--admin-border);">
                        <div style="flex-grow: 1; min-width: 0;">
                            <div style="font-weight: 600; font-size: 0.88rem; color: var(--admin-text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($p['name']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--admin-text-muted);">
                                ID: <code><?= htmlspecialchars($p['id']) ?></code>
                            </div>
                        </div>
                        <a href="/admin/products/<?= urlencode($p['id']) ?>/edit" class="btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            Edit
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
