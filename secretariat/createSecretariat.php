<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';
if (!hasRole("manager")) {
 $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
 header("Location: /transgourmet/index.php?message=$encodedMessage");
 exit;
 }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$reference = $_POST['reference'];
$designation = $_POST['designation'];
$stock=$_POST['stock'];
$reste=$_POST['reste'];
$conn = openDatabaseConnection();
$stmt = $conn->prepare("INSERT INTO violette (reference, designation, stock, reste) VALUES (?, ?, ?, ?)");
$stmt->execute([$reference, $designation, $stock, $reste]);
closeDatabaseConnection($conn);
header("Location: listMaisonViolette.php");
exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
crossorigin="anonymous">
  <style>
                body {
            background-image: url('../assets/createClient.jpg'); /* chemin vers ton image */
            background-repeat: no-repeat;   /* Ne pas répéter l'image */
            background-size: cover;         /* L'image couvre tout l'écran */
            background-position: center;    /* Centre l'image */
            background-attachment: fixed;   /* L'image reste fixe lors du scroll */
        }
                .dore {
    color: #FFD700; /* Couleur dorée */
    /* Optionnel : ajouter un léger effet de brillance */
    text-shadow: 0 0 5px #FFD700, 0 0 10px #FFA500;
}
  </style>
<title>Ajouter un produit</title>

</head>
<body>
<?php include_once '../assets/gestionMessage.php'; ?>
<?php include '../assets/navbar.php'; ?>
<h1><p class="dore">Ajouter un produit</p></h1>
<form method="post">
<div>
<label><p class="dore">Résidence: </p></label>
<input type="text" name="nom" required>
</div>
<div>
<label><p class="dore">référence: </p></label>
<input type="number" name="reference" required>
</div>
<div>
<label><p class="dore">Désignation du produit:</p></label>
<input type="text" name="designation" required>
</div>
<div>
<label><p class="dore">stock de base:</p></label>
<input type="number" name="stock" required>
</div>
<div>
<label><p class="dore">Quantité restante :</p></label>
<input type="number" name="reste" required>
</div>
<button type="submit">Enregistrer</button>
</form>
<a href="../secretariat/listSecretariat.php"><p class="dore"><i class="fas fa-user"></i><br>Retour à la liste</p></a>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
