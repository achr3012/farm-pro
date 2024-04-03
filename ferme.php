<?php

require('config.php');


if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if (isset($_SESSION['user']['codeferme'])) {
  $code = (int)$_SESSION['user']['codeferme'];
} elseif ($_SESSION['user']['role'] == 'administrateur') {
  $code = $_GET['code'];
} else {
  notFound();
}

$stmt = $mysql->prepare("SELECT * FROM ferme WHERE code = ?;");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
  notFound();
} else {
  $f = $result->fetch_object();
  $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_ferme'])) {
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
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
  if ($_SESSION['user']['role'] == 'administrateur' || $_SESSION['user']['role'] == 'gestionnaire') {
    $input = filter_var_array(array_map('trim', $_POST), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    if (strlen($input['username']) < 4 || strlen($input['password']) < 4) {
      $cuError = "Please fill all the fields";
    } else {

      $stmt = $mysql->prepare("SELECT id from user where username = ?");
      $stmt->bind_param("s", $input['username']);
      $stmt->execute();
      // Get result
      $result = $stmt->get_result();
      if ($result->num_rows == 1) {
        $cuError = "Ce nom d'utilisateur  (" . $input['username'] . ")  existe déjà";
      } else {
        $password = password_hash($input['password'], PASSWORD_DEFAULT);
        if ($input['role'] == 'administrateur') {
          $stmt = $mysql->prepare(
            "INSERT INTO `user` 
              (`username`, `password`, `etat`, `role`, `codeferme`)
              VALUES (?, ?, ?, ?, ?)"
          );
          $stmt->bind_param(
            "ssssi",
            $input['username'],
            $password,
            $input['userEtat'],
            $input['role'],
            $code
          );
        } else {

          $stmt = $mysql->prepare("INSERT INTO `user` 
            (`username`, `password`, `etat`, `role`, `codeferme`, `username_f`)
            VALUES (?, ?, ?, ?, ?, ?)");

          $stmt->bind_param(
            "ssssis",
            $input['username'],
            $password,
            $input['userEtat'],
            $input['role'],
            $code,
            $_SESSION['user']['username']
          );
        }

        $stmt->execute();
        if ($stmt->affected_rows == 1) {
          $createdId = $stmt->insert_id;
          $stmt->close();
          if ($mysql->query("INSERT INTO compte (id) VALUES ('$createdId')") === TRUE) {
            $cuSuccess = "L'utilisateur " . $input['username'] . " a été créé avec succès<br />il peut se connecter pour terminer la création de son compte";
          } else {
            $cuError = "Error: " . $sql . "<br>" . $mysql->error;
          }
        } else {
          $cuError = "ERROR";
        }
      }
    }
  }
}

if ($_SESSION['user']['role'] == 'administrateur' || $_SESSION['user']['role'] == 'gestionnaire' && $_SESSION['user']['codeferme'] == $code) {
  $result = $mysql->query("SELECT * FROM `user` WHERE `codeferme` = $code");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['role']) && isset($_POST['editUser'])) {
  $stmt = $mysql->prepare(
    "UPDATE `user` SET `role` = ?
    WHERE `user`.`id` = ?"
  );
  $stmt->bind_param(
    "si",
    $_POST['role'],
    $_POST['editUser'],
  );
  $stmt->execute();
  if ($stmt->affected_rows == 1) {
    $stmt->close();
    header("Location: " . $_SERVER['REQUEST_URI'] . '&role=edited');
  }
  $mysql->close(); // Close connection
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userEtat']) && isset($_POST['editUser'])) {
  // dump([$_POST['userEtat'], $_POST['editUser']]);
  dump($_POST['userEtat'] == 'active' || $_POST['userEtat'] == 'disactive');
  if ($_POST['userEtat'] == 'active' || $_POST['userEtat'] == 'disactive') {
    $stmt = $mysql->prepare(
      "UPDATE `user` SET `etat` = ?
    WHERE `user`.`id` = ?"
    );
    $stmt->bind_param(
      "si",
      $_POST['userEtat'],
      $_POST['editUser'],
    );
    $stmt->execute();
    if ($stmt->affected_rows == 1) {
      $stmt->close();
      header("Location: " . $_SERVER['REQUEST_URI'] . '&etat=edited');
    }
    $mysql->close(); // Close connection
  }
}

$editSelected = isset($fe)
  ? ($fe['etat'] == 'active' ? 'selected' : '')
  : ($f->etat == 'active' ? 'selected' : '');


$pageTitle = isset($fe) ? $fe['name'] : $f->name;
require('includes/templates/sidebar.php');
?>

<main>
  <h1 class="page-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
    </svg>
    <?= $pageTitle; ?>
  </h1>

  <section id="editSection">

    <?php if ($_SESSION['user']['role'] == 'administrateur' || $_SESSION['user']['role'] == 'gestionnaire' && !empty($_SESSION['user']['codeferme'])) : ?>
      <button type="button" id="toggleEdit" class="btn-out">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
          <path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
        Modifier la ferme</button>
    <?php endif; ?>

    <form action=<?= $_SERVER['REQUEST_URI']; ?> method="POST" id="editForm" class="ajoute-ferme readOnly">
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
          <option value="active" <?= $editSelected ?>>Active</option>
        </select>
      </div>
      <?php if ($_SESSION['user']['role'] == 'administrateur' || $_SESSION['user']['role'] == 'gestionnaire' && !empty($_SESSION['user']['codeferme'])) : ?>
        <div class="form-group">
          <button type="submit">Modifier</button>
        </div>
      <?php endif; ?>
    </form>
  </section>

  <?php if ($_SESSION['user']['role'] == 'administrateur' || $_SESSION['user']['role'] == 'gestionnaire') : ?>
    <section id="createUser">
      <h2 class="page-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        Create new User
      </h2>

      <form action=<?= $_SERVER['REQUEST_URI'] . '#createUser' ?> method="POST" class="create-user">
        <input type="hidden" name="create_user" value="tiri brk">
        <?php if (isset($cuError)) : ?>
          <div class="error"><?= $cuError ?></div>
        <?php endif; ?>
        <?php if (isset($cuSuccess)) : ?>
          <div class="success"><?= $cuSuccess ?></div>
        <?php endif; ?>
        <div class="form-group-2">
          <div>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" placeholder="Username" />
          </div>
          <div>
            <label for="password">Password</label>
            <input type="text" name="password" id="password" placeholder="Password" />
          </div>
        </div>

        <div class="form-group-2">
          <div>
            <label for="userEtat">
              Etat
              <select name="userEtat" id="userEtat">
                <option value="disactive">Disactive</option>
                <option value="active">Active</option>
              </select>
            </label>
          </div>
          <div>
            <label for="role">
              Role
              <select name="role" id="role">
                <option value=""></option>
                <option value="gestionnaire">Gestionnaire</option>
                <option value="operateur">Operateur</option>
                <option value="veterinaire">Veterinaire</option>
              </select>
            </label>
          </div>
        </div>
        <div class="form-group">
          <button type="submit">Create</button>
        </div>
      </form>

    </section>
  <?php endif; ?>

  <?php if ($_SESSION['user']['role'] == 'administrateur' || $_SESSION['user']['role'] == 'gestionnaire' && $_SESSION['user']['codeferme'] == $code) : ?>
    <section id="editUser">
      <h2 class="page-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
        </svg>
        Gérer les utilisateurs
      </h2>
      <?php if (isset($_GET['role'])) : ?>
        <div class="success">User Role a été Modifier avec succès.</div>
      <?php endif; ?>
      <?php if (isset($_GET['etat'])) : ?>
        <div class="success">User Etat a été Modifier avec succès.</div>
      <?php endif; ?>

      <div class="flex-table">
        <div class="flex-row flex-head">
          <div class="flex-cell">Username</div>
          <div class="flex-cell">Etat</div>
          <div class="flex-cell">Role</div>
        </div>

        <?php while ($row = $result->fetch_object()) : ?>
          <div class="flex-row">
            <div class="flex-cell"><?= $row->username ?></div>
            <div class="flex-cell">
              <form action="<?= $_SERVER['REQUEST_URI'] . "#editUser" ?>" method="POST">
                <input type="hidden" name="editUser" value="<?= $row->id ?>" />
                <select name="userEtat" class="editSelect">
                  <option value=""><?= $row->etat ?></option>
                  <option value="">--------------</option>
                  <option value="active">Active</option>
                  <option value="disactive">Disactive</option>
                </select>
              </form>
            </div>
            <div class="flex-cell">
              <form action="<?= $_SERVER['REQUEST_URI'] . "#editUser" ?>" method="POST">
                <input type="hidden" name="editUser" value="<?= $row->id ?>" />
                <select name="role" class="editSelect">
                  <option value=""><?= $row->role ?></option>
                  <option value="">--------------</option>
                  <option value="administrateur">Administrateur</option>
                  <option value="consultant">Consultant</option>
                  <option value="gestionnaire">Gestionnaire</option>
                  <option value="operateur">Operateur</option>
                  <option value="veterinaire">Veterinaire</option>
                </select>
              </form>
            </div>
          </div>


        <?php endwhile; ?>
      </div>


    </section>
  <?php endif; ?>

</main>
</div>
<script src="assets/js/ferme.js"></script>
</body>

</html>