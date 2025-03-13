<?php
session_start();
include './config.php';

// // Vérifier si l'utilisateur est bien un administrateur
// if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
//     $_SESSION['message'] = "Accès refusé.";
//     header("Location: ./login.php");
//     exit;
// }

// Statistiques
$stmt = $pdo->query("SELECT COUNT(*) AS total_livres FROM livres");
$total_livres = $stmt->fetch(PDO::FETCH_ASSOC)['total_livres'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_utilisateurs FROM utilisateurs");
$total_utilisateurs = $stmt->fetch(PDO::FETCH_ASSOC)['total_utilisateurs'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_emprunts FROM emprunts WHERE statut = 'emprunté'");
$total_emprunts = $stmt->fetch(PDO::FETCH_ASSOC)['total_emprunts'];

$stmt = $pdo->query("SELECT COUNT(*) AS total_reservations FROM reservations WHERE statut = 'en attente'");
$total_reservations = $stmt->fetch(PDO::FETCH_ASSOC)['total_reservations'];

// Informations de l'administrateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['utilisateurs_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

include './header.php';
?>

<div class="container-fluid py-4">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="card-title">
                        <i class="fas fa-user-shield me-2"></i>
                        Bienvenue, <?= htmlspecialchars($admin['nom']); ?> !
                    </h2>
                    <p class="card-text">Tableau de bord administrateur</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-primary">
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-3">
                        <i class="fas fa-book"></i>
                    </div>
                    <h5 class="card-title">Total Livres</h5>
                    <p class="card-text display-6"><?= $total_livres; ?></p>
                    <a href="ajouter_livre.php" class="btn btn-primary w-100">
                        <i class="fas fa-plus-circle me-2"></i>Ajouter un livre
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-success">
                <div class="card-body text-center">
                    <div class="display-4 text-success mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Total Utilisateurs</h5>
                    <p class="card-text display-6"><?= $total_utilisateurs; ?></p>
                    <a href="gestion_utilisateurs.php" class="btn btn-success w-100">
                        <i class="fas fa-user-cog me-2"></i>Gérer les utilisateurs
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-info">
                <div class="card-body text-center">
                    <div class="display-4 text-info mb-3">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h5 class="card-title">Emprunts en cours</h5>
                    <p class="card-text display-6"><?= $total_emprunts; ?></p>
                    <a href="gestion_emprunts.php?filter=emprunts" class="btn btn-info w-100 text-white">
                        <i class="fas fa-clipboard-list me-2"></i>Voir les emprunts
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card h-100 border-warning">
                <div class="card-body text-center">
                    <div class="display-4 text-warning mb-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5 class="card-title">Réservations en attente</h5>
                    <p class="card-text display-6"><?= $total_reservations; ?></p>
                    <a href="suivi_reservations.php?filter=reservations" class="btn btn-warning w-100">
                        <i class="fas fa-calendar-check me-2"></i>Voir les réservations
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des livres -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">
                        <i class="fas fa-books me-2"></i>
                        Liste des Livres
                    </h3>
                </div>
                <div class="col-auto">
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher un livre...">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="livresTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Couverture</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Catégorie</th>
                            <th>Année</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM livres ORDER BY titre");
                    while ($livre = $stmt->fetch(PDO::FETCH_ASSOC)):
                    ?>
                        <tr>
                            <td><?= $livre['id'] ?></td>
                            <td>
                                <?php if ($livre['couverture']): ?>
                                    <img src="uploads/<?= htmlspecialchars($livre['couverture']) ?>" 
                                         alt="Couverture" class="img-thumbnail" style="max-height: 50px;">
                                <?php else: ?>
                                    <span class="text-muted">Aucune couverture</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($livre['titre']) ?></td>
                            <td><?= htmlspecialchars($livre['auteur']) ?></td>
                            <td><?= htmlspecialchars($livre['categorie']) ?></td>
                            <td><?= htmlspecialchars($livre['annee_publication']) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="modifier_livre.php?id=<?= $livre['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="supprimer_livre.php?id=<?= $livre['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction de recherche
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('livresTable');
    const rows = table.getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        
        for(let i = 1; i < rows.length; i++) {  // Commence à 1 pour ignorer la ligne d'en-tête
            const row = rows[i];
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        }
    });

    // Animation des cartes statistiques
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Actualiser la table lors du changement de recherche
    searchInput.addEventListener('input', function() {
        // Cette fonction se déclenche chaque fois que l'utilisateur tape ou supprime quelque chose
        const term = searchInput.value.toLowerCase();

        for (let i = 1; i < rows.length; i++) {  // Commence à 1 pour ignorer la ligne d'en-tête
            const row = rows[i];
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        }
    });
});

</script>

<?php include './footer.php'; ?>
