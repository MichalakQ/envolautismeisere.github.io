<?php
require_once '../config/db_connect.php';
$conn = $conn ?? openDatabaseConnection();

// === CHARGEMENT DES DONNÉES ===
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = '%' . trim($_GET['search']) . '%';

    $stmt = $conn->prepare("
        SELECT * FROM referenciel 
        WHERE nom_fournisseur LIKE ?
           OR marque LIKE ?
           OR nom_gamme LIKE ?
           OR CAST(code_article AS CHAR) LIKE ?
           OR designation_article LIKE ?
           OR uf LIKE ?
        ORDER BY designation_article
    ");
    $stmt->execute([
        $search, $search, $search,
        $search, $search, $search
    ]);
} else {
    $stmt = $conn->query("SELECT * FROM referenciel ORDER BY designation_article");
}
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="get" class="text-center my-4">
    <input 
        type="text" 
        name="search" 
        placeholder="Rechercher par fournisseur, marque, article, etc." 
        class="form-control w-50 d-inline-block" 
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
    >
    <button type="submit" class="btn btn-primary ms-2"><i class="fa fa-search"></i></button>
</form>
