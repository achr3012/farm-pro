<?php

require('config.php');

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $f = filter_var_array(array_map('trim', $_POST), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  if (
    empty($f['name']) ||            empty($f['type']) ||
    empty($f['address']) ||         empty($f['statut_economique']) ||
    empty($f['entree_activite']) || empty($f['capital'])
  ) {
    $error = "Please fill all the fields.";
  } else {
    $stmt = $mysql->prepare("INSERT INTO `ferme` 
    (`name`, `address`, `type_ferme`, `statut_economique`, `capital`, `date_entree_activite`,
    `etat`, `date_cessation_activite`, `superficie_globale`, `superficie_du_bati`) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
      "ssssisssii",
      $f['name'],
      $f['address'],
      $f['type'],
      $f['statut_economique'],
      $f['capital'],
      $f['entree_activite'],
      $f['etat'],
      $f['date_cessation_activite'],
      $f['superficie_globale'],
      $f['superficie_du_bati']
    );
    $stmt->execute();
    if ($stmt->affected_rows == 1) {
      $success = "La ferme a été ajoutée avec succès.";
    } else {
      $error = "ERROR";
    }
  }
}

$pageTitle = "Ajouter une nouvelle ferme";
require('includes/templates/sidebar.php');
?>
<main>
  <h1 class="page-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    <?= $pageTitle; ?>
  </h1>

  <form action=<?= $_SERVER['PHP_SELF']; ?> method="POST" class="ajoute-ferme" autocomplete="off">
    <?php if (isset($error)) : ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($success)) : ?>
      <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="form-group-2">
      <input type="text" name="name" placeholder="Nom de ferme" />
      <input type="text" name="type" placeholder="Type de ferme" />
    </div>
    <div class="form-group-2">
      <input type="text" name="address" placeholder="Address de ferme" />
      <input type="text" name="statut_economique" placeholder="Statut economique de ferme" />
    </div>
    <div class="form-group-2">
      <div>
        <label for="entree_activite">Date d’entrée en activité</label>
        <input type="date" name="entree_activite" id="entree_activite">
      </div>
      <div>
        <label for="date_cessation_activite">Date de cessation d’activité superficie globale</label>
        <input type="date" name="date_cessation_activite" id="date_cessation_activite">
      </div>
    </div>
    <div class="form-group-3">
      <input type="number" name="capital" placeholder="00.0 Capital">
      <input type="number" name="superficie_globale" placeholder="00.0 Superficie globale">
      <input type="number" name="superficie_du_bati" placeholder="00.0 Superficie du bâti">
    </div>
    <div class="form-group">
      <label for="etat">Statut d’activité</label>
      <select name="etat" id="etat">
        <option value="disactive">Disactive</option>
        <option value="active">Active</option>
      </select>
    </div>
    <div class="form-group">
      <button type="submit">Créer la ferme</button>
    </div>
  </form>

</main>

</div>
</body>

</html>