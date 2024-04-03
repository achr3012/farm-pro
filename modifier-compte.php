<?php

require('config.php');


if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $input = filter_var_array(array_map('trim', $_POST), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  if (
    strlen($input['nom']) < 4 ||            strlen($input['prenom']) < 4 ||
    strlen($input['email']) < 4 ||         strlen($input['date']) < 4 ||
    strlen($input['fonction']) < 4 || strlen($input['affiliation']) < 4
  ) {
    $error = "Please fill all the fields.";
  } else {



    $stmt = $mysql->prepare(
      "UPDATE `compte`
      SET `nom` = ?, `prenom` = ?, `email` = ?, `date_naissance` = ?, `fonction` = ?, `affiliation` = ?
      WHERE `compte`.`id` = ?"
    );

    $stmt->bind_param(
      "ssssssi",
      $input['nom'],
      $input['prenom'],
      $input['email'],
      $input['date'],
      $input['fonction'],
      $input['affiliation'],
      $_SESSION['user']['id']
    );

    // Execute the statement
    if ($stmt->execute()) {
      $_SESSION['user'] = array_merge($_SESSION['user'], $input);
      $success = "Votre compte a été créé avec succès.";
      $stmt->close();
    }
    $mysql->close(); // Close connection
  }
}

$pageTitle = "Modifier votre Compte";
require('includes/templates/sidebar.php');

?>

<main>
  <h1 class="page-title"><?= $pageTitle ?> <?= $_SESSION['user']['username'] ?></h1>
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" class="create-account">
    <?php if (isset($error)) : ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($success)) : ?>
      <div class="success"><?= $success ?></div>
    <?php endif; ?>
    <div class="form-group-2">
      <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="<?= $_SESSION['user']['nom'] ?>" placeholder="Enter votre nom" />
      </div>

      <div class="form-group">
        <label for="prenom">Prenom</label>
        <input type="text" name="prenom" id="prenom" value="<?= $_SESSION['user']['prenom'] ?>" placeholder="Enter votre prenom" />
      </div>
    </div>
    <div class="form-group-2">
      <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= $_SESSION['user']['email'] ?>" placeholder="Enter votre address email" />
      </div>

      <div>
        <label for="date">La date de naissance</label>
        <input type="date" name="date" id="date" value="<?= $_SESSION['user']['date_naissance'] ?>" />
      </div>
    </div>
    <div class="form-group-2">
      <div class="form-group">
        <label for="fonction">Fonction</label>
        <input type="text" name="fonction" id="fonction" value="<?= $_SESSION['user']['fonction'] ?>" placeholder="Enter votre fonction" />
      </div>

      <div class="form-group">
        <label for="affiliation">Affiliation</label>
        <input type="text" name="affiliation" id="affiliation" value="<?= $_SESSION['user']['affiliation'] ?>" placeholder="Enter votre affiliation" />
      </div>
    </div>

    <div class="form-group">
      <button type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        </svg>
        Modifier mon compte
      </button>
    </div>
  </form>
</main>
</div>
</body>

</html>