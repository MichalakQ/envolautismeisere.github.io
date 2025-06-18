<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

if (!hasRole("client")) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /transgourmet/index.php?message=$encodedMessage");
    exit;
}

$conn = openDatabaseConnection();

// === CHANGEMENT DE FAVORI ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favori'])) {
    $code_article =$_POST['code_article'];
    $current_favori = $_POST['current_favori'];
    $new_favori = $current_favori === 1 ? 0 : 1;

    $stmt = $conn->prepare("UPDATE referenciel SET favori = ? WHERE code_article = ?");
    $stmt->execute([$new_favori, $code_article]);
}

// === RÉCUPÉRATION DES DONNÉES ===
$stmt = $conn->query("SELECT * FROM referenciel ORDER BY designation_article");
$referenciel = $stmt->fetchAll(PDO::FETCH_ASSOC);

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>RÉFÉRENTIEL PRODUITS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            background-image: url('../assets/ref.jpg') !important;
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
        }
        .dore {
            color: #FFD700;
            text-shadow: 0 0 5px #FFD700, 0 0 10px #FFA500;
        }
        td {
            color: #fff !important;
            background-color: rgba(128, 128, 128, 0.5) !important;
            padding: 8px;
            border: 1px solid #ccc;
        }
        th {
            color: rgb(37, 4, 102) !important;
            font-weight: bold;
            background-color: rgba(128, 128, 128, 0.5) !important;
            padding: 10px;
            border: 1px solid #999;
            text-transform: uppercase;
        }
        .favori-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
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

<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>
<?php include '../assets/searchbarRef.php'; ?>

<h1 class="text-center dore">RÉFÉRENTIEL PRODUITS</h1>

<div class="container mt-4">
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Code</th>
                <th>Fournisseur</th>
                <th>Marque</th>
                <th>Gamme</th>
                <th>Désignation</th>
                <th>UF</th>
                <th>Qtés</th>
                <th>Prix Moyen</th>
                <th>CA</th>
                <th>Nb Lignes</th>
                <th>Favori</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($referenciel as $ref): ?>
            <tr>
                <td><?= htmlspecialchars($ref['code_article']) ?></td>
                <td><?= htmlspecialchars($ref['nom_fournisseur']) ?></td>
                <td><?= htmlspecialchars($ref['marque']) ?></td>
                <td><?= htmlspecialchars($ref['nom_gamme']) ?></td>
                <td><?= htmlspecialchars($ref['designation_article']) ?></td>
                <td><?= htmlspecialchars($ref['uf']) ?></td>
                <td><?= htmlspecialchars($ref['qtes']) ?></td>
                <td><?= number_format($ref['prix_moyen'], 2) ?> €</td>
                <td><?= number_format($ref['ca'], 2) ?> €</td>
                <td><?= htmlspecialchars($ref['nb_lignes']) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="code_article" value="<?= $ref['code_article'] ?>">
                        <input type="hidden" name="current_favori" value="<?= $ref['favori'] ?>">
                        <button type="submit" name="toggle_favori" class="favori-btn">
                            <?= $ref['favori'] == 1 ? '⭐' : '☆' ?>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
