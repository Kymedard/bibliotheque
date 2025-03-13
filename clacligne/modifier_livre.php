<?php
session_start();
include './config.php';

// // Vérifier si l'utilisateur est administrateur
// if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
//     $_SESSION['message'] = "Accès refusé.";
//     header("Location: login.php");
//     exit;
// }

// Vérifier si un ID de livre est fourni
if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Aucun livre sélectionné.";
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'];

// Récupérer les informations du livre
$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$id]);
$livre = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livre) {
    $_SESSION['message'] = "Livre introuvable.";
    header("Location: admin.php");
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $auteur = htmlspecialchars($_POST['auteur']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $maison = htmlspecialchars($_POST['maison']);
    $description = htmlspecialchars($_POST['description']);
    $annee_publication = htmlspecialchars($_POST['annee_publication']);

    // Gestion de l'upload de la nouvelle couverture
    $couverture = $livre['couverture'];
    if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = './uploads/';
        $fileName = basename($_FILES['couverture']['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['couverture']['tmp_name'], $filePath)) {
            // Supprimer l'ancienne couverture si différente
            if ($livre['couverture'] && $livre['couverture'] !== $fileName && file_exists($uploadDir . $livre['couverture'])) {
                unlink($uploadDir . $livre['couverture']);
            }
            $couverture = $fileName;
        } else {
            $_SESSION['message'] = "Échec du téléchargement de la nouvelle couverture.";
            header("Location: modifier_livre.php?id=" . $id);
            exit;
        }
    }

    // Mise à jour des informations dans la base de données
    $stmt = $pdo->prepare("UPDATE livres SET titre = ?, auteur = ?, categorie = ?, maison = ?, description = ?, annee_publication = ?, couverture = ? WHERE id = ?");
    if ($stmt->execute([$titre, $auteur, $categorie, $maison, $description, $annee_publication, $couverture, $id])) {
        $_SESSION['message'] = "Livre mis à jour avec succès !";
        header("Location: admin.php");
        exit;
    } else {
        $_SESSION['message'] = "Erreur lors de la mise à jour du livre.";
        header("Location: modifier_livre.php?id=" . $id);
        exit;
    }
}

// Inclure le header après toutes les redirections possibles
include './header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Modifier le livre</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="modifier_livre.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($livre['titre']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="auteur" class="form-label">Auteur</label>
            <input type="text" class="form-control" id="auteur" name="auteur" value="<?= htmlspecialchars($livre['auteur']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <select class="form-control" id="categorie" name="categorie" required>
                <option value="Litérature Fiction" <?= $livre['categorie'] == "Litérature Fiction" ? 'selected' : '' ?>>Litérature Fiction</option>
                <option value="Litérature Scientifique" <?= $livre['categorie'] == "Litérature Scientifique" ? 'selected' : '' ?>>Litérature Scientifique</option>
                <option value="Litérature Histoire" <?= $livre['categorie'] == "Litérature Histoire" ? 'selected' : '' ?>>Litérature Histoire</option>
                <option value="Litérature Poème" <?= $livre['categorie'] == "Litérature Poème" ? 'selected' : '' ?>>Litérature Poème</option>
                <option value="Autre" <?= $livre['categorie'] == "Autre" ? 'selected' : '' ?>>Autre</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="maison" class="form-label">Maison d'édition</label>
            <input type="text" class="form-control" id="maison" name="maison" value="<?= htmlspecialchars($livre['maison']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="annee_publication" class="form-label">Année de publication</label>
            <input type="number" class="form-control" id="annee_publication" name="annee_publication" 
                   value="<?= htmlspecialchars($livre['annee_publication']) ?>" 
                   min="1800" max="<?= date('Y') ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($livre['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="couverture" class="form-label">Couverture</label>
            <?php if ($livre['couverture']): ?>
                <div class="mb-2">
                    <img src="./uploads/<?= htmlspecialchars($livre['couverture']) ?>" alt="Couverture actuelle" style="max-width: 200px;">
                </div>
            <?php endif; ?>
            <input type="file" class="form-control" id="couverture" name="couverture" accept="image/*">
            <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="admin.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include './footer.php'; ?>
