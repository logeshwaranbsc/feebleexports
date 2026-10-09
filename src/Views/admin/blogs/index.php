<div class="page-header">
    <div class="page-title-group">
        <h1>Blog Management</h1>
        <p>Publish export news, coir industry guides, and market updates</p>
    </div>
    <a href="/admin/blogs/create" class="btn-primary">+ Add New Blog Post</a>
</div>

<div class="card-panel">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title & Slug</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($blogs)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--admin-text-muted); padding: 3rem;">
                            No blog posts found. Click "+ Add New Blog Post" to publish one.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($blogs as $blog): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong style="font-size: 0.95rem; color: var(--admin-text-main);"><?= htmlspecialchars($blog['title']) ?></strong>
                                    <div style="font-size: 0.78rem; color: var(--admin-text-muted); margin-top: 0.15rem;">
                                        /blog/<?= htmlspecialchars($blog['slug']) ?>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($blog['category'] ?? 'Coir Industry') ?></td>
                            <td><?= htmlspecialchars($blog['author'] ?? 'Admin') ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($blog['status'] ?? 'published') ?>">
                                    <?= ucfirst(htmlspecialchars($blog['status'] ?? 'published')) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($blog['created_at'])) ?></td>
                            <td>
                                <div style="display: flex; gap: 0.4rem;">
                                    <a href="/admin/blogs/<?= $blog['id'] ?>/edit" class="btn-secondary btn-sm">Edit</a>
                                    <form action="/admin/blogs/<?= $blog['id'] ?>/delete" method="POST" class="form-delete-confirm" data-item-name="blog post">
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
