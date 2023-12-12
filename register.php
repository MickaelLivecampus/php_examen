<?php
require('config.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérification de l'existence de l'email
    $query = "SELECT * FROM utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':email' => $email));

    if ($stmt->rowCount() > 0) {
        $error = "Cet email est déjà utilisé.";
    } else {
        // Hasher le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insertion de l'utilisateur dans la base de données avec le mot de passe hashé
        $query = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (:name, :prenom, :email, :password)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':name' => $name, ':prenom' => $prenom, ':email' => $email, ':password' => $hashedPassword));


        if ($stmt) {
            // Redirection vers la page de connexion
            header('Location: login.php');
        } else {
            $error = "Erreur lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
        <h1>Inscription - Librairie XYZ</h1>
    </header>
    <form method="post" action="">
        <input type="text" name="name" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prenom" required>
        <input type="text" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>

    <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    <p>Vous avez déjà un compte ? <a href="login.php">Connectez-vous ici</a>.</p>
</body>
</html>
