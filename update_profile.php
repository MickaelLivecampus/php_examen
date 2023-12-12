<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérez les données soumises dans le formulaire
    $newName = $_POST['new_name'];
    $newEmail = $_POST['new_email'];

    // Effectuez des validations nécessaires sur les données, par exemple, vérifiez si l'email est unique, etc.

    // Mettez à jour les informations de l'utilisateur dans la base de données
    $query = "UPDATE utilisateurs SET nom = :new_name, email = :new_email WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(
        ':new_name' => $newName,
        ':new_email' => $newEmail,
        ':user_id' => $_SESSION['user_id']
    ));

    // Redirigez l'utilisateur vers son profil mis à jour
    header('Location: profile.php');
}
?>
