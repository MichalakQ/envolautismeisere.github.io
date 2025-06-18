<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

$conn = openDatabaseConnection();

if (!(hasRole("directeur") || hasRole("chef"))) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /transgourmet/index.php?message=$encodedMessage");
    exit;
}

// === CHARGEMENT DES DONNÉES EMPLOYÉS ===
$stmt = $conn->query("SELECT * FROM employes ORDER BY username");
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration Employés</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            background-image: url('../assets/admin.jpg');
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
<?php include '../assets/searchbarEmp.php'; ?>

<h1 class="text-center dore">Administration des employés</h1>

<a href="createEmp.php" class="btn btn-primary m-3">
Ajouter un employé
</a>

<table class="table table-bordered w-75 mx-auto text-center">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Date de création</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($employes as $emp): ?>
        <tr>
            <td><?= htmlspecialchars($emp['id']) ?></td>
            <td><?= htmlspecialchars($emp['username']) ?></td>
            <td><?= htmlspecialchars($emp['email']) ?></td>
            <td><?= htmlspecialchars($emp['role']) ?></td>
            <td><?= htmlspecialchars($emp['created_at']) ?></td>
            <td>
                <a href="editEmp.php?id=<?= $emp['id'] ?>" class="btn">
                    <i class="fa fa-pencil-square fa-2x"></i>
                </a>

                <a href="deleteEmp.php?id=<?= $emp['id'] ?>" class="btn" onclick="return confirm('Êtes-vous sûr ?')">
                    <i class="fa fa-trash fa-2x"></i>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
