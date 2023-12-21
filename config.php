<?php
require_once('session.php');


$host = 'www.livecampus.hexolis.com';
$dbname = 'library';
$username = 'admin';
$password = 'cEU4r.fCJVFLD!d%2P):[u';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
