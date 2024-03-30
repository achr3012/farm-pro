<?php

require('config.php');

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}


$result = $mysql->query("SELECT * FROM ferme");
if ($result === false) {
  die("Error: " . $mysql->error);
}

$pageTitle = "Les fermes";
require('includes/templates/sidebar.php');
?>
<main>
  <h1 class="page-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
    </svg>
    <?= $pageTitle; ?>
  </h1>






  <?php if ($result->num_rows > 0) { ?>
    <div class="table">
      <div class="table-row header">
        <div class="table-cell">ID</div>
        <div class="table-cell">Nom</div>
        <div class="table-cell">Address</div>
        <div class="table-cell">Type</div>
        <div class="table-cell">Status Economique</div>
        <div class="table-cell">etat</div>
        <div class="table-cell">Date entree d'act</div>
        <div class="table-cell">Date cessa d'act</div>
        <div class="table-cell">Capital</div>
        <div class="table-cell">Superficie global</div>
        <div class="table-cell">Superficie de bati</div>
        <div class="table-cell">Options</div>
      </div>

      <?php foreach ($result as $f) :  ?>

        <div class="table-row">
          <div class="table-cell"><a href="ferme.php?code=<?= $f['code']; ?>"><?= $f['code']; ?></a></div>
          <div class="table-cell"><a href="ferme.php?code=<?= $f['code']; ?>"><?= $f['name']; ?></a></div>
          <div class="table-cell"><?= $f['address']; ?></div>
          <div class="table-cell"><?= $f['type_ferme']; ?></div>
          <div class="table-cell"><?= $f['statut_economique']; ?></div>
          <div class="table-cell"><?= $f['etat']; ?></div>
          <div class="table-cell"><?= $f['date_entree_activite']; ?></div>
          <div class="table-cell"><?= $f['date_cessation_activite']; ?></div>
          <div class="table-cell"><?= $f['capital']; ?></div>
          <div class="table-cell"><?= $f['superficie_globale']; ?></div>
          <div class="table-cell"><?= $f['superficie_du_bati']; ?></div>
          <div class="table-cell">sahl</div>
        </div>

    <?php endforeach;
    } else {
      echo "No rows returned.";
    }

    ?>

    <!-- Add more rows here as needed -->
    </div>
</main>

</div>
</body>

</html>