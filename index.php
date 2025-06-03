<?php session_start(); ?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>איזון | בריאות | חיים</title>

  <!-- Bootstrap RTL CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

  <!-- גופנים -->
  <link href="https://fonts.googleapis.com/css2?family=Suez+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Attraction&display=swap" rel="stylesheet">

  <!-- CSS אישי שלך (אופציונלי, אחרי Bootstrap) -->
  <link rel="stylesheet" href="assets/css/home_css/home.css?v=1.3">
</head>
<body style="background: linear-gradient(to right, #ffe4b5, #ffb6c1); font-family: 'Suez One', serif;">

<!-- שורת עליונה עם התחברות -->
<div class="bg-transparent text-end px-3 pt-2 small">
  <?php if (isset($_SESSION['username'])): ?>
    שלום, <?= htmlspecialchars($_SESSION['username']) ?> |
    <a href="login/logout.php" class="text-dark text-decoration-none">התנתקות</a>
  <?php else: ?>
    <a href="login/login.html" class="text-dark text-decoration-none">התחברות</a>
  <?php endif; ?>
</div>

<!-- תפריט ניווט -->
<nav class="navbar navbar-expand-lg bg-light shadow-sm px-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">
      <img src="assets/images/logo2.png" alt="לוגו" height="80">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="תפריט">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-bold">
        <li class="nav-item">
          <a class="nav-link" href="index.php">בית</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">נעים להכיר</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">תוכניות</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">תשאלו אותם</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="home/price/price.php">תפריטים ועוד</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="home/user/user_dashboard.php">האזור האישי</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- גיבור (Hero Section) -->
<section class="text-center py-5">
  <h1 class="display-4 fw-bold mb-4">איזון. בריאות. חיים.</h1>
  <div class="container">
    <div class="row justify-content-center g-4">
      <div class="col-10 col-sm-6 col-md-4">
        <img src="assets/images/מדריכה.jpeg" class="img-fluid rounded-circle shadow" alt="תמונה 1">
      </div>
      <div class="col-10 col-sm-6 col-md-4">
        <img src="assets/images/סלטה.jpeg" class="img-fluid rounded-circle shadow" alt="תמונה 2">
      </div>
      <div class="col-10 col-sm-6 col-md-4">
        <img src="assets/images/שפגט.jpeg" class="img-fluid rounded-circle shadow" alt="תמונה 3">
      </div>
    </div>
  </div>
</section>

<!-- רגל -->
<footer class="text-center py-4">
  <div class="d-inline-flex gap-3 bg-light rounded-pill p-2 shadow-sm">
    <a href="#"><img src="assets/images/facebook-app-symbol.png" alt="פייסבוק" width="36"></a>
    <a href="#"><img src="assets/images/instagram.png" alt="אינסטגרם" width="36"></a>
    <a href="#"><img src="assets/images/whatsapp.png" alt="וואטסאפ" width="36"></a>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
