<div class="page-header">
    <div class="page-title-group">
        <a href="/admin/blogs" style="color: var(--admin-primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">← Back to Blogs</a>
        <h1 style="margin-top: 0.5rem;">Edit Blog Post</h1>
    </div>
</div>

<div class="card-panel" style="padding: 2rem; max-width: 900px;">
    <form action="/admin/blogs/<?= $blog['id'] ?>/edit" method="POST" class="admin-form">
        <div class="form-group">
            <label for="title">Blog Title *</label>
            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($blog['title']) ?>" required>
            <?php if (!empty($errors['title'])): ?>
                <span class="form-error"><?= htmlspecialchars($errors['title']) ?></span>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="slug">URL Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" value="<?= htmlspecialchars($blog['slug']) ?>">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category" class="form-control" value="<?= htmlspecialchars($blog['category'] ?? 'Coir Industry') ?>">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <div class="form-group">
                <label for="author">Author Name</label>
                <input type="text" id="author" name="author" class="form-control" value="<?= htmlspecialchars($blog['author'] ?? 'Kavimayil Venkatachalam') ?>">
            </div>

            <div class="form-group">
                <label for="status">Publication Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="published" <?= ($blog['status'] ?? '') === 'published' ? 'selected' : '' ?>>🟢 Published</option>
                    <option value="draft" <?= ($blog['status'] ?? '') === 'draft' ? 'selected' : '' ?>>⚪ Draft</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="image">Featured Image Path / URL</label>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="text" id="image" name="image" class="form-control" value="<?= htmlspecialchars($blog['image'] ?? '/assets/images/01_plain_handloom.png') ?>">
                <label class="btn-secondary" style="margin: 0; cursor: pointer; white-space: nowrap; font-size: 0.85rem; padding: 0.5rem 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;" title="Upload image directly to Supabase S3">
                    <span>☁️ Upload S3</span>
                    <input type="file" accept="image/*" class="admin-s3-upload-input" data-target="#image" data-preview="#image-preview" style="display: none;">
                </label>
            </div>
            <div class="upload-status-msg" style="display: none; font-size: 0.8rem; margin-top: 0.35rem; color: var(--admin-primary);"></div>
            <div class="image-preview-wrapper" style="margin-top: 0.5rem;">
                <img id="image-preview" src="<?= htmlspecialchars($blog['image'] ?? '/assets/images/01_plain_handloom.png') ?>" alt="Preview" style="max-height: 80px; border-radius: 6px; border: 1px solid var(--admin-border); object-fit: cover; display: block;" onerror="this.style.display='none'" onload="this.style.display='block'">
            </div>
        </div>

        <div class="form-group">
            <label for="excerpt">Short Excerpt / Summary</label>
            <textarea id="excerpt" name="excerpt" class="form-control" style="min-height: 80px;"><?= htmlspecialchars($blog['excerpt'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="content">Full Article Content *</label>
            <textarea id="content" name="content" class="form-control" style="min-height: 220px;" required><?= htmlspecialchars($blog['content'] ?? '') ?></textarea>
            <?php if (!empty($errors['content'])): ?>
                <span class="form-error"><?= htmlspecialchars($errors['content']) ?></span>
            <?php endif; ?>
        </div>

        <hr style="border-color: var(--admin-border); margin: 1rem 0;">
        <h3 style="font-size: 1.05rem; margin-bottom: 0.5rem; color: var(--admin-primary);">Search Engine Optimization (SEO)</h3>

        <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" class="form-control" value="<?= htmlspecialchars($blog['meta_title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea id="meta_description" name="meta_description" class="form-control" style="min-height: 70px;"><?= htmlspecialchars($blog['meta_description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <button type="submit" class="btn-primary">Update Blog Post</button>
            <a href="/admin/blogs" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
