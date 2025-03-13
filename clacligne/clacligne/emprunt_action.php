<?php
session_start();
include './config.php';

if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "Veuillez vous connecter.";
    header("Location: login.php");
    exit;
}

$utilisateurs_id = $_SESSION['utilisateurs_id'];
$livres_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if (!$livres_id || !$action) {
    $_SESSION['message'] = "Action invalide.";
    header("Location: dashboard.php");
    exit;
}

if ($action === "add") {
    // Vérifier si le livre est déjà emprunté par l'utilisateur
    $check = $pdo->prepare("SELECT id FROM emprunts WHERE id_livre = ? AND utilisateurs_id = ? AND statut = 'En cours'");
    $check->execute([$livres_id, $utilisateurs_id]);

    if ($check->rowCount() == 0) {
        $date_emprunt = date('Y-m-d');
        $date_retour = date('Y-m-d', strtotime('+14 days')); // Retour dans 14 jours
        $statut = "En cours";

        $stmt = $pdo->prepare("INSERT INTO emprunts (id_livre, date_emprunt, date_retour, statut, utilisateurs_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$livres_id, $date_emprunt, $date_retour, $statut, $utilisateurs_id]);

        $_SESSION['message'] = "Livre réservé avec succès !";
    } else {
        $_SESSION['message'] = "Vous avez déjà emprunté ce livre.";
    }
} elseif ($action === "remove") {
    $stmt = $pdo->prepare("DELETE FROM emprunts WHERE utilisateurs_id = ? AND id_livre = ?");
    $stmt->execute([$utilisateurs_id, $livres_id]);

    $_SESSION['message'] = "Réservation annulée avec succès.";
}

// Rediriger vers l'espace personnel de l'utilisateur
header("Location: espace_membre.php");
exit;
?>
