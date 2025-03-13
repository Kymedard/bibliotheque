<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier le rôle de l'utilisateur dans la session, avec un rôle par défaut pour les invités
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;  // null signifie non connecté

// Fonction pour rediriger un utilisateur si nécessaire
function redirectIfNotAdmin() {
    if ($_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLAC LIGNE</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">CLAC LIGNE</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="catalogue.php">Catalogue</a></li>

                    <?php if ($user_role === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin.php">Tableau de bord</a></li>
                        <li class="nav-item"><a class="nav-link" href="gestion_utilisateurs.php">Gérer les utilisateurs</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>

                    <?php elseif ($user_role === 'utilisateur'): ?>
                        <li class="nav-item"><a class="nav-link" href="mes_favoris.php">Mes Favoris</a></li>
                        <li class="nav-item"><a class="nav-link" href="espace_membre.php">Mon Profil</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>

                    <?php else: ?>
                        <!-- Afficher les options seulement si l'utilisateur n'est pas connecté -->
                        <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Inscription</a></li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>

    <style>
        /* Importation de la police Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        .navbar {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: 700;
            color: #ffd700 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .navbar-nav .nav-link {
            margin: 0 10px;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        footer {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            padding: 2rem 0;
            margin-top: 4rem;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        footer p {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
    </style>
</body>

</html>
