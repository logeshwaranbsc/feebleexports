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

        <!-- Multi-Currency Pricing Section (Rupees ₹, Dollar $, Pound £) -->
        <div class="pricing-card-section" style="margin: 1.5rem 0; padding: 1.25rem; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--admin-border); border-radius: 10px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--admin-text-main); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <span>🌐</span> Multi-Currency Pricing (Export & Domestic)
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--admin-text-muted); margin: 0.25rem 0 0 0;">
                        Enter prices for Indian Rupees (₹), US Dollars ($), and British Pounds (£). Website visitors can toggle between these currencies.
                    </p>
                </div>
                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: var(--admin-primary); border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.75rem; padding: 0.3rem 0.6rem; border-radius: 6px;">
                    3 Currencies (₹, $, £)
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                
                <!-- 1. Indian Rupee (₹ INR) -->
                <div class="currency-pricing-box" style="background: rgba(15, 23, 42, 0.55); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: #f59e0b; display: flex; align-items: center; gap: 0.4rem;">
                            <span>🇮🇳</span> Rupees (₹ INR)
                        </span>
                        <span style="font-size: 0.7rem; color: var(--admin-text-muted);">Domestic</span>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label for="price" style="font-size: 0.8rem;">Regular Price (₹)</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-weight: 600;">₹</span>
                            <input type="number" step="0.01" id="price" name="price" class="form-control" placeholder="250.00" value="<?= htmlspecialchars($product['price'] ?? '') ?>" style="padding-left: 2rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="offer_price" style="font-size: 0.8rem;">Offer Price (₹) <span style="font-size: 0.72rem; color: var(--admin-text-muted);">(Optional)</span></label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-weight: 600;">₹</span>
                            <input type="number" step="0.01" id="offer_price" name="offer_price" class="form-control" placeholder="199.00" value="<?= htmlspecialchars($product['offer_price'] ?? '') ?>" style="padding-left: 2rem;">
                        </div>
                    </div>
                </div>

                <!-- 2. US Dollar ($ USD) -->
                <div class="currency-pricing-box" style="background: rgba(15, 23, 42, 0.55); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: #38bdf8; display: flex; align-items: center; gap: 0.4rem;">
                            <span>🇺🇸</span> Dollar ($ USD)
                        </span>
                        <span style="font-size: 0.7rem; color: var(--admin-text-muted);">Global Export</span>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label for="price_usd" style="font-size: 0.8rem;">Regular Price ($)</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-weight: 600;">$</span>
                            <input type="number" step="0.01" id="price_usd" name="price_usd" class="form-control" placeholder="4.50" value="<?= htmlspecialchars($product['price_usd'] ?? '') ?>" style="padding-left: 2rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="offer_price_usd" style="font-size: 0.8rem;">Offer Price ($) <span style="font-size: 0.72rem; color: var(--admin-text-muted);">(Optional)</span></label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-weight: 600;">$</span>
                            <input type="number" step="0.01" id="offer_price_usd" name="offer_price_usd" class="form-control" placeholder="3.50" value="<?= htmlspecialchars($product['offer_price_usd'] ?? '') ?>" style="padding-left: 2rem;">
                        </div>
                    </div>
                </div>

                <!-- 3. British Pound (£ GBP) -->
                <div class="currency-pricing-box" style="background: rgba(15, 23, 42, 0.55); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: #a78bfa; display: flex; align-items: center; gap: 0.4rem;">
                            <span>🇬🇧</span> Pound (£ GBP)
                        </span>
                        <span style="font-size: 0.7rem; color: var(--admin-text-muted);">UK & Europe</span>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label for="price_gbp" style="font-size: 0.8rem;">Regular Price (£)</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-weight: 600;">£</span>
                            <input type="number" step="0.01" id="price_gbp" name="price_gbp" class="form-control" placeholder="3.50" value="<?= htmlspecialchars($product['price_gbp'] ?? '') ?>" style="padding-left: 2rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="offer_price_gbp" style="font-size: 0.8rem;">Offer Price (£) <span style="font-size: 0.72rem; color: var(--admin-text-muted);">(Optional)</span></label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-weight: 600;">£</span>
                            <input type="number" step="0.01" id="offer_price_gbp" name="offer_price_gbp" class="form-control" placeholder="2.75" value="<?= htmlspecialchars($product['offer_price_gbp'] ?? '') ?>" style="padding-left: 2rem;">
                        </div>
                    </div>
                </div>

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
