<div class="page-header">
    <div class="page-title-group">
        <h1>SEO & Meta Tag Management</h1>
        <p>Configure search engine titles, descriptions, and OpenGraph sharing tags for each page</p>
    </div>
</div>

<form action="/admin/seo" method="POST">
    <?php 
        $pages = [
            'home' => 'Home Page (/)',
            'discover' => 'Discover Us (/discover)',
            'products' => 'Products Collection (/products)',
            'story' => 'Our Story (/our-story)',
            'contact' => 'Contact Us (/contact)',
            'blogs' => 'Blogs & Insights (/blogs)'
        ];
    ?>

    <?php foreach ($pages as $key => $title): ?>
        <?php $pageSeo = $seoData[$key] ?? []; ?>
        <div class="card-panel" style="padding: 1.75rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--admin-primary); margin-bottom: 1rem; border-bottom: 1px solid var(--admin-border); padding-bottom: 0.5rem;">
                📌 <?= htmlspecialchars($title) ?>
            </h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div class="form-group">
                    <label>Meta Title Tag</label>
                    <input type="text" name="seo[<?= $key ?>][meta_title]" class="form-control" value="<?= htmlspecialchars($pageSeo['meta_title'] ?? '') ?>" placeholder="Page Title | FEEBLE EXPORTS">
                </div>

                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input type="text" name="seo[<?= $key ?>][meta_keywords]" class="form-control" value="<?= htmlspecialchars($pageSeo['meta_keywords'] ?? '') ?>" placeholder="coir mats, export, Namakkal...">
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label>Meta Description</label>
                <textarea name="seo[<?= $key ?>][meta_description]" class="form-control" style="min-height: 70px;" placeholder="Search engine snippet text (150-160 characters)..."><?= htmlspecialchars($pageSeo['meta_description'] ?? '') ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-top: 1rem;">
                <div class="form-group">
                    <label>OpenGraph Share Title (og:title)</label>
                    <input type="text" name="seo[<?= $key ?>][og_title]" class="form-control" value="<?= htmlspecialchars($pageSeo['og_title'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>OpenGraph Image Path (og:image)</label>
                    <input type="text" name="seo[<?= $key ?>][og_image]" class="form-control" value="<?= htmlspecialchars($pageSeo['og_image'] ?? '') ?>" placeholder="/assets/images/01_plain_handloom.png">
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div style="position: sticky; bottom: 1.5rem; background: var(--admin-card-bg); padding: 1.25rem 2rem; border-radius: var(--radius); border: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow);">
        <span style="font-size: 0.9rem; color: var(--admin-text-muted);">Save all updated SEO configurations across public pages</span>
        <button type="submit" class="btn-primary" style="padding: 0.75rem 2rem;">Save All SEO Settings</button>
    </div>
</form>
