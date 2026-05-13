<?php
  session_start();
  ini_set('session.cookie_httponly', 1);
  ini_set('session.cookie_secure', 1);   // только при HTTPS
  ini_set('session.cookie_samesite', 'Lax');
  session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'domain' => null,
      'secure' => true,
      'httponly' => true,
      'samesite' => 'Lax'
  ]);
  session_destroy();
  header('Location: login.php');
  exit();
?>
