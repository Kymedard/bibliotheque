<?php
include './config.php';
include './header.php';

// Vérifier si un ID de livre est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='container mt-5'><p class='text-danger'>Livre non trouvé.</p></div>";
    include './footer.php';
    exit;
}

// Récupérer les détails du livre
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$id]);
$livre = $stmt->fetch();

if (!$livre) {
    echo "<div class='container mt-5'><p class='text-danger'>Livre non trouvé.</p></div>";
    include './footer.php';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <!-- Image du livre -->
        <div class="col-md-4">
            <img src="./uploads/<?= htmlspecialchars($livre['couverture']) ?>" class="img-fluid" alt="<?= htmlspecialchars($livre['titre']) ?>" style="border: 5px solid red" >
        </div>
        
        <!-- Détails du livre -->
        <div class="col-md-8">
            <h1><?= htmlspecialchars($livre['titre']) ?></h1>
            <h4>Auteur : <?= htmlspecialchars($livre['auteur']) ?></h4>
            <p><strong>Maison d'edition :</strong> <?= htmlspecialchars($livre['maison']) ?></p>
            <p><strong>Catégorie :</strong> <?= htmlspecialchars($livre['categorie']) ?></p>
            <p><strong>Date de publication :</strong> <?= htmlspecialchars($livre['annee_publication']) ?></p>
            <p><strong>Description :</strong></p>
            <p><?= nl2br(htmlspecialchars($livre['description'])) ?></p>

            <!-- Bouton d'emprunt (à condition que l'utilisateur soit connecté) -->
            <a href="reserver.php?id=<?= $livre['id'] ?>" class="btn btn-primary">Réserver ce livre</a>
            <?php
                    session_start();
                    include './config.php';

                    $livres_id = $_GET['id'];
                    $utilisateurs_id = $_SESSION['utilisateurs_id'] ?? null;

                    // Vérifier si ce livre est déjà en favori
                    $is_fav = false;
                    if ($utilisateurs_id) {
                        $stmt = $pdo->prepare("SELECT * FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
                        $stmt->execute([$utilisateurs_id, $livres_id]);
                        $is_fav = $stmt->rowCount() > 0;
                    }

                    ?>

                    <a href="favoris_action.php?id=<?= $livres_id ?>&action=<?= $is_fav ? 'remove' : 'add' ?>" class="btn <?= $is_fav ? 'btn-danger' : 'btn-success' ?>">
                        <?= $is_fav ? "Retirer des favoris" : "Ajouter aux favoris" ?>
                    </a>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>
</body>
</html>
