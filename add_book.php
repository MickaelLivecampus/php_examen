<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collectez les données du formulaire
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $date_publication = $_POST['date_publication'];
    $isbn = $_POST['isbn'];
    $coverPath = $_POST['cover'];  // Assurez-vous que la clé correspond au nom du champ de formulaire

    // Effectuez des validations (assurez-vous que les données sont correctes)
    if (empty($title)) {
        $errors[] = "Le titre du livre est requis.";
    }
    if (empty($date_publication)) {
        $errors[] = "La date de publication est requise.";
    }
    if (empty($isbn)) {
        $errors[] = "ISBN est requis.";
    }
    // Ajoutez d'autres validations ici...

    // Si aucune erreur de validation n'est présente
    if (empty($errors)) {
        $query = "INSERT INTO livres (titre, auteur, description, date_publication, isbn, photo_url) VALUES (:title, :author, :description, :date_publication, :isbn, :photo_url)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(
            ':title' => $title,
            ':author' => $author,
            ':description' => $description,
            ':date_publication' => $date_publication,
            ':isbn' => $isbn,
            ':photo_url' => $coverPath,
        ));

        // Indiquez que l'ajout du livre a réussi
        $success = true;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un Livre</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
        <img class="logo" src="image/logo.png" alt="Logo Librairie XYZ">
        <h1>Ajouter un livre - Librairie XYZ</h1>
    </header>

    <?php if ($success) : ?>
        <p>Le livre a été ajouté avec succès.</p>
        <button onclick="window.location.href = 'books.php'">Retour à la gestion des livres </button>
    <?php else : ?>
        <?php if (!empty($errors)) : ?>
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post">
            <label for="cover">URL de l'image :</label>
            <input type="text" name="cover" required>
            <label for="title">Titre :</label>
            <input type="text" name="title" required>
            <br>
            <label for="author">Auteur :</label>
            <input type="text" name="author" required>
            <br>
            <label for="description">Description :</label>
            <textarea name="description" required></textarea>
            <br>
            <label for="date_publication">Date de Publication :</label>
            <input type="date" name="date_publication" required>
            <br>
            <label for="isbn">ISBN :</label>
            <input type="text" name="isbn" required>
            <br>
            <button type="submit">Ajouter le Livre</button>
        </form>
    <?php endif; ?>
</body>
</html>
