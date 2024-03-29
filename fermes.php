<?php

require('config.php');

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$pageTitle = "Add Ferme";
require('includes/templates/sidebar.php');
?>
<main>
  <h1>Here I Display All fermes</h1>
</main>

</div>
</body>

</html>