<?php
session_start();
include './config.php';

// Vérifier si les données nécessaires sont envoyées via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer l'ID du livre et la date de retour
    $livre_id = filter_input(INPUT_POST, 'livre_id', FILTER_VALIDATE_INT);
    $date_retour = filter_input(INPUT_POST, 'date_retour', FILTER_DEFAULT);

    // Vérifier que l'ID du livre et la date de retour sont valides
    if (!$livre_id || !$date_retour) {
        $_SESSION['message'] = "L'emprunt a échoué. Veuillez fournir des informations valides.";
        header("Location: ./emprunter.php?id=" . $livre_id);
        exit;
    }

    // Vérifier si le livre est déjà emprunté
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM emprunts WHERE id_livre = ? AND statut = 'En cours'");
    $stmt->execute([$livre_id]);
    $emprunt_existant = $stmt->fetchColumn();

    if ($emprunt_existant > 0) {
        $_SESSION['message'] = "Ce livre est déjà emprunté.";
        header("Location: ./catalogue.php");
        exit;
    }

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['utilisateurs_id'])) {
        $_SESSION['message'] = "Vous devez être connecté pour effectuer un emprunt.";
        header("Location: ./login.php");
        exit;
    }

    // Récupérer l'ID de l'utilisateur connecté
    $utilisateur_id = $_SESSION['utilisateurs_id'];

    // Insérer l'emprunt dans la table `emprunts`
    $stmt = $pdo->prepare("INSERT INTO emprunts (id_livre, id_lecteur, date_emprunt, date_retour, statut) 
                           VALUES (?, ?, NOW(), ?, 'En cours')");
    $stmt->execute([$livre_id, $utilisateur_id, $date_retour]);

    $_SESSION['message'] = "Emprunt effectué avec succès.";
    header("Location: ./catalogue.php");
    exit;
}
?>
