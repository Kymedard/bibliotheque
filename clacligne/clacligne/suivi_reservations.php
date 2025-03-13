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

// Gestion des actions (changer de statut)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    $nouveau_statut = '';
    if ($action === 'emprunter') {
        $nouveau_statut = 'emprunté';
    } elseif ($action === 'retourner') {
        $nouveau_statut = 'retourné';
    }

    if ($nouveau_statut) {
        $stmt = $pdo->prepare("UPDATE emprunts SET statut = ? WHERE id = ?");
        $stmt->execute([$nouveau_statut, $id]);
        $_SESSION['message'] = "Statut mis à jour avec succès.";
    }
}

// Récupérer les réservations et emprunts
$query = "SELECT e.id, l.titre, u.nom, e.date_emprunt, e.statut
          FROM emprunts e
          JOIN livres l ON e.id_livre = l.id
          JOIN utilisateurs u ON e.utilisateurs_id = u.id
          ORDER BY e.date_emprunt DESC";
$stmt = $pdo->query($query);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-5">
    <h2 class="mb-4">📖 Suivi des réservations et emprunts</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="bg-primary text-white">
            <tr>
                <th>Livre</th>
                <th>Utilisateur</th>
                <th>Date de réservation</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $res): ?>
                <tr>
                    <td><?= htmlspecialchars($res['titre']) ?></td>
                    <td><?= htmlspecialchars($res['nom']) ?></td>
                    <td><?= htmlspecialchars($res['date_emprunt']) ?></td>
                    <td><span class="badge bg-<?php
                        switch ($res['statut']) {
                            case 'réservé': echo 'info'; break;
                            case 'emprunté': echo 'success'; break;
                            case 'retourné': echo 'secondary'; break;
                        }
                    ?>">
                        <?= htmlspecialchars($res['statut']) ?></span></td>
                    <td>
                        <?php if ($res['statut'] === 'réservé'): ?>
                            <a href="admin_reservations.php?action=emprunter&id=<?= $res['id'] ?>" class="btn btn-success btn-sm">📦 Marquer comme emprunté</a>
                        <?php elseif ($res['statut'] === 'emprunté'): ?>
                            <a href="admin_reservations.php?action=retourner&id=<?= $res['id'] ?>" class="btn btn-warning btn-sm">↩️ Marquer comme retourné</a>
                        <?php else: ?>
                            ✅ Retour effectué
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="admin.php" class="btn btn-secondary">⬅️ Retour au tableau de bord</a>
</div>

<?php include './footer.php'; ?>
