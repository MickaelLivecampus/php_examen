<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Récupération des informations de l'utilisateur depuis la base de données
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM utilisateurs WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(array(':user_id' => $user_id));
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);



?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil de l'Utilisateur</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
        <h1>Mon profil - Librairie XYZ</h1>
    </header>
    <p>Nom : <?php echo $userInfo['nom']; ?></p>
    <p>Email : <?php echo $userInfo['email']; ?></p>
    <!-- Affichez d'autres informations du profil ici -->
    <button onclick="window.location.href ='edit_profile.php'">Modifier le Profil</button>
    <button onclick="window.location.href ='index.php'">Retour à l'accueil</button>

</body>
</html>