<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

if (!hasRole("manager")) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /transgourmet/index.php?message=$encodedMessage");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $role      = $_POST['role'] ?? 'standard';
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT); // ✅ sécurisation

    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("INSERT INTO employes (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);
    closeDatabaseConnection($conn);

    header("Location: listEmp.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un employé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body {
            background-image: url('../assets/admin.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .dore {
            color: #FFD700;
            text-shadow: 0 0 5px #FFD700, 0 0 10px #FFA500;
        }
    </style>
</head>
<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center dore">Ajouter un employé</h1>
    <form method="post" class="w-50 mx-auto mt-4">
        <div class="mb-3">
            <label class="form-label dore">Nom d'utilisateur :</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label dore">Email :</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label dore">Mot de passe :</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label dore">Rôle :</label>
            <select name="role" class="form-select">
                <option value="standard">Standard</option>
                <option value="encadrant">Encadrant</option>
                <option value="manager">Manager</option>
                <option value="directeur">Directeur</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="listEmp.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
