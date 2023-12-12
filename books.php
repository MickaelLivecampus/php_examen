<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Livres - Librairie XYZ</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
       
            color: #fff;
            text-align: center;
            padding: 1em 0;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
    
            color: #fff;
        }

        .book-image {
            max-width: 100px; /* Ajustez la taille maximale de l'image selon vos besoins */
            height: auto;
        }

        button {
     
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>

    <!-- Ajoutez des médias requêtes pour le style responsive -->
    <style>
        @media (max-width: 768px) {
            .container {
                width: 100%;
            }

            table {
                font-size: 14px;
            }

            .book-image {
                max-width: 50px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Liste des Livres - Librairie XYZ</h1>
    </header>

    <div class="container">
        <!-- Affichage des livres depuis la base de données -->
        <?php
        require('config.php');

        $query = "SELECT * FROM livres";
        $stmt = $pdo->query($query);

        if ($stmt) {
            echo "<table>";
            echo "<tr><th>Image</th><th>Titre</th><th>Auteur</th><th>Date de publication</th><th>Statut</th><th>Détails</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo '<td><img class="book-image" src="' . $row['photo_url'] . '" alt="' . $row['titre'] . '"></td>';
                echo "<td>{$row['titre']}</td>";
                echo "<td>{$row['auteur']}</td>";
                echo "<td>{$row['date_publication']}</td>";
                echo "<td>{$row['statut']}</td>";
                echo '<td><a href="book_details.php?id=' . $row['id'] . '">Voir les détails</a></td>';
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Erreur lors de la récupération des livres.";
        }
        ?>
  <!-- Bouton "Ajouter un livre" visible uniquement pour les admins -->
        <?php if ($_SESSION['role'] === 'admin') : ?>
            <button onclick="window.location.href = 'add_book.php'">Ajouter un livre</button>
        <?php endif; ?>
        <button onclick="window.location.href = 'index.php'">Retour à l'accueil</button>
        
    </div>
</body>
</html>
