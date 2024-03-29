<?php

require('config.php');


if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

require('includes/templates/header.php');

echo "Home Page";

require('includes/templates/footer.php');
