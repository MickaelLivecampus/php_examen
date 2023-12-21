<?php
require_once('session.php');
require('config.php');
require('csrfToken.php');

$error = false;
$emprunt = null;

if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    // Récupérez les détails du livre depuis la base de données en utilisant $bookId
    $query = "SELECT * FROM livres WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':id' => $bookId));

    if ($stmt->rowCount() == 1) {
        $book = $stmt->fetch();

        $query = "SELECT e.id_utilisateur, e.id FROM emprunts e WHERE e.id_livre = :idLivre AND e.date_retour_effective IS NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':idLivre' => $bookId));

        $emprunt = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($emprunt);
    } else {
        // Livre non trouvé, gérer l'erreur ici
    }
} else {
    // ID de livre non spécifié dans l'URL, gérer l'erreur ici
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // mettre à jour le status du du livre en disponible
    if ($book['statut'] == "emprunté" && $emprunt["id_utilisateur"] == $_SESSION["user_id"]) {

        $empruntId = htmlspecialchars(filter_input(INPUT_POST, 'emprunt_id'), ENT_QUOTES);
        $dateRetourEffective = htmlspecialchars(filter_input(INPUT_POST, 'date_retour_effective'), ENT_QUOTES);
        $book_id = htmlspecialchars(filter_input(INPUT_POST, 'book_id'), ENT_QUOTES);
        $csrfToken = filter_input(INPUT_POST, "csrf_token");

        if (verifyCSRFToken($csrfToken)) {

            $sql = "UPDATE emprunts SET date_retour_effective = :dateRetourEffective WHERE id = :empruntId";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':dateRetourEffective', $dateRetourEffective, PDO::PARAM_STR);
            $stmt->bindParam(':empruntId', $empruntId, PDO::PARAM_INT);

            $stmt->execute();

            // mettre à jour le status du libre 
            $updateQuery = "UPDATE livres SET statut = :statut WHERE id = :book_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute(array(
                ':statut' => "disponible",
                ':book_id' => $book_id
            ));

            header("Location: emprunts.php");
            exit();

        } else {
            $error = 'Veuillez réessayer';
        }

    } else {
        $sql = 'INSERT INTO emprunts (id_utilisateur, id_livre, date_emprunt, date_retour_prevue) VALUES (:id_utilisateur, :id_livre, :date_emprunt, :date_retour_prevue)';
    
        $stmt = $pdo->prepare($sql);
    
        $user_id = htmlspecialchars(filter_input(INPUT_POST, 'user_id'), ENT_QUOTES);
        $book_id = htmlspecialchars(filter_input(INPUT_POST, 'book_id'), ENT_QUOTES);
        $date_emprunt = date('Y-m-d');
        $date_retour_prevue = htmlspecialchars(filter_input(INPUT_POST, 'date_retour_prevue'), ENT_QUOTES);
        $csrfToken = filter_input(INPUT_POST, "csrf_token");
    
    
        // Checks si le libre est disponible 
        $query = "SELECT * FROM livres WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':id' => $book_id));
    
        if ($stmt->rowCount() == 1) {
            $book = $stmt->fetch();
    
            if ($book['statut'] == "disponible") {
    
    
                $form_data = [
                    "user_id" => $user_id,
                    "book_id" => $book_id,
                    "date_emprunt" => $date_emprunt,
                    "date_retour_prevue" => $date_retour_prevue,
                ];
    
                $dateMin = date('Y-m-d', strtotime('+1 day'));
                $dateMax = date('Y-m-d', strtotime('+30 days'));
    
                // test si les champs sont vides
                foreach ($form_data as $key => $field) {
                    if (!isset($field) || empty($field)) {
                        $error = "Field '$key' is empty or not defined";
                    }
                }
    
                // test si la date ne dépasse pas 30 jours ou avant (J-1)
                if ($date_retour_prevue >= $dateMin && $date_retour_prevue <= $dateMax) {
    
                    if (verifyCSRFToken($csrfToken)) {
    
                        $stmt = $pdo->prepare($sql);
                        // La date est valide, procéder à l'enregistrement de l'emprunt
                        $stmt->bindParam('id_utilisateur', $user_id, PDO::PARAM_INT);
                        $stmt->bindParam('id_livre', $book_id, PDO::PARAM_INT);
                        $stmt->bindParam('date_emprunt', $date_emprunt, PDO::PARAM_STR);
                        $stmt->bindParam('date_retour_prevue', $date_retour_prevue, PDO::PARAM_STR);
                        $stmt->execute();
    
                        // mettre à jour le status du libre 
                        $updateQuery = "UPDATE livres SET statut = :statut WHERE id = :book_id";
                        $updateStmt = $pdo->prepare($updateQuery);
                        $updateStmt->execute(array(
                            ':statut' => "emprunté",
                            ':book_id' => $book_id
                        ));
    
                        header("Location: emprunts.php");
                        exit();
    
                    } else {
                        $error = 'Veuillez réessayer';
                    }
                } else {
                    // La date n'est pas valide, afficher un message d'erreur
                    $error = "La date n'est pas valide";
                }
            } else {
                $error = "Le libre n'est pas disponible";
            }
        } else {
            $error = "Le libre n'a pas été trouvé";
        }
    }

}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Détails du Livre</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .book-image {
            max-width: 30%;
            height: auto;
            display: block;
            margin: 0 auto;
            /* Pour centrer l'image */
        }
    </style>
