<?php
    require_once '../config/db_connect.php';
    require_once 'authFunctions.php';

    logoutUser();
    error_log("transgourmet : disconnect user");
    $encodedMessage = urlencode("SUCCES: Vous êtes maintenant déconnecté.");
    header("Location: http://transgourmet.calc/index.php?message=$encodedMessage");

?>
