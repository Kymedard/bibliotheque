<?php
session_start();
include './config.php';

if (!isset($_SESSION['utilisateurs_id'])) {
    $_SESSION['message'] = "Veuillez vous connecter.";
    header("Location: login.php");
    exit;
}

$utilisateurs_id = $_SESSION['utilisateurs_id'];
$livres_id = $_GET['id'];
$action = $_GET['action'];

if ($action === "add") {
    $stmt = $pdo->prepare("INSERT INTO favoris (utilisateurs_id, livres_id) VALUES (?, ?)");
    $stmt->execute([$utilisateurs_id, $livres_id]);
    $_SESSION['message'] = "Livre ajouté aux favoris !";
} elseif ($action === "remove") {
    $stmt = $pdo->prepare("DELETE FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
    $stmt->execute([$utilisateurs_id, $livres_id]);
    $_SESSION['message'] = "Livre retiré des favoris.";
}

header("Location: livre.php?id=$livres_id");
exit;
?>
