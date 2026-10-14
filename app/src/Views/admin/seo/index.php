<?php
/**
 * Admin SEO Pages Listing View
 * Displays searchable, filterable table of SEO landing pages with status badges and actions.
 */
?>
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
    <div class="page-title-group">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); margin-bottom: 0.35rem;">SEO Pages</h1>
        <p style="color: var(--admin-text-muted); font-size: 0.95rem;">
            Create and manage custom landing pages with SEO metadata, hero content, projects, testimonials and FAQ.
        </p>
    </div>
    <div>
        <a href="/admin/seo/create" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.65rem 1.25rem; font-weight: 600; border-radius: 8px;">
            <span>+</span>
            <span>New SEO Page</span>
        </a>
    </div>
</div>

<!-- Search & Status Filter Bar -->
<div class="card-panel" style="padding: 1.25rem; margin-bottom: 1.5rem; background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: var(--radius);">
    <form method="GET" action="/admin/seo" id="seoFilterForm" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        
        <!-- Search Input -->
        <div style="flex: 1; min-width: 260px; position: relative;">
            <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); pointer-events: none; font-size: 0.95rem;">🔍</span>
            <input 
                type="text" 
                name="search" 
                id="seoSearchInput"
                class="form-control" 
                value="<?= htmlspecialchars($search ?? '') ?>" 
                placeholder="Search by page title or slug..."
                style="padding-left: 2.5rem; width: 100%; background: #0f172a; border: 1px solid var(--admin-border); color: #fff; height: 42px; border-radius: 8px;"
            >
        </div>

        <!-- Status Filter -->
        <div style="min-width: 180px;">
            <select 
                name="status" 
                id="seoStatusSelect"
                class="form-control" 
                style="background: #0f172a; border: 1px solid var(--admin-border); color: #fff; height: 42px; border-radius: 8px;"
                onchange="document.getElementById('seoFilterForm').submit();"
            >
                <option value="all" <?= ($status ?? 'all') === 'all' ? 'selected' : '' ?>>All Status</option>
                <option value="published" <?= ($status ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
            </select>
        </div>

        <?php if (!empty($search) || ($status ?? 'all') !== 'all'): ?>
            <a href="/admin/seo" class="btn-secondary btn-sm" style="height: 42px; display: inline-flex; align-items: center; text-decoration: none;">
                Reset Filters
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- SEO Pages Table Card -->
<div class="card-panel" style="padding: 0; background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: var(--radius); overflow: hidden;">
    <?php if (!empty($pages)): ?>
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--admin-border); background: rgba(15, 23, 42, 0.4);">
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase; width: 50px;">#</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase;">Page Title</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase;">URL / Slug</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase;">Status</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase;">Last Updated</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--admin-text-muted); text-transform: uppercase; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $index => $item): ?>
                        <tr style="border-bottom: 1px solid rgba(51, 65, 85, 0.5); transition: background-color 0.15s ease;">
                            <td style="padding: 1.15rem 1.25rem; font-size: 0.9rem; color: var(--admin-text-muted);">
                                <?= $index + 1 ?>
                            </td>
                            <td style="padding: 1.15rem 1.25rem;">
                                <a href="/admin/seo/<?= htmlspecialchars($item['id']) ?>/edit" style="color: var(--admin-text-main); font-weight: 600; text-decoration: none; font-size: 0.95rem;">
                                    <?= htmlspecialchars($item['title']) ?>
                                </a>
                                <?php if (!empty($item['meta_title'])): ?>
                                    <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 3px;">
                                        <?= htmlspecialchars(mb_strimwidth($item['meta_title'], 0, 60, '...')) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1.15rem 1.25rem; font-size: 0.88rem; color: #94a3b8; font-family: monospace;">
                                /<?= htmlspecialchars($item['slug']) ?>
                            </td>
                            <td style="padding: 1.15rem 1.25rem;">
                                <?php if (($item['status'] ?? '') === 'published'): ?>
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                        Published
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
                                        Draft
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1.15rem 1.25rem; font-size: 0.88rem; color: var(--admin-text-muted); white-space: nowrap;">
                                <?= date('M d, Y', strtotime($item['updated_at'] ?? $item['created_at'] ?? 'now')) ?>
                            </td>
                            <td style="padding: 1.15rem 1.25rem; text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    <!-- View Live / Preview in New Tab -->
                                    <?php if (($item['status'] ?? '') === 'published'): ?>
                                        <a href="/<?= htmlspecialchars($item['slug']) ?>" target="_blank" class="btn-action-icon" title="View Published Page" style="color: #60a5fa; padding: 6px; border-radius: 6px; display: inline-flex; align-items: center;">
                                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                <polyline points="15 3 21 3 21 9"></polyline>
                                                <line x1="10" y1="14" x2="21" y2="3"></line>
                                            </svg>
                                        </a>
                                    <?php else: ?>
                                        <a href="/admin/seo/<?= htmlspecialchars($item['id']) ?>/preview" target="_blank" class="btn-action-icon" title="Preview Draft" style="color: #f59e0b; padding: 6px; border-radius: 6px; display: inline-flex; align-items: center;">
                                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Edit Page -->
                                    <a href="/admin/seo/<?= htmlspecialchars($item['id']) ?>/edit" class="btn-action-icon" title="Edit SEO Page" style="color: #10b981; padding: 6px; border-radius: 6px; display: inline-flex; align-items: center;">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete Form -->
                                    <form action="/admin/seo/<?= htmlspecialchars($item['id']) ?>/delete" method="POST" class="form-delete-confirm" data-item-name="SEO page '<?= htmlspecialchars($item['title']) ?>'" style="display: inline-block; margin: 0;">
                                        <button type="submit" class="btn-action-icon" title="Delete SEO Page" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 6px; border-radius: 6px; display: inline-flex; align-items: center;">
                                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination bar -->
        <div style="padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--admin-border); background: rgba(15, 23, 42, 0.4); flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.85rem; color: var(--admin-text-muted);">
                Showing 1 to <?= count($pages) ?> of <?= count($pages) ?> entries
            </span>
            <div style="display: flex; gap: 0.25rem;">
                <button type="button" class="btn-secondary btn-sm" disabled style="opacity: 0.5; padding: 0.35rem 0.75rem;">&lt;</button>
                <button type="button" class="btn-primary btn-sm" style="padding: 0.35rem 0.75rem;">1</button>
                <button type="button" class="btn-secondary btn-sm" disabled style="opacity: 0.5; padding: 0.35rem 0.75rem;">&gt;</button>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--admin-text-main); margin-bottom: 0.5rem;">No SEO Pages Found</h3>
            <p style="color: var(--admin-text-muted); font-size: 0.95rem; max-width: 480px; margin: 0 auto 1.5rem auto;">
                <?= !empty($search) || ($status ?? 'all') !== 'all' 
                    ? 'No landing pages matched your current search and filter criteria. Try resetting your search filters.' 
                    : 'Get started by creating your first search-engine optimized landing page with hero banners, content blocks, and FAQs.' ?>
            </p>
            <?php if (!empty($search) || ($status ?? 'all') !== 'all'): ?>
                <a href="/admin/seo" class="btn-secondary">Clear Filters</a>
            <?php else: ?>
                <a href="/admin/seo/create" class="btn-primary">+ Create New SEO Page</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
