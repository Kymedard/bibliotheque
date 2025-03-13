<?php
session_start();
include './config.php'; // Doit être inclus avant d'utiliser $pdo

// Vérifier si l'utilisateur est bien un administrateur
if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
    $_SESSION['message'] = "Accès refusé.";
    header("Location: ./login.php");
    exit;
}

// Nombre total de livres
$stmt = $pdo->query("SELECT COUNT(*) AS total_livres FROM livres");
$total_livres = $stmt->fetch(PDO::FETCH_ASSOC)['total_livres'];

// Nombre total d'utilisateurs
$stmt = $pdo->query("SELECT COUNT(*) AS total_utilisateurs FROM utilisateurs");
$total_utilisateurs = $stmt->fetch(PDO::FETCH_ASSOC)['total_utilisateurs'];

// Nombre total d'emprunts en cours
$stmt = $pdo->query("SELECT COUNT(*) AS total_emprunts FROM emprunts WHERE statut = 'en cours'");
$total_emprunts = $stmt->fetch(PDO::FETCH_ASSOC)['total_emprunts'];

$utilisateurs_id = $_SESSION['utilisateurs_id'];
// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$utilisateurs_id]);
$utilisateurs = $stmt->fetch(PDO::FETCH_ASSOC);

include './header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
</head>
<body>
<div class="container mt-5">
<h2>Bienvenue, <?= htmlspecialchars($utilisateurs['nom']); ?> !</h2>
    <h2>Tableau de bord Administrateur</h2>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Livres</h5>
                    <p class="card-text"><?= $total_livres; ?></p>
                </div>
            </div>
            <?php
                echo '<div class="text-center mt-4">
                        <a href="ajouter_livre.php" class="btn btn-success">➕ Ajouter un nouveau livre</a>
                    </div>';
            ?>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Utilisateurs</h5>
                    <p class="card-text"><?= $total_utilisateurs; ?></p>
                </div>
            </div>
            <?php
                echo '<div class="text-center mt-4">
                        <a href="gestion_utilisateurs.php" class="btn btn-success">➕ Ajouter / Modifier / Suprimer des utilisateurs</a>
                    </div>';
            ?>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Emprunts en cours</h5>
                    <p class="card-text"><?= $total_emprunts; ?></p>
                </div>
            </div>
            <?php
                echo '<div class="text-center mt-4">
                        <a href="suivi_reservations.php" class="btn btn-success"> Suivi des réservations et emprunts</a>
                    </div>';
            ?>
        </div>
    </div>
</div>

<?php
// Récupérer tous les livres
$stmt = $pdo->query("SELECT * FROM livres");
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h3>Liste des Livres</h3>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($livres as $livre) : ?>
                <tr>
                    <td><?= $livre['id']; ?></td>
                    <td><?= htmlspecialchars($livre['titre']); ?></td>
                    <td><?= htmlspecialchars($livre['auteur']); ?></td>
                    <td><img src="./uploads/<?= htmlspecialchars($livre['couverture']) ?>" 
                        class="card-img-top" alt="<?= htmlspecialchars($livre['titre']) ?>" width="10px" height="20px" >
                    </td>
                    <td>
                        <a href="modifier_livre.php?id=<?= $livre['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                        <a href="supprimer_livre.php?id=<?= $livre['id'] ?>" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce livre ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    <a href="logout.php" class="btn btn-primary mt-3">Déconnexion</a>
</div>
<?php include './footer.php'; ?>
</body>
</html>

