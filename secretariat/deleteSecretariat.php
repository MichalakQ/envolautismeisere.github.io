<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

if (!hasRole("manager")) {
 $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
 header("Location: /transgourmet/index.php?message=$encodedMessage");
 exit;
 }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: listSecretariat.php");
    exit;
}

$conn = openDatabaseConnection();

$stmt = $conn->prepare("SELECT * FROM lieu WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    header("Location: listSecretariat.php");
    exit;
}

// **Si tu as une table liée à lieu (ex: commandes, réservations), adapte cette partie :**
// Ici, je suppose qu’il n’y a pas de dépendances, sinon il faudrait vérifier les liens
$hasDependances = false;
$countDependances = 0;

// Traitement de la suppression si confirmée
if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    /*
    // Si dépendances et suppression demandée :
    if ($hasDependances && isset($_POST['delete_dependances']) && $_POST['delete_dependances'] === 'yes') {
        // Suppression des dépendances ici
    } elseif ($hasDependances) {
        header("Location: listMaisonViolette.php?error=1");
        exit;
    }
    */
    
    // Suppression du produit
    $stmt = $conn->prepare("DELETE FROM lieu WHERE id = ?");
    $stmt->execute([$id]);
    
    closeDatabaseConnection($conn);
    header("Location: listSecretariat   .php?deleted=1");
    exit;
}

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Supprimer un produit</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" />
</head>
<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

<div class="container mt-5">
    <h1>Supprimer un produit</h1>

    <div class="alert alert-warning">
        <p><i class="fa fa-exclamation-triangle"></i> <strong>Attention :</strong> Vous êtes sur le point de supprimer le produit <strong><?= htmlspecialchars($produit['designation']) ?></strong>.</p>
    </div>

    <!-- Si tu as des dépendances à gérer, affiche l’alerte ici -->
    <?php if ($hasDependances): ?>
        <div class="alert alert-danger">
            <p><i class="fa-solid fa-skull-crossbones"></i> Ce produit est associé à <?= $countDependances ?> dépendance(s).</p>
            <p>La suppression affectera ces données liées.</p>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php if ($hasDependances): ?>
            <div class="form-check mb-3">
                <input type="checkbox" id="delete_dependances" name="delete_dependances" value="yes" class="form-check-input" />
                <label for="delete_dependances" class="form-check-label">Supprimer également les dépendances associées</label>
            </div>
        <?php endif; ?>

        <p>Êtes-vous sûr de vouloir supprimer ce produit ?</p>

        <input type="hidden" name="confirm" value="yes" />

        <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
        <a href="listSecretariat.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
