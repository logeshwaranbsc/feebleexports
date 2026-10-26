<div class="page-header">
    <div class="page-title-group">
        <a href="/admin/products" style="color: var(--admin-primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">← Back to Products</a>
        <h1 style="margin-top: 0.5rem;">Edit Product: <?= htmlspecialchars($product['name']) ?></h1>
    </div>
</div>

<div class="card-panel" style="padding: 2rem; max-width: 800px;">
    <form action="/admin/products/<?= $product['id'] ?>/edit" method="POST" class="admin-form">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
            <?php if (!empty($errors['name'])): ?>
                <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="id">Product Identifier (Read-Only)</label>
                <input type="text" id="id" class="form-control" value="<?= htmlspecialchars($product['id']) ?>" disabled>
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <label for="category_slug" style="margin: 0;">Map to Category *</label>
                    <a href="/admin/categories/create" target="_blank" style="font-size: 0.78rem; color: var(--admin-primary); text-decoration: underline;">+ Add New Category</a>
                </div>
                <select id="category_slug" name="category_slug" class="form-control" required>
                    <option value="">-- Select Category to Map --</option>
                    <?php foreach ($categories as $cat): ?>
                        <?php if ($cat['slug'] === 'all') continue; ?>
                        <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= (($product['category_slug'] ?? '') === $cat['slug'] || ($product['category'] ?? '') === $cat['name'] || ($product['category_id'] ?? '') === $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($errors['category'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['category']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="image">Image Asset Path / URL</label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <input type="text" id="image" name="image" class="form-control" value="<?= htmlspecialchars($product['image'] ?? '/assets/images/01_plain_handloom.png') ?>">
                    <label class="btn-secondary" style="margin: 0; cursor: pointer; white-space: nowrap; font-size: 0.85rem; padding: 0.5rem 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;" title="Upload image to server storage">
                        <span>📁 Upload Image</span>
                        <input type="file" accept="image/*" class="admin-file-upload-input" data-target="#image" data-preview="#image-preview" style="display: none;">
                    </label>
                </div>
                <div class="upload-status-msg" style="display: none; font-size: 0.8rem; margin-top: 0.35rem; color: var(--admin-primary);"></div>
                <div class="image-preview-wrapper" style="margin-top: 0.5rem;">
                    <img id="image-preview" src="<?= htmlspecialchars($product['image'] ?? '/assets/images/01_plain_handloom.png') ?>" alt="Preview" style="max-height: 80px; border-radius: 6px; border: 1px solid var(--admin-border); object-fit: cover; display: block;" onerror="this.style.display='none'" onload="this.style.display='block'">
                </div>
            </div>

            <div class="form-group">
                <label for="status">Catalog Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>🟢 Active</option>
                    <option value="draft" <?= ($product['status'] ?? '') === 'draft' ? 'selected' : '' ?>>⚪ Hidden / Draft</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Product Description</label>
            <textarea id="description" name="description" class="form-control" style="min-height: 120px;"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Update Product</button>
            <a href="/admin/products" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
