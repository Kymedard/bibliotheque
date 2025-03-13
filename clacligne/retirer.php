<?php
session_start();
include './config.php';



// $utilisateurs_id = $_SESSION['utilisateurs_id'];

if (isset($_GET['action'], $_GET['livre_id'])) {
    $livre_id = $_GET['livre_id'];

    if ($_GET['action'] == 'retirer') {
        // Vérifier que le livre est bien en favoris
        $stmt = $pdo->prepare("SELECT * FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
        $stmt->execute([$utilisateurs_id, $livre_id]);

        if ($stmt->rowCount() > 0) {
            // Supprimer le livre des favoris
            $stmt = $pdo->prepare("DELETE FROM favoris WHERE utilisateurs_id = ? AND livres_id = ?");
            $stmt->execute([$utilisateurs_id, $livre_id]);

            $_SESSION['message'] = "Livre retiré des favoris avec succès.";
        } else {
            $_SESSION['message'] = "Ce livre ne fait pas partie de vos favoris.";
        }
    }

    // Redirection
    header("Location: mes_favoris.php");
    exit();
}
?>
