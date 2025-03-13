<?php
session_start();
include './config.php';

// Vérification de l'existence de l'utilisateur dans la session
if (!isset($_SESSION['utilisateurs_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php"); 
    exit();
}

$utilisateurs_id = $_SESSION['utilisateurs_id'];

// Requête pour récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$utilisateurs_id]);
$utilisateurs = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérification si l'utilisateur existe dans la base de données
if (!$utilisateurs) {
    header("Location: login.php");
    exit();
}

include './header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
       :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --accent-color: #17a2b8;
            --background-light: #f8f9fa;
            --text-dark: #343a40;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }

        body {
            background-color: var(--background-light);
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 900px;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
            text-align: center;
        }

        .card {
            transition: all 0.3s ease-in-out;
            margin-bottom: 1rem;
            border: none;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .status-en-cours {
            background-color: var(--warning-color);
            color: #212529;
            border: 1px solid var(--warning-color);
        }

        .status-termine {
            background-color: var(--success-color);
            color: white;
            border: 1px solid var(--success-color);
        }

        .favorite-book {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border: 1px solid var(--secondary-color);
            transition: all 0.3s ease-in-out;
        }

        .favorite-book:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .favorite-book a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: bold;
        }

        .favorite-book:hover a {
            color: white;
        }

        .section-title {
            border-left: 5px solid var(--primary-color);
            padding-left: 1rem;
            margin: 2rem 0;
            font-weight: bold;
            color: var(--text-dark);
        }

        .logout-btn {
            background: linear-gradient(to right, var(--primary-color), var(--accent-color));
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            transition: all 0.3s;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

    
    

        .logout-btn:hover {
            opacity: 0.85;
            transform: scale(1.05);
        }

        .alert-info {
            background-color: var(--background-light);
            border-left: 5px solid var(--secondary-color);
            color: var(--text-dark);
            font-weight: bold;
        }

        .btn-outline-danger {
            color: var(--danger-color);
            border-color: var(--danger-color);
            transition: all 0.3s ease-in-out;
        }

        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        @media (max-width: 768px) {
            .welcome-section {
                padding: 1.5rem;
            }
            .logout-btn {
                padding: 0.6rem 1.5rem;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="welcome-section">
        <h2 class="mb-0"><i class="fas fa-user-circle mr-2"></i> Bienvenue, <?= htmlspecialchars($utilisateurs['nom']); ?> !</h2>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php


$utilisateurs_id = $_SESSION['utilisateurs_id'];

$stmt = $pdo->prepare("
    SELECT reservations.id_reservations, IFNULL(livres.titre, 'Livre supprimé') AS titre, 
           reservations.date_reservation, reservations.date_retrait, reservations.statut
    FROM reservations
    LEFT JOIN livres ON reservations.id_livre = livres.id
    WHERE reservations.id_lecteur = ?
    ORDER BY reservations.date_reservation DESC;
");

$stmt->execute([$utilisateurs_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h3 class="section-title">Mes Réservations</h3>
<div class="row">
    <?php if (empty($reservations)): ?>
        <p>Aucune réservation trouvée.</p>
    <?php else: ?>
        <?php foreach ($reservations as $reservation): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($reservation['titre']) ?></h5>
                        <p class="card-text">
                            <small class="text-muted">Réservé le <?= date('d/m/Y', strtotime($reservation['date_reservation'])) ?></small>
                        </p>

                        <!-- Affichage de la date de retrait -->
                        <p class="card-text">
                            <strong>Retrait prévu :</strong> 
                            <?= !empty($reservation['date_retrait']) ? date('d/m/Y', strtotime($reservation['date_retrait'])) : 'Non défini'; ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="status-badge <?= $reservation['statut'] == 'En attente' ? 'status-en-cours' : 'status-termine' ?>">
                                <?= htmlspecialchars($reservation['statut']) ?>
                            </span>

                            <!-- Bouton Annuler si statut "En attente" -->
                            <?php if ($reservation['statut'] == 'En attente'): ?>
                                <a href="annuler_reservation.php?id=<?= htmlspecialchars($reservation['id_reservations']) ?>" 
                                   class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>



    <h3 class="section-title">Mes Emprunts</h3>
    <div class="row">
        <?php 
        $stmt = $pdo->prepare("
            SELECT emprunts.id_emprunt, livres.titre, emprunts.date_emprunt, emprunts.statut
            FROM emprunts
            INNER JOIN livres ON emprunts.id_livre = livres.id
            WHERE emprunts.utilisateurs_id = ?
            ORDER BY emprunts.date_emprunt DESC
        ");
        $stmt->execute([$utilisateurs_id]);
        $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($emprunts as $emprunt): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($emprunt['titre']) ?></h5>
                    <p class="card-text">
                        <small class="text-muted">Emprunté le <?= date('d/m/Y', strtotime($emprunt['date_emprunt'])) ?></small>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="status-badge <?= $emprunt['statut'] == 'En cours' ? 'status-en-cours' : 'status-termine' ?>">
                            <?= $emprunt['statut'] ?>
                        </span>
                        <?php if ($emprunt['statut'] == 'En cours'): ?>
                            <a href="emprunter_livre.php?id=<?= $emprunt['id'] ?>&action=remove" 
                               class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h3 class="section-title">Mes livres favoris</h3>
    <div class="row">
        <?php
        $stmt = $pdo->prepare("SELECT livres.id, livres.titre FROM favoris JOIN livres ON favoris.livres_id = livres.id WHERE favoris.utilisateurs_id = ?");
        $stmt->execute([$utilisateurs_id]);
        while ($livre = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="col-md-6">
                <div class="favorite-book">
                    <a href="livre.php?id=<?= $livre['id'] ?>" class="text-decoration-none">
                        <i class="fas fa-book mr-2"></i>
                        <?= htmlspecialchars($livre['titre']) ?>
                        <i class="fas fa-chevron-right float-right"></i>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="text-center mt-5 mb-5">
        <a href="logout.php" class="btn btn-primary logout-btn">
            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
        </a>
    </div>
</div>
<?php include './footer.php'; ?>
</body>
</html>
