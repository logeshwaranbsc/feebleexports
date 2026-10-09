<div class="page-header">
    <div class="page-title-group">
        <a href="/admin/products" style="color: var(--admin-primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">← Back to Products</a>
        <h1 style="margin-top: 0.5rem;">Add New Product</h1>
    </div>
</div>

<div class="card-panel" style="padding: 2rem; max-width: 800px;">
    <form action="/admin/products/create" method="POST" class="admin-form">
        <div class="form-group">
            <label for="name">Product Name *</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g., Premium Tufted Coir Mat" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
            <?php if (!empty($errors['name'])): ?>
                <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="id">Product Identifier / Slug</label>
                <input type="text" id="id" name="id" class="form-control" placeholder="e.g., tufted-coir-mat" value="<?= htmlspecialchars($old['id'] ?? '') ?>">
                <?php if (!empty($errors['id'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['id']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" class="form-control" placeholder="Plain & Handloom, Backed Mats..." value="<?= htmlspecialchars($old['category'] ?? '') ?>" required>
                <?php if (!empty($errors['category'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['category']) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="image">Image Asset Path / URL</label>
                <input type="text" id="image" name="image" class="form-control" placeholder="/assets/images/01_plain_handloom.png" value="<?= htmlspecialchars($old['image'] ?? '/assets/images/01_plain_handloom.png') ?>">
            </div>

            <div class="form-group">
                <label for="status">Catalog Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active" <?= ($old['status'] ?? '') === 'active' ? 'selected' : '' ?>>🟢 Active</option>
                    <option value="draft" <?= ($old['status'] ?? '') === 'draft' ? 'selected' : '' ?>>⚪ Hidden / Draft</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="price">Regular Price (₹)</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" placeholder="e.g. 250.00" value="<?= htmlspecialchars($old['price'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="offer_price">Offer Price (₹) <span style="font-weight: normal; color: var(--admin-text-muted); font-size: 0.8rem;">(Optional - Strikes out old price)</span></label>
                <input type="number" step="0.01" id="offer_price" name="offer_price" class="form-control" placeholder="e.g. 199.00" value="<?= htmlspecialchars($old['offer_price'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Product Description</label>
            <textarea id="description" name="description" class="form-control" style="min-height: 120px;" placeholder="Detailed specification of coir bristles, weave quality, backing materials..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Save Product</button>
            <a href="/admin/products" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
