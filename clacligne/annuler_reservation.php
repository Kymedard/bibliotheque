<?php
session_start();
require './config.php'; // Assure-toi que ce fichier contient la connexion à la base de données

// Vérifie si l'ID est bien passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ID de réservation invalide !";
    header("Location: reserver.php");
    exit();
}

$id_reservation = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id_reservations = ?");
    $stmt->execute([$id_reservation]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Réservation annulée avec succès !";
    } else {
        $_SESSION['message'] = "Impossible d'annuler la réservation. livre non trouvé.";
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur lors de l'annulation : " . $e->getMessage();
}

header("Location: reserver.php");
exit();
?>
