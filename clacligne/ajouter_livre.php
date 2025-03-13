<?php
session_start();
include './config.php';

// // Vérifier si l'utilisateur est un administrateur
// if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
//     $_SESSION['message'] = "Accès refusé.";
//     header("Location: ./login.php");
//     exit;
// }

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = htmlspecialchars($_POST['titre']);
    $auteur = htmlspecialchars($_POST['auteur']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $maison = htmlspecialchars($_POST['maison']);
    $description = htmlspecialchars($_POST['description']);
    $annee_publication = htmlspecialchars($_POST['annee_publication']);

    // Validation des champs requis
    if (empty($titre) || empty($auteur) || empty($categorie) || empty($annee_publication)) {
        $_SESSION['message'] = "Tous les champs marqués * sont obligatoires.";
        header("Location: ajouter_livre.php");
        exit;
    }

    // Gestion de l'upload de la couverture
    $couverture = null;
    if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = './uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['couverture']['name']);
        $filePath = $uploadDir . $fileName;

        // Vérifier le type de fichier
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['couverture']['type'], $allowed)) {
            $_SESSION['message'] = "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.";
            header("Location: ajouter_livre.php");
            exit;
        }

        if (!move_uploaded_file($_FILES['couverture']['tmp_name'], $filePath)) {
            $_SESSION['message'] = "Échec du téléchargement de la couverture.";
            header("Location: ajouter_livre.php");
            exit;
        }
        $couverture = $fileName;
    } else {
        $_SESSION['message'] = "Une image de couverture est requise.";
        header("Location: ajouter_livre.php");
        exit;
    }

    // Insertion dans la base de données
    try {
        $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, categorie, description, maison, annee_publication, couverture) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$titre, $auteur, $categorie, $description, $maison, $annee_publication, $couverture])) {
            $_SESSION['message'] = "Livre ajouté avec succès !";
            header("Location: admin.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de l'ajout du livre : " . $e->getMessage();
        header("Location: ajouter_livre.php");
        exit;
    }
}

// Inclusion du header après toutes les redirections possibles
include './header.php';
?>

<div class="container mt-5">
    <h2 class="mb-4">Ajouter un nouveau livre</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="ajouter_livre.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="titre" class="form-label">Titre *</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>

        <div class="mb-3">
            <label for="auteur" class="form-label">Auteur *</label>
            <input type="text" class="form-control" id="auteur" name="auteur" required>
        </div>

        <div class="mb-3">
            <label for="categorie" class="form-label">Catégorie *</label>
            <select class="form-control" id="categorie" name="categorie" required>
                <option value="">Sélectionnez une catégorie</option>
                <optgroup label="Fiction">
                    <option value="Roman">Roman</option>
                    <option value="Nouvelle">Nouvelle</option>
                    <option value="Poésie">Poésie</option>
                    <option value="Théâtre">Théâtre</option>
                    <option value="Science-Fiction">Science-Fiction</option>
                    <option value="Fantastique">Fantastique</option>
                    <option value="Policier">Policier</option>
                    <option value="Aventure">Aventure</option>
                </optgroup>
                <optgroup label="Non-Fiction">
                    <option value="Histoire">Histoire</option>
                    <option value="Biographie">Biographie</option>
                    <option value="Essai">Essai</option>
                    <option value="Philosophie">Philosophie</option>
                    <option value="Sciences">Sciences</option>
                    <option value="Technologie">Technologie</option>
                    <option value="Art">Art</option>
                    <option value="Musique">Musique</option>
                </optgroup>
                <optgroup label="Éducation">
                    <option value="Manuel Scolaire">Manuel Scolaire</option>
                    <option value="Dictionnaire">Dictionnaire</option>
                    <option value="Encyclopédie">Encyclopédie</option>
                    <option value="Guide Pratique">Guide Pratique</option>
                </optgroup>
                <optgroup label="Jeunesse">
                    <option value="Album Jeunesse">Album Jeunesse</option>
                    <option value="Roman Jeunesse">Roman Jeunesse</option>
                    <option value="Conte">Conte</option>
                    <option value="Bande Dessinée">Bande Dessinée</option>
                </optgroup>
                <optgroup label="Autres">
                    <option value="Religion">Religion</option>
                    <option value="Cuisine">Cuisine</option>
                    <option value="Voyage">Voyage</option>
                    <option value="Sport">Sport</option>
                    <option value="Loisirs Créatifs">Loisirs Créatifs</option>
                    <option value="Autre">Autre</option>
                </optgroup>
            </select>
        </div>

        <div class="mb-3">
            <label for="maison" class="form-label">Maison d'édition</label>
            <input type="text" class="form-control" id="maison" name="maison">
        </div>

        <div class="mb-3">
            <label for="annee_publication" class="form-label">Année de publication *</label>
            <input type="number" class="form-control" id="annee_publication" name="annee_publication" 
                   min="1800" max="<?= date('Y') ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label for="couverture" class="form-label">Couverture *</label>
            <input type="file" class="form-control" id="couverture" name="couverture" accept="image/*" required>
            <small class="text-muted">Formats acceptés : JPG, PNG, GIF</small>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter le livre</button>
        <a href="admin.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include './footer.php'; ?>
