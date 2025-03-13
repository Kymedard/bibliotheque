<?php
session_start();
include './config.php';
include './header.php';

// // Vérifier si l'utilisateur est administrateur
// if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
//     $_SESSION['message'] = "Accès refusé.";
//     header("Location: ./login.php");
//     exit;
// }

// Récupérer les informations de l'utilissateur
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $utilisateurs = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$utilisateurs) {
        $_SESSION['message'] = "Utilisateur introuvable.";
        header("Location: admin.php");
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}

// Mettre à jour les informations du livre
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    // Mise à jour des informations dans la base de données
    $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, email = ?, role = ?, WHERE id = ?");
    if ($stmt->execute([$nom, $email, $role, $id])) {
        $_SESSION['message'] = "Utilisateur mis à jour avec succès !";
        header("Location: gestion_utilisateurs.php");
        exit;
    } else {
        $_SESSION['message'] = "Erreur lors de la mise à jour de utilisateurs.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un utilisateur</title>
    
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Modifier l'utilisateur</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="modifier_utilisateurs.php?id=<?= $utilisateurs['id']; ?>" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= $utilisateurs['nom']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= $utilisateurs['email']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="mots_de_passe" class="form-label">Mots de passe</label>
            <input type="password" class="form-control" readonly>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-control" id="role" name="annee_publication" value="<?= $livre['annee_publication']; ?>" required>
                <option value="<?= $utilisateurs['role']; ?>" default>Utilisateur</option>
                <option value="<?= $utilisateurs['role']; ?>" default>Administrateur</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="gestion_utilisateurs.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
<?php include './footer.php'; ?>
</body>
</html>
