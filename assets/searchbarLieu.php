<?php
require_once '../config/db_connect.php';
$conn = $conn ?? openDatabaseConnection();

// === CHARGEMENT DES DONNÉES ===
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = '%' . trim($_GET['search']) . '%';

    $stmt = $conn->prepare("
        SELECT * FROM lieu 
        WHERE designation LIKE ?
           OR nom LIKE ?
           OR CAST(reference AS CHAR) LIKE ?
           OR nom_fournisseur LIKE ?
           OR marque LIKE ?
           OR nom_gamme LIKE ?
           OR designation_article LIKE ?
        ORDER BY designation
    ");
    $stmt->execute([
        $search, $search, $search,
        $search, $search, $search, $search
    ]);
} else {
    $stmt = $conn->query("SELECT * FROM lieu ORDER BY designation");
}
$lieu = $stmt->fetchAll(PDO::FETCH_ASSOC); // N'oublie pas de récupérer les résultats !
?>
<form method="get" class="text-center my-4">
    <input 
        type="text" 
        name="search" 
        placeholder="Rechercher par désignation, fournisseur, référence, etc." 
        class="form-control w-50 d-inline-block" 
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
    >
    <button type="submit" class="btn btn-primary ms-2"><i class="fa fa-search"></i></button>
</form>
