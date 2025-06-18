<?php
 // Gestion des messages d'erreurs
 if (isset($_GET['message'])) {
 $message = htmlspecialchars(urldecode($_GET['message'])); // limiter les injections XSS

 if (strpos($message, 'ERREUR') !== false) {
 echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>"
 .$message
 ."<button type='button' class='btn-close' data-bs-dismiss='alert'
aria-label='Close'></button>"
 ."</div>";
 } else {
 echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>"
 .$message
 ."<button type='button' class='btn-close' data-bs-dismiss='alert'
aria-label='Close'></button>"
 ."</div>";
 }
 }
?>