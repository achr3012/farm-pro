<?php

require('config.php');


if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$pageTitle = "Home Page";
require('includes/templates/sidebar.php');
dump($_SESSION['user'])
?>

<main>Page Content here</main>
</div>
</body>

</html>