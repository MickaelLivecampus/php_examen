<?php
require('config.php');


// Récupérer le nombre total de livres
$queryTotalBooks = "SELECT COUNT(*) as total_books FROM livres";
$stmtTotalBooks = $pdo->prepare($queryTotalBooks);
$stmtTotalBooks->execute();
$resultTotalBooks = $stmtTotalBooks->fetch(PDO::FETCH_ASSOC);


// Récupérer le nombre d'utilisateurs enregistrés
$queryTotalUsers = "SELECT COUNT(*) as total_users FROM utilisateurs";
$stmtTotalUsers = $pdo->prepare($queryTotalUsers);
$stmtTotalUsers->execute();
$resultTotalUsers = $stmtTotalUsers->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accueil</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
        <h1>Librairie XYZ</h1>
    </header>

<div class="wrapper">
        <!-- Sidebar -->
       <nav id="sidebar">
    <ul>
        <?php if (isset($_SESSION['user'])) : ?>
            <li>Bonjour <?= $_SESSION['prenom']; ?></li>
            <li><a href="books.php">Voir la liste des livres</a></li>
            <li><a href="profile.php">Mon profil</a></li>
            <li><a href="logout.php">Deconnexion</a></li>
        <?php else : ?>
            <li><a href="login.php">Connexion</a></li>
            <li><a href="register.php">Inscription</a></li>
        <?php endif; ?>
    </ul>
</nav>
        

        <!-- Page Content -->
        <div id="content">
            <div class="container">
                
                <!-- Votre contenu principal va ici -->
                <div id="content">
                <h1>Dashboard</h1>
    <div class="container">
        
    <div class="statistic">
        
            <h3>Total des Livres</h3>
            <p><?php echo $resultTotalBooks['total_books']; ?></p>
        </div>


        <div class="statistic">
            <h3>Utilisateurs Enregistrés</h3>
            <p><?php echo $resultTotalUsers['total_users']; ?></p>
        </div>

        <!-- ... Autres statistiques ... -->
    </div>
</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
    <div class="container">
        <p>&copy; <?= date("Y"); ?> Librairie XYZ</p>
    </div>
</footer>
</body>
</html>