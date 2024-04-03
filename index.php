<?php

require('config.php');


if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$pageTitle = "Home Page";
require('includes/templates/sidebar.php');

?>

<main>
  Page Content here
  <?php dump($_SESSION['user']); ?>
</main>
</div>
</body>

</html>