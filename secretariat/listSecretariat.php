<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

$conn = openDatabaseConnection();
if (!hasRole("manager")) {
 $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
 header("Location: /transgourmet/index.php?message=$encodedMessage");
 exit;
 }

// === TRAITEMENT DU FORMULAIRE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stock']) && isset($_POST['reste'])) {
    foreach ($_POST['stock'] as $id => $stock) {
        $stock = intval($stock);
        $reste = isset($_POST['reste'][$id]) ? intval($_POST['reste'][$id]) : 0;

        $stmt = $conn->prepare("UPDATE lieu SET stock = ?, reste = ? WHERE id = ?");
        $stmt->execute([$stock, $reste, $id]);
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
}

// === CHARGEMENT DES DONNÉES POUR AFFICHAGE ===
$stmt = $conn->query("SELECT * FROM lieu ORDER BY designation");
$lieu = $stmt->fetchAll(PDO::FETCH_ASSOC);

closeDatabaseConnection($conn);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inventaire</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            background-image: url('../assets/sec.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }
        .dore {
            color: #FFD700;
            text-shadow: 0 0 5px #FFD700, 0 0 10px #FFA500;
        }
td {
    color:rgb(255, 255, 255)!important;                                
    background-color: rgba(128, 128, 128, 0.5)!important;    /* Gris clair transparent */
    padding: 8px;
    border: 1px solid #ccc;
}

th {
    color:rgb(37, 4, 102)!important;                                 /* Noir foncé lisible */
    font-weight: bold;
    background-color: rgba(128, 128, 128, 0.5) !important;   /* Gris plus opaque */
    padding: 10px;
    border: 1px solid #999;
    text-transform: uppercase;
}
.fa-pencil-square {
    color:rgb(33, 253, 13); /* bleu Bootstrap */
}

.fa-trash {
    color: #dc3545; /* rouge Bootstrap */
}

    </style>
</head>
<body>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success text-center">Modifications enregistrées avec succès !</div>
<?php endif; ?>

<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>
<?php include '../assets/searchbarLieu.php'; ?>

<h1 class="text-center dore">INVENTAIRE</h1>

<a href="createSecretariat.php" class="btn btn-primary m-3">
    <i class="fas fa-plus"></i> Ajouter un élément
</a>




<form method="post">
    <table class="table table-bordered w-75 mx-auto text-center">
        <thead>
            <tr>
                <th>Résidence</th>
                <th>Référence</th>
                <th>Désignation</th>
                <th>A commander</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($lieu as $lieu): ?>
            <tr>
                <td><?= htmlspecialchars($lieu['nom']) ?></td>
                <td><?= htmlspecialchars($lieu['reference']) ?></td>
                <td><?= htmlspecialchars($lieu['designation']) ?></td>
                <td><?= htmlspecialchars($lieu['commande']) ?></td>
                <td>
                    <a href="editSecretariat.php?id=<?= $lieu['id'] ?>"><i class="fa fa-pencil-square fa-2x"></i></a> |
                    <a href="deleteSecretariat.php?id=<?= $lieu['id'] ?>" onclick="return confirm('Êtes-vous sûr ?')"><i class="fa fa-trash fa-2x"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center mb-4">
        <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>