<?php
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
    $_SESSION['message'] = "Accès refusé.";
    header("Location: ./login.php");
    exit;
}

include './config.php';
include './header.php';

// Gestion de l'ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);

    if ($nom && $email && $password && $role) {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, role, date_inscription) VALUES (?, ?, ?, ?, NOW())");
        if ($stmt->execute([$nom, $email, $password, $role])) {
            $_SESSION['message'] = "Utilisateur ajouté avec succès !";
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout de l'utilisateur.";
        }
    } else {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
    }
}

// Gestion de la suppression d'utilisateur
if (isset($_GET['supprimer_id'])) {
    $id = (int)$_GET['supprimer_id'];
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['message'] = "Utilisateur supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression.";
    }
    header("Location: gestion_utilisateurs.php");
    exit;
}

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT * FROM utilisateurs");
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2 class="mb-4">Gestion des utilisateurs</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire d'ajout d'utilisateur -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Ajouter un utilisateur</div>
        <div class="card-body">
            <form action="gestion_utilisateurs.php" method="POST">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Rôle</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="utilisateur">Utilisateur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                <button type="submit" name="ajouter" class="btn btn-success">Ajouter</button>
            </form>
        </div>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-header bg-info text-white">Liste des utilisateurs</div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?= $utilisateur['id'] ?></td>
                            <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                            <td><span class="badge bg-<?= $utilisateur['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                <?= htmlspecialchars($utilisateur['role']) ?></span></td>
                            <td><?= $utilisateur['date_inscription'] ?></td>
                            <td>
                                <a href="modifier_utilisateurs.php?id=<?= $utilisateur['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="gestion_utilisateurs.php?supprimer_id=<?= $utilisateur['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="admin.php" class="btn btn-secondary mt-3">Retour au tableau de bord</a>
</div>

<?php include './footer.php'; ?>
