<div class="page-header">
    <div class="page-title-group">
        <a href="/admin/categories" style="color: var(--admin-primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">← Back to Categories</a>
        <h1 style="margin-top: 0.5rem;">Add New Category</h1>
        <p>Create a product category to group and filter coir mats across the catalog</p>
    </div>
</div>

<div class="card-panel" style="padding: 2rem; max-width: 700px;">
    <form action="/admin/categories/create" method="POST" class="admin-form">
        <div class="form-group">
            <label for="name">Category Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Geotextiles & Erosion Control" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required autofocus>
            <?php if (!empty($errors['name'])): ?>
                <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
            <?php endif; ?>
            <small style="color: var(--admin-text-muted); font-size: 0.78rem; display: block; margin-top: 0.25rem;">
                This label will be shown in the public product filter tabs and product detail cards.
            </small>
        </div>

        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="slug">Category Slug (URL Identifier)</label>
                <input type="text" id="slug" name="slug" class="form-control" placeholder="e.g. geotextiles-erosion-control" value="<?= htmlspecialchars($old['slug'] ?? '') ?>">
                <?php if (!empty($errors['slug'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['slug']) ?></span>
                <?php endif; ?>
                <small style="color: var(--admin-text-muted); font-size: 0.78rem; display: block; margin-top: 0.25rem;">
                    Leave blank to auto-generate from Category Name.
                </small>
            </div>

            <div class="form-group">
                <label for="display_order">Display Order</label>
                <input type="number" id="display_order" name="display_order" class="form-control" placeholder="1, 2, 3..." value="<?= htmlspecialchars($old['display_order'] ?? '10') ?>" min="0">
                <small style="color: var(--admin-text-muted); font-size: 0.78rem; display: block; margin-top: 0.25rem;">
                    Lower numbers appear first.
                </small>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description (Optional)</label>
            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief summary of products falling under this category..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1.75rem; border-top: 1px solid var(--admin-border); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary">
                ✓ Create Category
            </button>
            <a href="/admin/categories" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
