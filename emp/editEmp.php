<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

if (!(hasRole("directeur") || hasRole("chef") || hasRole("admin"))) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /transgourmet/index.php?message=$encodedMessage");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: listEmp.php");
    exit;
}

$conn = openDatabaseConnection();

$errors = [];
$employe = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est obligatoire.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }

    $validRoles = ['admin', 'directeur', 'manager', 'chef', 'encadrant'];
    if (!in_array($role, $validRoles)) {
        $errors[] = "Rôle invalide.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE employes SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $role, $id]);

        header("Location: listEmp.php?success=1");
        exit;
    } else {
        // Pour conserver les valeurs en cas d'erreur
        $employe = ['username' => $username, 'email' => $email, 'role' => $role];
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM employes WHERE id = ?");
    $stmt->execute([$id]);
    $employe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employe) {
        header("Location: listEmp.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Modifier un employé</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url('../assets/admin.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    .red {
      color: rgb(255, 0, 0);
      text-shadow: 0 0 5px rgb(0, 17, 255), 0 0 10px rgb(0, 238, 255);
    }
  </style>
</head>
<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

<div class="container mt-5">
  <h1 class="text-center red">Modifier un employé</h1>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="w-50 mx-auto">
    <div class="mb-3">
      <label class="form-label red">Nom d'utilisateur :</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($employe['username'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label red">Email :</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employe['email'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label red">Rôle :</label>
      <select name="role" class="form-select" required>
        <?php
        foreach (['admin', 'directeur', 'manager', 'chef', 'encadrant'] as $r) {
            $selected = ($employe['role'] ?? '') === $r ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($r) . "\" $selected>" . htmlspecialchars($r) . "</option>";
        }
        ?>
      </select>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-success">Enregistrer</button>
      <a href="listEmp.php" class="btn btn-secondary">Annuler</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php closeDatabaseConnection($conn); ?>
