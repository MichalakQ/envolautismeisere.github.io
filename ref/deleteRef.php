<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

// Accès réservé au directeur uniquement
if (!hasRole("manager")) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /transgourmet/index.php?message=$encodedMessage");
    exit;
}

// Vérifie que l'ID est valide
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: listRef.php");
    exit;
}

$conn = openDatabaseConnection();

// Récupération de l'employé
$stmt = $conn->prepare("SELECT * FROM referenciel WHERE id = ?");
$stmt->execute([$id]);
$employe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employe) {
    closeDatabaseConnection($conn);
    header("Location: listRef.php");
    exit;
}

// Suppression après confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $stmt = $conn->prepare("DELETE FROM referenciel WHERE id = ?");
    $stmt->execute([$id]);
    closeDatabaseConnection($conn);

    header("Location: listRef.php?deleted=1");
    exit;
}

closeDatabaseConnection($conn);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Supprimer un article</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet" />
</head>
<body>

<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

<div class="container mt-5">
    <h1>Supprimer un article</h1>

    <div class="alert alert-warning">
        <p><i class="fa fa-exclamation-triangle"></i> <strong>Attention :</strong> Vous êtes sur le point de supprimer l'article <strong><?= htmlspecialchars(article['marque' ]) ?></strong> (<?= htmlspecialchars($employe['designation']) ?>).</p>
    </div>

    <form method="post">
        <input type="hidden" name="confirm" value="yes" />
        <p>Confirmez-vous cette suppression ? Cette action est irréversible.</p>

        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
        <a href="listRef.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
