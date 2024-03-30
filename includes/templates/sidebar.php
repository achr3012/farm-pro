<?php

if (isset($pageTitle)) {
  $title = $pageTitle;
} else {
  $title = "No title provided";
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title . " | MyFarm"; ?></title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
  <div class="conatiner">
    <section class="sidebar">
      <div class="header">
        <h1 class="logo"><a href="/">My<span>Farm</span></a></h1>
        <p class="user"><?= $_SESSION['user']['username'] . '@' . $_SESSION['user']['role']  ?></p>
      </div>
      <div class="links">
        <ul>
          <?php if ($_SESSION['user']['role'] == 'administrateur') : ?>
            <li><a href="ajoute-ferme.php">Ajoute Ferme</a></li>
          <?php endif; ?>

          <?php if ($_SESSION['user']['role'] == 'administrateur' || $_SESSION['user']['role'] == 'consultant') : ?>
            <li><a href="fermes.php">Fermes</a></li>
          <?php endif; ?>

          <li><a href="#">Link Three</a></li>
          <li><a href="#">Link Four</a></li>
          <li><a href="#">Link Fivee</a></li>
        </ul>
      </div>
      <a class="logout" href="logout.php">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
        </svg>
        LogOut
      </a>
    </section>