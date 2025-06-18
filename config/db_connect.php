<?php
// Fonction de connexion à la base de données qui retourne le handle
function openDatabaseConnection() {
$host = 'localhost';
$db = 'Transgourmet';
$user = 'root';
$pass = '';
try {
// Utilisation de PDO plutôt que MySQLi
$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
return $conn;
} catch(PDOException $e) {
echo "Connection failed: " . $e->getMessage();
exit;
}
}
function closeDatabaseConnection($conn) {
$conn = null; // Destructeur se charge de clore la connexion
}
?>
