<?php
session_start();

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['utilisateurs_role'] !== 'admin') {
    $_SESSION['message'] = "Accès refusé.";
    header("Location: ./login.php");
    exit;
}

include './config.php';

// Vérifier si l'ID du livre est fourni
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $livre_id = $_GET['id'];

    // Récupérer le chemin de la couverture avant suppression
    $stmt = $pdo->prepare("SELECT couverture FROM livres WHERE id = ?");
    $stmt->execute([$livre_id]);
    $livre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($livre) {
        // Supprimer le livre de la base de données
        $stmt = $pdo->prepare("DELETE FROM livres WHERE id = ?");
        if ($stmt->execute([$livre_id])) {
            // Supprimer le fichier de couverture si présent
            $chemin_couverture = './uploads/' . $livre['couverture'];
            if (file_exists($chemin_couverture)) {
                unlink($chemin_couverture);
            }
            $_SESSION['message'] = "Livre supprimé avec succès !";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression du livre.";
        }
    } else {
        $_SESSION['message'] = "Livre introuvable.";
    }
} else {
    $_SESSION['message'] = "ID de livre invalide.";
}

// Redirection vers le tableau de bord admin
header("Location: admin.php");
exit;
?>
