<?php
// Détruire la session de l'utilisateur
session_start();
session_unset();
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
?>
