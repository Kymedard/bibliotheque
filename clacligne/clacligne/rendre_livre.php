<?php
require 'config.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Mettre à jour le statut de l'emprunt
    $query = $pdo->prepare("UPDATE emprunts SET statut = 'Rendu' WHERE id = ?");
    $query->execute([$id]);

    // Redirection vers la liste des emprunts
    header("Location: admin_emprunts.php");
    exit();
}
?>
