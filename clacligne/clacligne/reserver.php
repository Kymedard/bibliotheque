<?php
session_start();
include './config.php';
include './header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "Vous devez être connecté pour réserver un livre.";
    header("Location: ./login.php");
    exit;
}

session_start();
include './config.php';
include './header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "Vous devez être connecté pour réserver un livre.";
    header("Location: ./login.php");
    exit;
}

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? null;
    $utilisateur_id = $_SESSION['utilisateurs_id'];
    $date_reservation = date('Y-m-d');
    $date_retrait = $_POST['date_retrait'] ?? null;

    if ($titre && $date_retrait) {
        // Vérifier si l'utilisateur existe bien dans `utilisateurs`
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = ?");
        $stmt->execute([$utilisateur_id]);
        if (!$stmt->fetch()) {
            $_SESSION['message'] = "Utilisateur non trouvé.";
            header("Location: espace_membre.php");
            exit;
        }

        // Insérer l'emprunt
        $stmt = $pdo->prepare("INSERT INTO emprunts (titre, utilisateurs_id, date_emprunt, statut) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$titre, $utilisateur_id, $date_reservation, 'en attente'])) {
            $_SESSION['message'] = "Réservation effectuée avec succès !";
            header("Location: espace_membre.php");
            exit;
        } else {
            $_SESSION['message'] = "Erreur lors de la réservation.";
        }
    } else {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
    }
}


// Récupérer les informations du livre
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
    $stmt->execute([$id]);
    $livre = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$livre) {
        $_SESSION['message'] = "Livre introuvable.";
        header("Location: admin.php");
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}

// Affichage du formulaire
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un livre</title>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Réserver un livre</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="reserver.php" method="POST" class="shadow p-4 bg-light rounded">
        <input type="hidden" name="id_livre" value="<?= $livre['id']; ?>">
        
        <div class="mb-3">
            <label for="titre" class="form-label">Titre du Livre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($livre['titre']); ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="date_retour" class="form-label">Date de retour prévue</label>
            <input type="date" class="form-control" id="date_retour" name="date_retour" min="<?= date('Y-m-d'); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Réserver</button>
        <a href="espace_membre.php" class="btn btn-secondary">Retour</a>
    </form>
</div>

<?php include './footer.php'; ?>

</body>
</html>
