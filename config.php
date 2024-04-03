<?php

include "includes/dbConn.php";
session_start();

function dump($var)
{
  echo "<pre>";
  var_dump($var);
  echo "</pre>";
}

function notFound()
{
  $pageTitle = "404 Page not found";
  require('includes/templates/sidebar.php');

  die("
    <main>
      <h1 class='page-title'>404 Page not found</h1>
      <br />
      <a href='/'>&lt;&lt; Retour à la page d'accueil</a>
    </main>
</div></body></html>");
}
