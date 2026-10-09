<div class="page-header">
    <div class="page-title-group">
        <h1>Enquiry Management</h1>
        <p>Review customer quote requests and contact messages</p>
    </div>
</div>

<!-- Filters Bar -->
<div class="card-panel" style="padding: 1rem 1.25rem;">
    <form action="/admin/enquiries" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <div style="flex: 1; min-width: 200px;">
            <input type="text" name="search" class="form-control" placeholder="Search by ID, Name, Email, Country..." value="<?= htmlspecialchars($searchQuery) ?>">
        </div>

        <div style="width: 160px;">
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>New</option>
                <option value="contacted" <?= $statusFilter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </div>

        <div style="width: 160px;">
            <select name="type" class="form-control" onchange="this.form.submit()">
                <option value="all" <?= $typeFilter === 'all' ? 'selected' : '' ?>>All Types</option>
                <option value="quote" <?= $typeFilter === 'quote' ? 'selected' : '' ?>>Quote Requests</option>
                <option value="contact" <?= $typeFilter === 'contact' ? 'selected' : '' ?>>Contact Messages</option>
            </select>
        </div>

        <button type="submit" class="btn-primary">Search</button>
        <?php if ($searchQuery || $statusFilter !== 'all' || $typeFilter !== 'all'): ?>
            <a href="/admin/enquiries" class="btn-secondary">Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- Enquiries Table -->
<div class="card-panel">
    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>Received Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($enquiries)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--admin-text-muted); padding: 3rem;">
                            No enquiries found matching criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($enquiries as $enquiry): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($enquiry['id']) ?></strong></td>
                            <td>
                                <span class="badge <?= $enquiry['type'] === 'quote' ? 'badge-quote' : 'badge-contact' ?>">
                                    <?= htmlspecialchars($enquiry['type_label']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($enquiry['name']) ?></td>
                            <td><?= htmlspecialchars($enquiry['email']) ?></td>
                            <td><?= htmlspecialchars($enquiry['phone'] ?: '—') ?></td>
                            <td><?= htmlspecialchars($enquiry['country'] ?: '—') ?></td>
                            <td><?= date('M d, Y H:i', strtotime($enquiry['created_at'])) ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($enquiry['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($enquiry['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem;">
                                    <a href="/admin/enquiries/<?= $enquiry['id'] ?>" class="btn-secondary btn-sm">View</a>
                                    <form action="/admin/enquiries/<?= $enquiry['id'] ?>/delete" method="POST" class="form-delete-confirm" data-item-name="enquiry">
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
