<?php
require_once '../config/db_connect.php';
require_once '../auth/authFunctions.php';

if (!hasRole("manager")) {
    $encodedMessage = urlencode("ERREUR : Vous n'avez pas les bonnes permissions.");
    header("Location: /transgourmet/index.php?message=$encodedMessage");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_fournisseur  = trim($_POST['nom_fournisseur']);
    $marque    = trim($_POST['marque']);
    $nom_gamme      = trim($_POST['nom_gamme']) ;
    $code_article  = $_POST['code_article'];
    $designation_article=$POST['designation_article'];
    $uf=$POST['uf'];
    $qtes=$POST['qtes'];
    $prix_moyen=$POST['prix_moyen'];
    $ca=$POST['ca'];
    $nb_lignes=$POST['nb_lignes'];
    $favori=$POST['favori']

    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("INSERT INTO referenciel (nom_fournisseur,marque,nom_gamme,code_article,designation_article,uf,qtes,prix_moyen,ca,nb_lignes,favori) VALUES (?, ?,?, ?,?,?,?,?,?,?,0)");
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
    <title>Ajouter une référence</title>
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
    <h1 class="text-center dore">Ajouter une référence</h1>
    <form method="post" class="w-75 mx-auto mt-4">
        <?php
        $fields = [
            "nom_fournisseur" => "Nom du fournisseur",
            "marque" => "Marque",
            "nom_gamme" => "Nom de la gamme",
            "code_article" => "Code article",
            "designation_article" => "Désignation",
            "uf" => "Unité de facturation",
            "qtes" => "Quantité",
            "prix_moyen" => "Prix moyen",
            "ca" => "Chiffre d'affaires",
            "nb_lignes" => "Nombre de lignes"
        ];
        foreach ($fields as $name => $label) {
            echo '<div class="mb-3">
                    <label class="form-label dore">' . $label . '</label>
                    <input type="' . (in_array($name, ['qtes', 'code_article', 'nb_lignes', 'prix_moyen', 'ca']) ? 'number' : 'text') . '" step="any" name="' . $name . '" class="form-control" required>
                  </div>';
        }
        ?>
        <div class="mb-3">
            <label class="form-label dore">Favori</label><br>
            <input type="checkbox" name="favori" value="1"> Oui
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="listRef.php" class="btn btn-secondary ms-2">Annuler</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
