<?php
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
    $_SESSION['message'] = "Accès refusé.";
    header("Location: ./login.php");
    exit;
}

include './config.php';
include './header.php';

// Gestion de l'ajout de livre
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $auteur = htmlspecialchars($_POST['auteur']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $maison = htmlspecialchars($_POST['maison']);
    $description = htmlspecialchars($_POST['description']);
    $annee_publication = htmlspecialchars($_POST['annee_publication']);

    // Gestion de l'upload de la couverture
    $couverture = null;
    if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = './uploads/';
        $fileName = basename($_FILES['couverture']['name']);
        $filePath = $uploadDir . $fileName;

        // Vérifier et déplacer le fichier
        if (move_uploaded_file($_FILES['couverture']['tmp_name'], $filePath)) {
            $couverture = $fileName;
        } else {
            $_SESSION['message'] = "Échec du téléchargement de la couverture.";
        }
    }

    // Insertion dans la base de données
    if ($titre && $auteur && $categorie && $annee_publication && $couverture) {
        $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, categorie, description, maison, annee_publication, couverture) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$titre, $auteur, $categorie, $description, $maison, $annee_publication, $couverture])) {
            $_SESSION['message'] = "Livre ajouté avec succès !";
            header("Location: admin.php");
            exit;
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout du livre.";
        }
    } else {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
    }
}

?>

<div class="container mt-5">
    <h2 class="mb-4">Ajouter un nouveau livre</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="ajouter_livre.php" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre du livre</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>

        <div class="mb-3">
            <label for="auteur" class="form-label">Auteur</label>
            <input type="text" class="form-control" id="auteur" name="auteur" required>
        </div>

        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie" required>
        </div>

        <div class="mb-3">
            <label for="maison" class="form-label">Maison d'edition</label>
            <input type="text" class="form-control" id="maison" name="maison" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description du livre</label>
            <input type="text" class="form-control" id="description" name="description" required>
        </div>

        <div class="mb-3">
            <label for="annee" class="form-label">Année de publication</label>
            <input type="number" class="form-control" id="annee_publication" name="annee_publication" min="1800" max="<?php echo date('Y'); ?>" required>
        </div>

        <div class="mb-3">
            <label for="couverture" class="form-label">Couverture</label>
            <input type="file" class="form-control" id="couverture" name="couverture" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter le livre</button>
        <a href="admin.php" class="btn btn-secondary">Retour au tableau de bord</a>
    </form>
</div>
