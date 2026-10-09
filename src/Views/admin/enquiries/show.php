<div class="page-header">
    <div class="page-title-group">
        <a href="/admin/enquiries" style="color: var(--admin-primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">← Back to Enquiries</a>
        <h1 style="margin-top: 0.5rem;">Enquiry Details: <?= htmlspecialchars($enquiry['id']) ?></h1>
    </div>

    <div style="display: flex; gap: 0.75rem;">
        <span class="badge <?= $enquiry['type'] === 'quote' ? 'badge-quote' : 'badge-contact' ?>" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
            <?= htmlspecialchars($enquiry['type_label']) ?>
        </span>
        <span class="badge badge-<?= htmlspecialchars($enquiry['status']) ?>" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
            <?= ucfirst(htmlspecialchars($enquiry['status'])) ?>
        </span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Main Detail Card -->
    <div class="card-panel" style="padding: 1.75rem;">
        <h2 style="font-size: 1.2rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--admin-border); padding-bottom: 0.75rem;">
            Customer & Requirement Information
        </h2>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
            <div>
                <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Full Name</span>
                <p style="font-size: 1.05rem; font-weight: 600; margin-top: 0.2rem;"><?= htmlspecialchars($enquiry['name']) ?></p>
            </div>

            <div>
                <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Email Address</span>
                <p style="font-size: 1.05rem; font-weight: 600; margin-top: 0.2rem;">
                    <a href="mailto:<?= htmlspecialchars($enquiry['email']) ?>" style="color: var(--admin-primary); text-decoration: none;">
                        <?= htmlspecialchars($enquiry['email']) ?> ✉️
                    </a>
                </p>
            </div>

            <div>
                <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Phone Number</span>
                <p style="font-size: 1rem; margin-top: 0.2rem;"><?= htmlspecialchars($enquiry['phone'] ?: 'Not specified') ?></p>
            </div>

            <div>
                <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Country / Destination</span>
                <p style="font-size: 1rem; margin-top: 0.2rem;"><?= htmlspecialchars($enquiry['country'] ?: 'Not specified') ?></p>
            </div>
        </div>

        <?php if ($enquiry['type'] === 'quote'): ?>
            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--admin-primary); margin-bottom: 0.75rem;">📦 Quote Specifics</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <span style="font-size: 0.75rem; color: var(--admin-text-muted);">Product Category</span>
                        <p style="font-weight: 600;"><?= htmlspecialchars($enquiry['product'] ?? 'General') ?></p>
                    </div>
                    <div>
                        <span style="font-size: 0.75rem; color: var(--admin-text-muted);">Estimated Quantity</span>
                        <p style="font-weight: 600;"><?= htmlspecialchars($enquiry['quantity'] ?? 'N/A') ?> units</p>
                    </div>
                    <?php if (!empty($enquiry['custom_size'])): ?>
                        <div style="grid-column: span 2;">
                            <span style="font-size: 0.75rem; color: var(--admin-text-muted);">Custom Size Requirement</span>
                            <p style="font-weight: 600;"><?= htmlspecialchars($enquiry['custom_size']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div>
            <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--admin-text-muted); font-weight: 700;">Message / Notes</span>
            <div style="background: rgba(0, 0, 0, 0.2); border: 1px solid var(--admin-border); border-radius: 8px; padding: 1rem; margin-top: 0.4rem; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.5;">
                <?= htmlspecialchars($enquiry['message'] ?? $enquiry['notes'] ?? 'No additional message text.') ?>
            </div>
        </div>
    </div>

    <!-- Status Management Sidebar Card -->
    <div class="card-panel" style="padding: 1.5rem;">
        <h3 style="font-size: 1.05rem; margin-bottom: 1rem;">Update Status</h3>

        <form action="/admin/enquiries/<?= $enquiry['id'] ?>/status" method="POST" class="admin-form">
            <div class="form-group">
                <label for="status">Current Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="new" <?= $enquiry['status'] === 'new' ? 'selected' : '' ?>>🔵 New (Unread)</option>
                    <option value="contacted" <?= $enquiry['status'] === 'contacted' ? 'selected' : '' ?>>🟡 Contacted / Processing</option>
                    <option value="closed" <?= $enquiry['status'] === 'closed' ? 'selected' : '' ?>>🟢 Closed / Fulfilled</option>
                </select>
            </div>

            <button type="submit" class="btn-primary" style="justify-content: center; width: 100%;">
                Save Status Update
            </button>
        </form>

        <hr style="border-color: var(--admin-border); margin: 1.5rem 0;">

        <form action="/admin/enquiries/<?= $enquiry['id'] ?>/delete" method="POST" class="form-delete-confirm" data-item-name="enquiry">
            <button type="submit" class="btn-danger" style="width: 100%; text-align: center;">
                🗑️ Delete Enquiry
            </button>
        </form>
    </div>
</div>
