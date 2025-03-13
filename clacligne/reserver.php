<?php
session_start();
include './config.php';

// Vérifier si un ID de livre valide est fourni
$livre_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$livre_id) {
    $_SESSION['message'] = "La réservation a échoué : ID du livre invalide.";
    header("Location: ./catalogue.php");
    exit;
}


// Récupérer les informations du livre
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$livre_id]);
$livre = $stmt->fetch();

if (!$livre) {
    $_SESSION['message'] = "Livre non trouvé.";
    header("Location: ./catalogue.php");
    exit;
}

// Vérifier si le livre est déjà emprunté ou réservé
$stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_livre = ? AND (statut = 'en attente' OR statut = 'emprunté')");
$stmt->execute([$livre_id]);
$emprunts_existants = $stmt->fetchColumn();

if ($emprunts_existants > 0) {
    $_SESSION['message'] = "Ce livre est déjà emprunté ou réservé.";
    header("Location: ./catalogue.php");
    exit;
}

include './header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un livre</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous d'avoir un fichier CSS -->
</head>
<body>

<div class="container mt-5">
    <h2>Réserver un livre</h2>  

    <!-- Affichage des messages d'alerte -->
    <?php if (isset($_SESSION['message'])) : ?>
        <div class="alert alert-warning"><?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Détails du livre</h5>
            <p><strong>Titre :</strong> <?= htmlspecialchars($livre['titre']) ?></p>
            <p><strong>Auteur :</strong> <?= htmlspecialchars($livre['auteur']) ?></p>
        </div>
        <form method="POST" action="emprunt_action.php">
                <input type="hidden" name="livre_id" value="<?= $livre_id ?>">
                <div class="form-group mb-3">
                    <label for="date_retour">Date de retour souhaitée :</label>
                    <input type="date" class="form-control" id="date_retour" name="date_retour" 
                        min="<?= date('Y-m-d') ?>" 
                        max="<?= date('Y-m-d', strtotime('+1 month')) ?>" 
                        required>
                </div>

                <button type="submit" class="btn btn-primary">Confirmer la réservation</button>
                <a href="catalogue.php" class="btn btn-secondary">Annuler</a>
        </form>

</div>

<?php include './footer.php'; ?>

</body>
</html>
