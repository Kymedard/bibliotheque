<?php
session_start();
include './config.php';
include './header.php';

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
    $_SESSION['message'] = "Accès refusé.";
    header("Location: .login.php");
    exit;
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

// Mettre à jour les informations du livre
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
            if ($livre['couverture'] && file_exists($uploadDir . $livre['couverture'])) {
                unlink($uploadDir . $livre['couverture']);
            }
            $couverture = $fileName;
        } else {
            $_SESSION['message'] = "Échec du téléchargement de la nouvelle couverture.";
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifications</title>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Modifier le livre</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="modifier_livre.php?id=<?= $livre['id']; ?>" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" id="titre" name="titre" value="<?= $livre['titre']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="auteur" class="form-label">Auteur</label>
            <input type="text" class="form-control" id="auteur" name="auteur" value="<?= $livre['auteur']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie</label>
            <input type="text" class="form-control" id="categorie" name="categorie" value="<?= $livre['categorie']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="maison" class="form-label">Maison d'edition</label>
            <input type="text" class="form-control" id="maison" name="maison" value="<?= $livre['maison']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description du livre</label>
            <input type="text" class="form-control" id="description" name="description" value="<?= $livre['description']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="annee_publication" class="form-label">Année de publication</label>
            <input type="number" class="form-control" id="annee_publication" name="annee_publication" value="<?= $livre['annee_publication']; ?>" min="1000" max="<?= date('Y'); ?>" required>
        </div>

        <div class="mb-3">
            <label for="couverture" class="form-label">Couverture actuelle</label><br>
            <img src="./uploads/<?= $livre['couverture']; ?>" alt="Couverture actuelle" class="img-thumbnail mb-3" style="max-width: 200px;">
            <input type="file" class="form-control" id="couverture" name="couverture" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="admin.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php include './footer.php'; ?>
</body>
</html>
