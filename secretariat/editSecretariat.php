<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';
if (!hasRole("manager")) {
 $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
 header("Location: /transgourmet/index.php?message=$encodedMessage");
 exit;
 }
// Vérification de l'ID transmis
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: listEncadrant.php");
    exit;
}

$conn = openDatabaseConnection();

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = trim($_POST['reference']);
    $designation = trim($_POST['designation']);
    $nom = trim($_POST['nom']); // ← Correction ici
    $stock = intval($_POST['stock']);
    $reste = intval($_POST['reste']);

    $errors = [];

    if (empty($reference)) {
        $errors[] = "La référence est obligatoire.";
    }

    if (empty($designation)) {
        $errors[] = "Veuillez nommer le produit.";
    }

    if ($stock <= 0) {
        $errors[] = "Le stock de base doit être strictement positif.";
    }

    if ($reste < 0) {
        $errors[] = "Les quantités restantes doivent être positives.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE lieu SET nom = ?, reference = ?, designation = ?, stock = ?, reste = ? WHERE id = ?");
        $stmt->execute([$nom, $reference, $designation, $stock, $reste, $id]); // ← Ajout de $nom ici

        header("Location: listEncadrant.php?success=1");
        exit;
    }
} else {
    // Récupération des données existantes
    $stmt = $conn->prepare("SELECT * FROM lieu WHERE id = ?");
    $stmt->execute([$id]);
    $lieu = $stmt->fetch(PDO::FETCH_ASSOC); // ← Renommé de $violette à $lieu

    if (!$lieu) {
        header("Location: listEncadrant.php");
        exit;
    }
}
closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Modifier un produit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url('../assets/EditClients.jpg');
      background-repeat: no-repeat;
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
  <h1 class="text-center red">Modifier un produit</h1>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="w-50 mx-auto">
    <div class="mb-3">
      <label for="reference" class="form-label red">Résidence :</label>
      <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($lieu['nom'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label for="reference" class="form-label red">Référence :</label>
      <input type="number" class="form-control" id="reference" name="reference" value="<?= htmlspecialchars($lieu['reference'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label for="designation" class="form-label red">Désignation :</label>
      <input type="text" class="form-control" id="designation" name="designation" value="<?= htmlspecialchars($lieu['designation'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label for="stock" class="form-label red">Stock de base :</label>
      <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?= htmlspecialchars($lieu['stock'] ?? 0) ?>" required>
    </div>

    <div class="mb-3">
      <label for="reste" class="form-label red">Quantité restante :</label>
      <input type="number" class="form-control" id="reste" name="reste" min="0" value="<?= htmlspecialchars($lieu['reste'] ?? 0) ?>" required>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-success">Enregistrer</button>
     <a href="listEncadrant.php" class="btn btn-secondary">Annuler</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
