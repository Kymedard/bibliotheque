
<?php

session_start();
if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "Veuillez vous connecter.";
    header("Location: login.php");
    exit;
}
$utilisateurs_id = $_SESSION['utilisateurs_id'];

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include './config.php';

if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "Voici votre espace personnel.";
    header("Location: login.php");
    exit;
}

$utilisateurs_id = $_SESSION['utilisateurs_id'];
include './header.php';

// Vérifier si l'utilisateur est bien un utilisateur
if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'utilisateur') {
    $_SESSION['message'] = ".";
    header("Location: ./login.php");
    exit;
}


// Vérifier si l'utilisateur est connect

if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "voici votre espace personnel.";
    header("Location: login.php");
    exit;
}

$utilisateurs_id = $_SESSION['utilisateurs_id'];
// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$utilisateurs_id]);
$utilisateurs = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$utilisateurs) {
    $_SESSION['message'] = "Utilisateur introuvable.";
    header("Location: login.php");
    exit;
}

// Récupérer les emprunts de l'utilisateur
$stmt = $pdo->prepare("
    SELECT emprunts.id, livres.titre, emprunts.date_emprunt, emprunts.statut
    FROM emprunts
    INNER JOIN livres ON emprunts.id_livre = livres.id
    WHERE emprunts.utilisateurs_id = ?
    ORDER BY emprunts.date_emprunt DESC
");
$stmt->execute([$utilisateurs_id]);
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Bienvenue, <?= htmlspecialchars($utilisateurs['nom']); ?> !</h2>
    <h2>Tableau de bord Utilisateur</h2>
    
    
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <table class="table">
    <h3 class="mt-4">Mes Emprunts</h3>
        <thead>
            <tr>
                <th>Livre</th>
                <th>Date d'emprunt</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $emprunt): ?>
            <tr>
                <td><?= htmlspecialchars($emprunt['titre']) ?></td>
                <td><?= $emprunt['date_emprunt'] ?></td>
                <td><?= $emprunt['statut'] ?></td>
                <td>
                    <?php if ($emprunt['statut'] == 'En cours'): ?>
                        <a href="emprunter_livre.php?id=<?= $emprunt['id'] ?>&action=remove" class="btn btn-danger btn-sm">Annuler</a>
                    <?php else: ?>
                        <span class="text-muted">Terminé</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3 class="mt-4">Mes livres favoris</h3>
        <ul>
            <?php
            $stmt = $pdo->prepare("SELECT livres.id, livres.titre FROM favoris JOIN livres ON favoris.livres_id = livres.id WHERE favoris.utilisateurs_id = ?");
            $stmt->execute([$utilisateurs_id]);
            while ($livre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li><a href='livre.php?id={$livre['id']}'>{$livre['titre']}</a></li>";
            }
            ?>
        </ul>
    <a href="logout.php" class="btn btn-primary mt-3">Déconnexion</a>
    
</div>
<?php include './footer.php'; ?>
</body>
    
</html>

