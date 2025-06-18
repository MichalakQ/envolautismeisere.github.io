<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

// Vérifie les rôles autorisés
if (!hasRole("directeur") && !hasRole("chef") && !hasRole("admin")) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /transgourmet/index.php?message=$encodedMessage");
    exit;
}

// Validation de l'ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: listRef.php");
    exit;
}

$conn = openDatabaseConnection();

$errors = [];
$referenciel = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Récupération et nettoyage des données ---
    $nom_fournisseur     = htmlspecialchars(trim($_POST['nom_fournisseur'] ?? ''));
    $marque              = htmlspecialchars(trim($_POST['marque'] ?? ''));
    $nom_gamme           = htmlspecialchars(trim($_POST['nom_gamme'] ?? ''));
    $code_article        = htmlspecialchars(trim($_POST['code_article'] ?? ''));
    $designation_article = htmlspecialchars(trim($_POST['designation_article'] ?? ''));
    $uf                  = htmlspecialchars(trim($_POST['uf'] ?? ''));
    $qtes                = isset($_POST['qtes']) ? (int)$_POST['qtes'] : null;
    $prix_moyen          = isset($_POST['prix_moyen']) ? floatval($_POST['prix_moyen']) : null;
    $ca                  = isset($_POST['ca']) ? floatval($_POST['ca']) : null;
    $nb_lignes           = isset($_POST['nb_lignes']) ? (int)$_POST['nb_lignes'] : null;
    $favori              = isset($_POST['favori']) ? (int)$_POST['favori'] : 0;

    // --- Validation ---
    if ($nom_fournisseur === '') $errors[] = "Veuillez insérer le nom du fournisseur.";
    if ($marque === '') $errors[] = "Veuillez préciser la marque du produit.";
    if ($nom_gamme === '') $errors[] = "Précisez la gamme du produit.";
    if ($code_article === '') $errors[] = "Entrez le numéro de l'article.";
    if ($designation_article === '') $errors[] = "Entrez la désignation de l'article.";
    if ($uf === '') $errors[] = "Entrez une unité de facturation.";
    if (!is_numeric($qtes) || $qtes < 0) $errors[] = "Quantité invalide.";
    if (!is_numeric($prix_moyen) || $prix_moyen < 0) $errors[] = "Prix moyen invalide.";
    if (!is_numeric($ca) || $ca < 0) $errors[] = "Chiffre d'affaire invalide.";
    if (!is_numeric($nb_lignes) || $nb_lignes < 0) $errors[] = "Nombre de lignes invalide.";

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE referenciel
            SET nom_fournisseur = ?, marque = ?, nom_gamme = ?, code_article = ?, designation_article = ?, uf = ?, qtes = ?, prix_moyen = ?, ca = ?, nb_lignes = ?, favori = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $nom_fournisseur, $marque, $nom_gamme, $code_article, $designation_article,
            $uf, $qtes, $prix_moyen, $ca, $nb_lignes, $favori, $id
        ]);

        header("Location: listRef.php?success=1");
        exit;
    }

    // En cas d'erreur, conserver les données postées
    $referenciel = $_POST;
} else {
    // --- Récupération des données existantes ---
    $stmt = $conn->prepare("SELECT * FROM referenciel WHERE id = ?");
    $stmt->execute([$id]);
    $referenciel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$referenciel) {
        header("Location: listRef.php");
        exit;
    }
}

closeDatabaseConnection($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier un article</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>

<div class="container mt-5">
  <h1 class="text-center text-primary">Modifier un article</h1>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" class="w-75 mx-auto">
    <?php
    $fields = [
      'nom_fournisseur' => 'Nom du fournisseur',
      'marque' => 'Marque',
      'nom_gamme' => 'Nom de la gamme',
      'code_article' => 'Code article',
      'designation_article' => 'Désignation',
      'uf' => 'Unité de facturation (UF)',
      'qtes' => 'Quantité',
      'prix_moyen' => 'Prix moyen',
      'ca' => 'Chiffre d\'affaire',
      'nb_lignes' => 'Nombre de lignes'
    ];
    foreach ($fields as $name => $label):
    ?>
      <div class="mb-3">
        <label class="form-label"><?= $label ?> :</label>
        <input type="<?= is_numeric($referenciel[$name] ?? '') ? 'number' : 'text' ?>"
               name="<?= $name ?>" class="form-control"
               value="<?= htmlspecialchars($referenciel[$name] ?? '') ?>"
               <?= in_array($name, ['prix_moyen', 'ca']) ? 'step="0.01"' : '' ?> required>
      </div>
    <?php endforeach; ?>

    <div class="text-center">
      <button type="submit" class="btn btn-success">Enregistrer</button>
      <a href="listRef.php" class="btn btn-secondary">Annuler</a>
    </div>
  </form>
</div>

</body>
</html>
