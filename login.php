<?php

require('config.php');

if (isLoggedIn()) {
  header("Location: index.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $passw = filter_var($_POST['password'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $errors = [];

  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($passw)) {
    array_push($errors, "Invalid Informaion");
  }


  if (empty($errors)) {
    $_SESSION['user'] = ['id' => 1, "name" => "Achraf", "email" => "achr3012@gmail.com", "role" => 1];
    header("Location: /");
  }

  // echo "Email: $email;;;Password: $passw";
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Farm-Pro</title>
  <link rel="stylesheet" href="assets/css/login.css" />
</head>

<body>

  <h1>Log In with ur Credentials</h1>
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">

    <?php
    // To display $errors
    if (!empty($errors)) {
      echo "<ul class='error'>";

      foreach ($errors as $value) {
        echo "<li>" . $value . "</li>";
      }

      echo "</ul>";
    }
    ?>
    <label for="email">Email Address</label>
    <input type="text" name="email" id="email" required />

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required />

    <button role="submit" type="submit">Login</button>
  </form>

</body>

</html>