</head>

<body>
    <header>
        <h1>Détails du Livre</h1>
    </header>
    <div class="container">
        <div class="details">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            <?php if (isset($book)): ?>
                <h3>
                    <?= htmlspecialchars($book['titre']); ?>
                </h3>

                <?php echo '<img class="book-image" src="' . htmlspecialchars($book['photo_url']) . '" alt="' . htmlspecialchars($book['titre']) . '">'; ?>
                <p>Auteur :
                    <?= htmlspecialchars($book['auteur']); ?>
                </p>
                <p>Année de publication :
                    <?= htmlspecialchars($book['date_publication']); ?>
                </p>
                <p>disponible :
                    <?= htmlspecialchars($book['statut']); ?>
                </p>
                <p>ISBN :
                    <?= htmlspecialchars($book['isbn']); ?>
                </p>
                <!-- Ajoutez l'URL de l'image ici -->
                <p>URL de l'image :
                    <?= htmlspecialchars($book['photo_url']); ?>
                </p>
                <!-- Autres détails du livre à afficher ici -->
            <?php else: ?>
                <p>Livre non trouvé</p>
            <?php endif; ?>
        </div>
        <div class="back-button">
            <?php if ($book['statut'] == "emprunté" && $emprunt != null && $emprunt["id_utilisateur"] == $_SESSION["user_id"]): ?>
                <form method="post">
                    <input type="hidden" name="emprunt_id" value="<?= $emprunt["id"]; ?>" />
                    <input type="hidden" name="book_id" value="<?= $bookId; ?>" />
                    <label for="date_retour_prevue">Selectionner la date de rendu:</label>
                    <input type="date" id="date_retour_effective" name="date_retour_effective"
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= $bookId; ?>" required />
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                    <button type="submit">Rendre le livre</button>
                </form>
            <?php elseif ($book['statut'] !== "emprunté"): ?>
                <form method="post">
                    <input type="hidden" name="book_id" value="<?= $bookId; ?>" />
                    <input type="hidden" name="user_id" value="<?= $_SESSION["user_id"] ?>" />
                    <label for="date_retour_prevue">Date de retour prévue:</label>
                    <input type="date" id="date_retour_prevue" name="date_retour_prevue"
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                        value="<?= $bookId; ?>" required />
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                    <!-- TODO DISABLED CSS -->
                    <!-- <button <?= $book['statut'] == "emprunté" ? "disabled" : ""; ?> type="submit">Emprunter le libre</button> -->
                    <button type="submit">Emprunter le livre</button>
                </form>
            <?php endif; ?>
            <button onclick="window.location.href = 'books.php'">Retour à la liste des livres</button>

            <?php
            // Ajoutez une vérification du rôle de l'utilisateur
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                // Si l'utilisateur est un administrateur, affichez les boutons "Modifier" et "Supprimer"
                echo '<button onclick="window.location.href = \'edit_book.php?book_id=' . $bookId . '\'">Modifier le livre</button>';
                echo '<button onclick="showDeleteConfirmation(' . $bookId . ')">Supprimer le livre</button>';
            }
            ?>

        </div>

    </div>
</body>
<script>
    function showDeleteConfirmation(bookId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce livre ?")) {
            // Si l'utilisateur confirme la suppression, redirigez-le vers la page de suppression avec l'ID du livre.
            window.location.href = "delete_book.php?book_id=" + bookId;
        } else {
            // Si l'utilisateur annule la suppression, ne faites rien.
        }
    }
</script>

</html>