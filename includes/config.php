<?php
// Paramètres de connexion à la base de données
define('DB_SERVER', 'mysql-tp-git-dev.mysql.database.azure.com');    // Serveur de la base de données
define('DB_USERNAME', 'esgiuser');                                           // Nom d'utilisateur de la base de données
define('DB_PASSWORD', 'P@ssw0rd123!');                                           // Mot de passe de la base de données
define('DB_NAME', 'party_planner_db');                               // Nom de la base de données

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERREUR : Impossible de se connecter. " . $e->getMessage());
}
?>
