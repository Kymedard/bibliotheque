<?php
require 'config.php'; // Connexion à la base de données
include './header.php';


$query = $pdo->query("SELECT emprunts.*, livres.titre, lecteurs.nom 
                      FROM emprunts 
                      JOIN livres ON emprunts.id_livre = livres.id 
                      JOIN lecteurs ON emprunts.id_lecteur = lecteurs.id 
                      WHERE emprunts.statut = 'En cours'");

$emprunts = $query->fetchAll();

$query = $pdo->query("SELECT emprunts.*, livres.titre, lecteurs.email 
                      FROM emprunts 
                      JOIN livres ON emprunts.id_livre = livres.id 
                      JOIN lecteurs ON emprunts.id_lecteur = lecteurs.id 
                      WHERE emprunts.statut = 'En cours' AND emprunts.date_retour < CURDATE()");

$emprunts_retard = $query->fetchAll();

foreach ($emprunts_retard as $emprunt) {
    $message = "Bonjour, votre livre '" . $emprunt['titre'] . "' est en retard. Merci de le rendre rapidement.";
    mail($emprunt['email'], "Rappel : Retour de livre en retard", $message);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des emprunts</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #343a40;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e2e6ea;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
    <h2>Liste des emprunts en cours</h2>
    <table border="1">
        <tr>
            <th>Livre</th>
            <th>Lecteur</th>
            <th>Date d'emprunt</th>
            <th>Date retour prévue</th>
            <th>Action</th>
        </tr>
        <?php foreach ($emprunts as $emprunt): ?>
            <tr>
                <td><?= htmlspecialchars($emprunt['titre']) ?></td>
                <td><?= htmlspecialchars($emprunt['nom']) ?></td>
                <td><?= $emprunt['date_emprunt'] ?></td>
                <td><?= $emprunt['date_retour'] ?></td>
                <td>
                    <a href="rendre_livre.php?id=<?= $emprunt['id'] ?>">Marquer comme rendu</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="admin.php" class="btn btn-secondary">⬅️ Retour au tableau de bord</a>

</body>
</html>
<?php include './footer.php'; ?>
