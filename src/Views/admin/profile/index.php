<div class="page-header">
    <div class="page-title-group">
        <h1>Admin Profile & Security Settings</h1>
        <p>Update administrator details, login credentials, and account password</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Profile Form -->
    <div class="card-panel" style="padding: 2rem;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--admin-primary); margin-bottom: 1.25rem;">
            Account Details
        </h2>

        <form action="/admin/profile" method="POST" class="admin-form">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div class="form-group">
                    <label for="name">Proprietress / Admin Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($adminUser['name'] ?? 'Kavimayil Venkatachalam') ?>" required>
                    <?php if (!empty($errors['name'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['name']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($adminUser['email'] ?? 'admin@feebleexports.com') ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['email']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username (Optional)</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($adminUser['username'] ?? 'admin') ?>">
            </div>

            <hr style="border-color: var(--admin-border); margin: 1.5rem 0;">

            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: #f59e0b;">
                Change Security Password
            </h3>
            <p style="font-size: 0.82rem; color: var(--admin-text-muted); margin-bottom: 1rem;">
                Leave fields empty if you do not wish to change your current password.
            </p>

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" placeholder="••••••••">
                <?php if (!empty($errors['current_password'])): ?>
                    <span class="form-error"><?= htmlspecialchars($errors['current_password']) ?></span>
                <?php endif; ?>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="At least 6 characters">
                    <?php if (!empty($errors['new_password'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['new_password']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Repeat new password">
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <span class="form-error"><?= htmlspecialchars($errors['confirm_password']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn-primary" style="padding: 0.75rem 1.75rem;">Save Profile Changes</button>
            </div>
        </form>
    </div>

    <!-- Company Details Card -->
    <div class="card-panel" style="padding: 1.75rem;">
        <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: var(--admin-primary);">
            🏢 Company Information
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.88rem;">
            <div>
                <span style="color: var(--admin-text-muted); font-size: 0.75rem; uppercase;">Company Name</span>
                <p style="font-weight: 700; color: var(--admin-text-main);">FEEBLE EXPORTS</p>
            </div>

            <div>
                <span style="color: var(--admin-text-muted); font-size: 0.75rem; uppercase;">Tagline</span>
                <p style="font-weight: 600; color: var(--admin-accent-gold);">"Not a Big Deal"</p>
            </div>

            <div>
                <span style="color: var(--admin-text-muted); font-size: 0.75rem; uppercase;">Proprietress</span>
                <p style="font-weight: 600;">Kavimayil Venkatachalam</p>
            </div>

            <div>
                <span style="color: var(--admin-text-muted); font-size: 0.75rem; uppercase;">Location</span>
                <p style="font-weight: 600;">Namakkal, Tamil Nadu, India - 637001</p>
            </div>

            <div>
                <span style="color: var(--admin-text-muted); font-size: 0.75rem; uppercase;">Core Business</span>
                <p style="font-weight: 600;">Natural coir mats & eco-friendly coconut fibre products</p>
            </div>
        </div>
    </div>
</div>
