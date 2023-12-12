<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est authentifié et a le rôle approprié (par exemple, "admin" ou "gestionnaire") pour accéder à cette fonctionnalité.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Assurez-vous que l'ID du livre que vous souhaitez modifier est passé en tant que paramètre (par exemple, dans l'URL).
if (!isset($_GET['book_id'])) {
    header('Location: books.php'); // Redirigez l'utilisateur vers la liste des livres ou une autre page appropriée.
    exit();
}

$book_id = $_GET['book_id'];

// Récupérez les détails du livre à partir de la base de données pour les afficher dans le formulaire de modification.
$query = "SELECT * FROM livres WHERE id = :book_id";
$stmt = $pdo->prepare($query);
$stmt->execute(array(':book_id' => $book_id));
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérez les données du formulaire de modification
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $date_publication = $_POST['date_publication'];
    $isbn = $_POST['isbn'];
    $coverUrl = $_POST['cover_url']; 

    // Mettez à jour les détails du livre dans la base de données
    $updateQuery = "UPDATE livres SET titre = :title, auteur = :author, description = :description, date_publication = :date_publication, isbn = :isbn, photo_url = :cover_url WHERE id = :book_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute(array(
        ':title' => $title,
        ':author' => $author,
        ':description' => $description,
        ':date_publication' => $date_publication,
        ':isbn' => $isbn,
          ':cover_url' => $coverUrl,
        ':book_id' => $book_id
    ));

    // Redirigez l'utilisateur vers la page de détails du livre mis à jour ou une autre page appropriée.
    header('Location: book_details.php?id=' . $book_id);

    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un Livre</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
        <h1>Modifier un livre - Librairie XYZ</h1>
    </header>
    <form method="post">
        <label for="title">Titre :</label>
        <input type="text" name="title" value="<?php echo $book['titre']; ?>" required>
        <br>
        <label for="author">Auteur :</label>
        <input type="text" name="author" value="<?php echo $book['auteur']; ?>" required>
        <br>
        <label for="description">Description :</label>
        <textarea name="description" required><?php echo $book['description']; ?></textarea>
        <br>
        <label for="date_publication">Date de Publication :</label>
        <input type="date" name="date_publication" value="<?php echo $book['date_publication']; ?>" required>
        <br>
        <label for="isbn">ISBN :</label>
        <input type="text" name="isbn" value="<?php echo $book['isbn']; ?>" required>
        <br>
        <label for="cover_url">URL de l'image :</label>
        <input type="text" name="cover_url" value="<?= htmlspecialchars($book['photo_url']); ?>" required>
        <br>
        <button type="submit">Enregistrer les Modifications</button>
    </form>
    <button onclick="window.location.href ='books.php'">Retour à la Liste des Livres</a>
</body>
</html>
