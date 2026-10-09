<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Login - FEEBLE EXPORTS') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-brand">
                <h2>FEEBLE EXPORTS</h2>
                <p>Admin Portal Sign In</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert-banner alert-danger">
                    <span>⚠️</span>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form action="/admin/login" method="POST" class="admin-form">
                <div class="form-group">
                    <label for="email">Email or Username</label>
                    <input type="text" id="email" name="email" class="form-control" placeholder="admin@feebleexports.com or admin" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem; margin-top: 0.5rem;">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
    </div>
</body>
</html>
