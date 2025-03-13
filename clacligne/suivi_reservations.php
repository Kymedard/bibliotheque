<?php
session_start();
include './config.php';
include './header.php';

// Générer un token CSRF pour la session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gestion des actions via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'], $_POST['csrf_token'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'];
    $csrf_token = $_POST['csrf_token'];

    // Vérification du token CSRF
    if ($csrf_token !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = "Action interdite !";
        header("Location: admin_reservations.php");
        exit();
    }

    // Définition des statuts valides
    $statuts_valides = [
        'reserver' => 'réservé',
        'annuler' => 'annulé'
    ];

    if (array_key_exists($action, $statuts_valides)) {
        $nouveau_statut = $statuts_valides[$action];

        // Vérifier si la réservation existe
        $stmt = $pdo->prepare("SELECT id FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        $reservation = $stmt->fetch();

        if ($reservation) {
            // Mise à jour du statut
            $stmt = $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ?");
            if ($stmt->execute([$nouveau_statut, $id])) {
                $_SESSION['message'] = "Réservation mise à jour avec succès.";
            } else {
                $_SESSION['message'] = "Erreur lors de la mise à jour.";
            }
        } else {
            $_SESSION['message'] = "Aucune réservation trouvée avec cet ID.";
        }
    } else {
        $_SESSION['message'] = "Action invalide.";
    }

    header("Location: admin_reservations.php");
    exit();
}

// Filtrage des réservations
$filtre_statut = $_GET['statut'] ?? '';
$filtre_utilisateur = $_GET['utilisateur'] ?? '';
$filtre_date = $_GET['date'] ?? '';

// Pagination
$limit = 10; // Nombre de réservations par page
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// Construction de la requête SQL avec filtres
$query = "SELECT 
    reservations.id_reservations, 
    livres.titre, 
    utilisateurs.nom AS utilisateur, 
    reservations.date_reservation, 
    reservations.date_retrait, 
    reservations.statut
FROM reservations
JOIN livres ON reservations.id_livre = livres.id
JOIN utilisateurs ON reservations.id_lecteur = utilisateurs.id
WHERE 1=1";

$params = [];

if (!empty($filtre_statut)) {
    $query .= " AND reservations.statut = ?";
    $params[] = $filtre_statut;
}
if (!empty($filtre_utilisateur)) {
    $query .= " AND utilisateurs.nom LIKE ?";
    $params[] = "%$filtre_utilisateur%";
}
if (!empty($filtre_date)) {
    $query .= " AND reservations.date_reservation = ?";
    $params[] = $filtre_date;
}

$query .= " ORDER BY reservations.date_reservation DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nombre total de réservations pour la pagination
$total_query = "SELECT COUNT(*) FROM reservations WHERE 1=1";
$total_stmt = $pdo->query($total_query);
$total_reservations = $total_stmt->fetchColumn();
$total_pages = ceil($total_reservations / $limit);
?>

<div class="container mt-5">
    <h2 class="mb-4">📖 Suivi des réservations</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de filtres -->
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="utilisateur" class="form-control" placeholder="Rechercher utilisateur" value="<?= htmlspecialchars($filtre_utilisateur) ?>">
            </div>
            <div class="col-md-3">
                <select name="statut" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="réservé" <?= $filtre_statut === 'réservé' ? 'selected' : '' ?>>Réservé</option>
                    <option value="annulé" <?= $filtre_statut === 'annulé' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filtre_date) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th>Livre</th>
                <th>Utilisateur</th>
                <th>Date de réservation</th>
                <th>Date de retrait</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $res): ?>
                <tr>
                    <td><?= htmlspecialchars($res['titre']) ?></td>
                    <td><?= htmlspecialchars($res['utilisateur']) ?></td>
                    <td><?= htmlspecialchars($res['date_reservation']) ?></td>
                    <td><?= $res['date_retrait'] ? htmlspecialchars($res['date_retrait']) : 'Non définie' ?></td>
                    <td><span class="badge bg-info"><?= htmlspecialchars($res['statut']) ?></span></td>
                    <td>
                        <a href="emprunt_action.php?action=emprunter&id=<?= $res['id_reservations'] ?>" class="btn btn-success btn-sm">📦 Marquer comme emprunté</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&statut=<?= $filtre_statut ?>&utilisateur=<?= urlencode($filtre_utilisateur) ?>&date=<?= $filtre_date ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <a href="admin.php" class="btn btn-secondary">⬅️ Retour au tableau de bord</a>
</div>

<?php include './footer.php'; ?>
