<?php

include "includes/dbConn.php";
session_start();

function dump($var)
{
  echo "<pre>";
  var_dump($var);
  echo "</pre>";
}
