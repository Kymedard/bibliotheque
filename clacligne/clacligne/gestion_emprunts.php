<?php
require 'config.php'; // Connexion à la base de données

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
</body>
</html>
