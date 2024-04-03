<?php

require('config.php');

if (isset($_SESSION['user'])) {
  header("Location: index.php");
  exit;
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user = filter_var(trim($_POST['user']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $pass = filter_var(trim($_POST['pass']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  if (strlen($user) < 4 || strlen($pass) < 4) {
    $error .= "Please fill all the fields";
  } else {
    $stmt = $mysql->prepare(
      "SELECT user.*, compte.*
        FROM user 
        JOIN compte 
        ON user.id = compte.id
        WHERE user.etat = 'active'
        AND user.username = ?;"
    );
    $stmt->bind_param("s", $user);
    $stmt->execute();
    // Get result
    $result = $stmt->get_result();
    if ($result->num_rows !== 1) {
      $error .= "Wrong Credentials.";
    } else {
      $user = $result->fetch_assoc();
      $stmt->close();
      $mysql->close(); // Close connection
      if (password_verify($pass, $user['password'])) {
        unset($user['password'], $user['etat']);
        $_SESSION['user'] = $user;
        if (isset($user['prenom'])) {
          $success = "<span>" . $user['prenom'] . "</span>! Logged in Perfectly";
          header("REFRESH: 3; URL=index.php?loggedIn=true");
        } else {
          $success = "<span>Connecté</span>, mais vous devez d'abord Modifier votre Compte";
          header("REFRESH: 5; URL=modifier-compte.php");
        }
      } else {
        $error .= "Wrong Credentials";
      }
    }
  }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connecter à votre compte | FarmPro</title>
  <!-- My Css -->
  <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" class="card">

    <h1 class="logo">my<span>Farm</span></h1>

    <p class="desc">Ici, vous pouvez connecter à votre compte</p>

    <?php if (isset($success)) : ?>
      <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="form-group">
      <label for="username">Email or Username</label>
      <input type="text" name="user" id="username" placeholder="Enter votre email or username" />
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="pass" id="password" placeholder="Enter votre password" />
    </div>

    <?php if (!empty($error)) : ?>
      <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <div class="form-group">
      <button type="submit">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
        Login
      </button>
    </div>
  </form>
</body>

</html>