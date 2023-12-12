<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include("config.php");
include("header.php");

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page === 'login') {
    echo "Avant inclusion du login.php"; // Débogage
    include("login.php");
    echo "Après inclusion du login.php"; // Débogage
} elseif ($page === 'books') {
    include("books.php");
} else {
    include("home.php");
}

include("footer.php");
?>
