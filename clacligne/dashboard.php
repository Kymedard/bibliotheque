<?php
// session_start();
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: admin_login.php");
//     exit;
// }
// session_start();
// if (!isset($_SESSION['utilisateurs_id']) || $_SESSION['user_role'] !== 'admin') {
//     $_SESSION['message'] = "Accès interdit.";
//     header("Location: ../login.php");
//     exit;
// }

session_start(); // Un seul appel

// Vérifier si l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Vérifier si un utilisateur essaye d'accéder à l'espace admin
if (isset($_SESSION['utilisateurs_id']) && $_SESSION['user_role'] !== 'admin') {
    $_SESSION['message'] = "Accès interdit.";
    header("Location: ../login.php");
    exit;
}

?>

