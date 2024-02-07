<?php

require('config.php');


if (!isLoggedIn()) {
  header("Location: login.php");
  exit;
}

require('includes/header.php');

echo "Home Page";

require('includes/footer.php');
