<?php
session_start();
include './config.php';

if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "Veuillez vous connecter.";
    header("Location: login.php");
    exit;
}

$utilisateurs_id = $_SESSION['utilisateurs_id'];
$livres_id = $_POST['livres_id'] ?? null;

if (!$livres_id) {
    $_SESSION['message'] = "Erreur : livre non spécifié.";
    header("Location: catalogue.php");
    exit;
}

// Vérifier si le livre existe
$stmt = $pdo->prepare("SELECT id FROM livres WHERE id = ?");
$stmt->execute([$livres_id]);
if (!$stmt->fetch()) {
    $_SESSION['message'] = "Erreur : livre introuvable.";
    header("Location: catalogue.php");
    exit;
}

// Vérifier si le livre est déjà en favoris
$stmt = $pdo->prepare("SELECT id FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
$stmt->execute([$utilisateurs_id, $livres_id]);
$favori_existe = $stmt->fetch();

try {
    if ($favori_existe) {
        // Retirer des favoris
        $stmt = $pdo->prepare("DELETE FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
        $stmt->execute([$utilisateurs_id, $livres_id]);
        $_SESSION['message'] = "Livre retiré des favoris.";
    } else {
        // Ajouter aux favoris
        $stmt = $pdo->prepare("INSERT INTO favoris (utilisateurs_id, livres_id) VALUES (?, ?)");
        $stmt->execute([$utilisateurs_id, $livres_id]);
        $_SESSION['message'] = "Livre ajouté aux favoris !";
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur lors de la modification des favoris.";
}

header("Location: livre.php?id=$livres_id");
exit;
?>
