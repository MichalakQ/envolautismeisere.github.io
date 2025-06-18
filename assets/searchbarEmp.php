<?php
require_once '../config/db_connect.php';
$conn = $conn ?? openDatabaseConnection();

// === CHARGEMENT DES DONNÉES ===
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = '%' . trim($_GET['search']) . '%';

    $stmt = $conn->prepare("
        SELECT * FROM employes 
        WHERE username LIKE ?
           OR email LIKE ?
           OR role LIKE ?
           OR CAST(id AS CHAR) LIKE ?
        ORDER BY username
    ");
    $stmt->execute([
        $search, $search, $search, $search
    ]);
} else {
    $stmt = $conn->query("SELECT * FROM employes ORDER BY username");
}
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="get" class="text-center my-4">
    <input 
        type="text" 
        name="search" 
        placeholder="Rechercher par nom d'utilisateur, email, rôle, ID..." 
        class="form-control w-50 d-inline-block" 
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
    >
    <button type="submit" class="btn btn-primary ms-2"><i class="fa fa-search"></i></button>
</form>
