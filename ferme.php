<?php

require('config.php');


if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if (isset($_GET['code'])) {
  $code = (int)$_GET['code'];
} else if (isset($_SESSION['user']['codeferme'])) {
  $code = (int)$_SESSION['user']['codeferme'];
} else {
  $pageTitle = "404 Page not found";
  require('includes/templates/sidebar.php');

  die("
    <main>
      <h1 class='page-title'>404 Page not found</h1>
      <br />
      <a href='/'>&lt;&lt; Retour à la page d'accueil</a>
    </main>
</div></body></html>");
}

$stmt = $mysql->prepare("SELECT * FROM ferme WHERE code = ?;");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
  $pageTitle = "404 Page not found";
  require('includes/templates/sidebar.php');

  die("
    <main>
      <h1 class='page-title'>404 Page not found</h1>
      <br />
      <a href='/'>&lt;&lt; Retour à la page d'accueil</a>
    </main>
</div></body></html>");
} else {
  $f = $result->fetch_object();
  $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['edit_ferme']) {
  $fe = filter_var_array(array_map('trim', $_POST), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  if (
    empty($fe['name']) ||            empty($fe['type']) ||
    empty($fe['address']) ||         empty($fe['statut_economique']) ||
    empty($fe['entree_activite']) || empty($fe['capital'])
  ) {
    $error = "Please fill all the fields.";
  } else {

    $stmt = $mysql->prepare(
      "UPDATE `ferme` SET 
              `name` = ?, `address` = ?, `type_ferme` = ?,
              `statut_economique` = ?, `capital` = ?, `date_entree_activite` = ?,
              `etat` = ?, `date_cessation_activite` = ?, `superficie_globale` = ?, `superficie_du_bati` = ?
      WHERE `ferme`.`code` = ?"
    );
    $stmt->bind_param(
      "ssssisssiii",
      $fe['name'],
      $fe['address'],
      $fe['type'],
      $fe['statut_economique'],
      $fe['capital'],
      $fe['entree_activite'],
      $fe['etat'],
      $fe['date_cessation_activite'],
      $fe['superficie_globale'],
      $fe['superficie_du_bati'],
      $f->code
    );
    $stmt->execute();
    if ($stmt->affected_rows == 1) {
      $success = "La ferme (" . $fe['name'] . ") a été Modifier avec succès.";
      $stmt->close();
    }
    $mysql->close(); // Close connection
  }
}

$pageTitle = isset($fe) ? $fe['name'] : $f->name;
require('includes/templates/sidebar.php');
$selected = isset($fe)
  ? ($fe['etat'] == 'active' ? 'selected' : '')
  : ($f->etat == 'active' ? 'selected' : '');
?>

<main>
  <h1 class="page-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
    </svg>
    <?= $pageTitle; ?>
  </h1>

  <div class="buttons">
    <button type="button" id="toggleEdit" class="btn-out">Modifier la ferme</button>
    <button type="button" id="toggleCreate" class="btn-out">Crée un compte Gestionnaire</button>
  </div>


  <section class="ajoute-ferme readonlyForm" id="readonlyForm">

    <div class="form-group-2">
      <div>
        <label for="name">Nom de ferme</label>
        <input readonly type="text" name="name" id="name" value="<?php isset($fe) ? printf($fe['name']) : printf($f->name) ?>" placeholder="Nom de ferme" />
      </div>
      <div>
        <label for="type">Type de ferme</label>
        <input readonly type="text" name="type" id="type" value="<?php isset($fe) ? printf($fe['type']) : printf($f->type_ferme) ?>" placeholder="Type de ferme" />
      </div>
    </div>
    <div class="form-group-2">
      <div>
        <label for="address">Address de ferme</label>
        <input readonly type="text" name="address" id="address" value="<?php isset($fe) ? printf($fe['address']) : printf($f->address) ?>" placeholder="Address de ferme" />
      </div>
      <div>
        <label for="statut_economique">Statut economique de ferme</label>
        <input readonly type="text" name="statut_economique" id="statut_economique" value="<?php isset($fe) ? printf($fe['statut_economique']) : printf($f->statut_economique) ?>" placeholder="Statut economique de ferme" />
      </div>
    </div>
    <div class="form-group-2">
      <div>
        <label for="entree_activite">Date d’entrée en activité</label>
        <input readonly type="date" name="entree_activite" value="<?php isset($fe) ? printf($fe['entree_activite']) : printf($f->date_entree_activite) ?>" id="entree_activite">
      </div>
      <div>
        <label for="date_cessation_activite">Date de cessation d’activité superficie globale</label>
        <input readonly type="date" name="date_cessation_activite" value="<?php isset($fe) ? printf($fe['date_cessation_activite']) : printf($f->date_cessation_activite) ?>" id="date_cessation_activite">
      </div>
    </div>
    <div class="form-group-3">
      <div>
        <label for="capital">Capital</label>
        <input readonly type="number" name="capital" id="capital" value="<?php isset($fe) ? printf($fe['capital']) : printf($f->capital) ?>" placeholder="00.0 Capital">
      </div>
      <div>
        <label for="superficie_globale">Superficie globale</label>
        <input readonly type="number" name="superficie_globale" id="superficie_globale" value="<?php isset($fe) ? printf($fe['superficie_globale']) : printf($f->superficie_globale) ?>" placeholder="00.0 Superficie globale">
      </div>
      <div>
        <label for="superficie_du_bati">Superficie du bâti</label>
        <input readonly type="number" name="superficie_du_bati" id="superficie_du_bati" value="<?php isset($fe) ? printf($fe['superficie_du_bati']) : printf($f->superficie_du_bati) ?>" placeholder="00.0 Superficie du bâti">
      </div>
    </div>
    <div class="form-group">
      <label for="etat">Statut d’activité</label>
      <select name="etat" id="etat" disabled>
        <option value="disactive">Disactive</option>
        <option value="active" <?= $selected ?>>Active</option>
      </select>
    </div>
  </section>


  <form action=<?= $_SERVER['REQUEST_URI']; ?> method="POST" id="editForm" class="ajoute-ferme">
    <input type="hidden" name="edit_ferme" value="tiri brk">
    <?php if (isset($error)) : ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($success)) : ?>
      <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="form-group-2">
      <div>
        <label for="name">Nom de ferme</label>
        <input type="text" name="name" id="name" value="<?php isset($fe) ? printf($fe['name']) : printf($f->name) ?>" placeholder="Nom de ferme" />
      </div>
      <div>
        <label for="type">Type de ferme</label>
        <input type="text" name="type" id="type" value="<?php isset($fe) ? printf($fe['type']) : printf($f->type_ferme) ?>" placeholder="Type de ferme" />
      </div>
    </div>
    <div class="form-group-2">
      <div>
        <label for="address">Address de ferme</label>
        <input type="text" name="address" id="address" value="<?php isset($fe) ? printf($fe['address']) : printf($f->address) ?>" placeholder="Address de ferme" />
      </div>
      <div>
        <label for="statut_economique">Statut economique de ferme</label>
        <input type="text" name="statut_economique" id="statut_economique" value="<?php isset($fe) ? printf($fe['statut_economique']) : printf($f->statut_economique) ?>" placeholder="Statut economique de ferme" />
      </div>
    </div>
    <div class="form-group-2">
      <div>
        <label for="entree_activite">Date d’entrée en activité</label>
        <input type="date" name="entree_activite" value="<?php isset($fe) ? printf($fe['entree_activite']) : printf($f->date_entree_activite) ?>" id="entree_activite">
      </div>
      <div>
        <label for="date_cessation_activite">Date de cessation d’activité superficie globale</label>
        <input type="date" name="date_cessation_activite" value="<?php isset($fe) ? printf($fe['date_cessation_activite']) : printf($f->date_cessation_activite) ?>" id="date_cessation_activite">
      </div>
    </div>
    <div class="form-group-3">
      <div>
        <label for="capital">Capital</label>
        <input type="number" name="capital" id="capital" value="<?php isset($fe) ? printf($fe['capital']) : printf($f->capital) ?>" placeholder="00.0 Capital">
      </div>
      <div>
        <label for="superficie_globale">Superficie globale</label>
        <input type="number" name="superficie_globale" id="superficie_globale" value="<?php isset($fe) ? printf($fe['superficie_globale']) : printf($f->superficie_globale) ?>" placeholder="00.0 Superficie globale">
      </div>
      <div>
        <label for="superficie_du_bati">Superficie du bâti</label>
        <input type="number" name="superficie_du_bati" id="superficie_du_bati" value="<?php isset($fe) ? printf($fe['superficie_du_bati']) : printf($f->superficie_du_bati) ?>" placeholder="00.0 Superficie du bâti">
      </div>
    </div>
    <div class="form-group">
      <label for="etat">Statut d’activité</label>
      <select name="etat" id="etat">
        <option value="disactive">Disactive</option>
        <option value="active" <?= $selected ?>>Active</option>
      </select>
    </div>
    <div class="form-group">
      <button type="submit">Modifier</button>
    </div>
  </form>

  <form action=<?= $_SERVER['REQUEST_URI']; ?> method="POST" id="createUsers" class="createUsers">
    add users
  </form>
</main>
</div>
<script>
  const readonlyForm = document.getElementById('readonlyForm');
  const toggleEdit = document.getElementById('toggleEdit');
  const editForm = document.getElementById('editForm');
  const toggleCreate = document.getElementById('toggleCreate');
  const createUsers = document.getElementById('createUsers');

  toggleEdit.addEventListener('click', function() {
    toggleEdit.classList.toggle('active')
    editForm.classList.toggle('expanded')
    readonlyForm.classList.toggle('collapsed')
  });

  toggleCreate.addEventListener('click', function() {
    toggleCreate.classList.toggle('active')
    createUsers.classList.toggle('expanded')
    createUsers.classList.toggle('collapsed')
  });

  <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_ferme'])) : ?>
    toggleEdit.classList.add('active')
    editForm.classList.add('expanded')
    readonlyForm.classList.add('collapsed')
  <?php endif; ?>
</script>
</body>

</html>