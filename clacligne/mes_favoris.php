<?php
session_start();
include './config.php';

$utilisateurs_id = $_SESSION['utilisateurs_id'];

// Récupérer les livres favoris de l'utilisateur
$stmt = $pdo->prepare("SELECT livres.id, livres.titre FROM favoris JOIN livres ON favoris.livres_id = livres.id WHERE favoris.utilisateurs_id = ?");
$stmt->execute([$utilisateurs_id]);
$favoris = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retirer un livre des favoris ou le réserver
if (isset($_GET['action'], $_GET['livre_id'])) {
    $livre_id = $_GET['livre_id'];

    if ($_GET['action'] == 'retirer') {
        // Retirer du favoris
        $stmt = $pdo->prepare("DELETE FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
        $stmt->execute([$utilisateurs_id, $livre_id]);
    } elseif ($_GET['action'] == 'reserver') {
        // Logique pour réserver le livre (à définir selon vos besoins)
        // Ici on affiche un message pour indiquer que le livre est réservé
        echo "<script>alert('Le livre a été réservé avec succès !');</script>";
    }

    header("Location: favoris.php"); // Rediriger après l'action
    exit();
}

include './header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes livres favoris</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Mes livres favoris</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">Titre du livre</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($favoris as $favori): ?>
            <tr>
                <td><?= htmlspecialchars($favori['titre']) ?></td>
                <td>
                    <a href="retirer.php?action=retirer&livre_id=<?= $favori['id'] ?>" class="btn btn-danger btn-sm">
                        Retirer des favoris
                    </a>
                    <a href="reserver.php?action=reserver&livre_id=<?= $favori['id'] ?>" class="btn btn-warning btn-sm">
                        Réserver
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
    </div>
</div>
</body>
</html>

<?php include './footer.php'; ?>
