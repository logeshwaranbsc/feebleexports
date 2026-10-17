<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'FEEBLE EXPORTS - Sustainable Coir Products') ?></title>
  <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'FEEBLE EXPORTS - Sustainable, durable, and eco-friendly natural coir mats crafted for a cleaner and greener tomorrow.') ?>">
  <?php if (!empty($metaKeywords)): ?>
    <meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
  <?php endif; ?>
  <?php if (!empty($canonicalUrl)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
  <?php endif; ?>
  <?php if (!empty($ogTitle)): ?>
    <meta property="og:title" content="<?= htmlspecialchars($ogTitle) ?>">
  <?php endif; ?>
  <?php if (!empty($ogDescription)): ?>
    <meta property="og:description" content="<?= htmlspecialchars($ogDescription) ?>">
  <?php endif; ?>
  <?php if (!empty($ogImage)): ?>
    <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
  <?php endif; ?>
  <link rel="stylesheet" href="/css/style.css?v=2.3.0_<?= @filemtime(PUBLIC_PATH . '/css/style.css') ?: '20261017_2' ?>">
  <link rel="icon" type="image/png" href="/assets/images/Feeble%20Logo.png">
  <?php if (!empty($customHeadHtml)): ?>
    <?= $customHeadHtml ?>
  <?php endif; ?>
</head>
<body>

  <?php require BASE_PATH . '/src/Views/partials/header.php'; ?>

  <main style="flex-grow: 1;">
    <?= $content ?>
  </main>

  <?php require BASE_PATH . '/src/Views/partials/footer.php'; ?>
  <?php require BASE_PATH . '/src/Views/partials/quote_modal.php'; ?>

  <script src="/js/app.js?v=2.3.0_<?= @filemtime(PUBLIC_PATH . '/js/app.js') ?: '20261017_2' ?>"></script>
  <script src="https://www.google.com/recaptcha/api.js?onload=initRecaptchas&render=explicit" async defer></script>
</body>
</html>
