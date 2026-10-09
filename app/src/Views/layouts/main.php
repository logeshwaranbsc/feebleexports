<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'FEEBLE EXPORTS - Sustainable Coir Products') ?></title>
  <meta name="description" content="FEEBLE EXPORTS - Sustainable, durable, and eco-friendly natural coir mats crafted for a cleaner and greener tomorrow.">
  <link rel="stylesheet" href="/css/style.css">
  <link rel="icon" type="image/png" href="/assets/images/Feeble%20Logo.png">
</head>
<body>

  <?php require BASE_PATH . '/src/Views/partials/header.php'; ?>

  <main style="flex-grow: 1;">
    <?= $content ?>
  </main>

  <?php require BASE_PATH . '/src/Views/partials/footer.php'; ?>
  <?php require BASE_PATH . '/src/Views/partials/quote_modal.php'; ?>

  <script src="/js/app.js"></script>
</body>
</html>
