<?php
session_start();
include './config.php';

// Récupérer l'ID de l'utilisateur s'il est connecté
$utilisateurs_id = $_SESSION['utilisateurs_id'] ?? null;
$role = $_SESSION['role'] ?? null; // Vérification du rôle

// Vérifier si un ID de livre est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Livre non trouvé.";
    header('Location: catalogue.php');
    exit;
}

$livres_id = $_GET['id'];

// Récupérer les détails du livre
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$livres_id]);
$livre = $stmt->fetch();

if (!$livre) {
    $_SESSION['message'] = "Livre non trouvé.";
    header('Location: catalogue.php');
    exit;
}

// Vérifier si ce livre est déjà en favori
$is_fav = false;
if ($utilisateurs_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
    $stmt->execute([$utilisateurs_id, $livres_id]);
    $is_fav = $stmt->fetchColumn() > 0;
}

include './header.php';
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
            <p><strong>Maison d'édition :</strong> <?= htmlspecialchars($livre['maison']) ?></p>
            <p><strong>Catégorie :</strong> <?= htmlspecialchars($livre['categorie']) ?></p>
            <p><strong>Date de publication :</strong> <?= htmlspecialchars($livre['annee_publication']) ?></p>
            <p><strong>Description :</strong></p>
            <p><?= nl2br(htmlspecialchars($livre['description'])) ?></p>

            <!-- Boutons d'action -->
            <div class="d-flex gap-2">
                <?php if ($role === 'admin'): ?>
                    <!-- Affichage uniquement pour l'admin -->
                    <a href="catalogue.php" class="btn btn-secondary">Retour au catalogue</a>
                <?php else: ?>
                    <!-- Affichage pour les utilisateurs non admin -->
                    <?php if ($utilisateurs_id): ?>
                        <a href="reserver.php?id=<?= $livre['id'] ?>" class="btn btn-primary">Réserver ce livre</a>

                        <!-- Bouton favori -->
                        <form action="favoris_action.php" method="post" class="d-inline">
                            <input type="hidden" name="livres_id" value="<?= $livre['id'] ?>">
                            <button type="submit" class="btn <?= $is_fav ? 'btn-danger' : 'btn-outline-danger' ?>">
                                <?= $is_fav ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary">Connectez-vous pour réserver</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include './footer.php'; ?>
</body>
</html>
