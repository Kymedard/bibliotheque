<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Rediriger vers la page d'accueil si l'utilisateur n'est pas connecté
    exit();
}

// Récupérer les informations de l'utilisateur depuis la session
$user_id = $_SESSION['user_id']; // L'ID utilisateur stocké en session
$user_name = $_SESSION['user_name']; // Le nom de l'utilisateur
$user_email = $_SESSION['user_email']; // L'email de l'utilisateur

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
    <title>Mon Profil</title>
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
                    <li class="nav-item"><a class="nav-link" href="mes_favoris.php">Mes Favoris</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <style>
       /* Importation de la police Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
}

/* Navbar */
.navbar {
    background: linear-gradient(135deg, #1a237e, #3949ab);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 1rem 0;
}

/* Logo */
.navbar-brand {
    font-size: 2rem;
    font-weight: 700;
    color: #ffd700 !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: color 0.3s ease-in-out;
}

.navbar-brand:hover {
    color: #ffed4a !important;
}

/* Liens de navigation */
.navbar-nav .nav-link {
    color: white !important;
    font-size: 1.1rem;
    font-weight: 500;
    margin: 0 10px;
    padding: 8px 16px;
    border-radius: 4px;
    transition: all 0.3s ease-in-out;
    position: relative;
}

/* Effet de survol */
.navbar-nav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
    color: #ffd700 !important;
}

/* Soulignement animé */
.navbar-nav .nav-link::after {
    content: "";
    display: block;
    width: 0;
    height: 3px;
    background: #ffd700;
    transition: width 0.3s ease-in-out;
    position: absolute;
    bottom: -4px;
    left: 0;
}

.navbar-nav .nav-link:hover::after {
    width: 100%;
}

/* Bouton hamburger */
.navbar-toggler {
    border: none;
    background: transparent;
    outline: none;
}

.navbar-toggler-icon {
    filter: invert(100%);
}

.navbar-toggler:focus, 
.navbar-toggler:hover {
    transform: scale(1.1);
}

/* Footer */
footer {
    background: linear-gradient(135deg, #1a237e, #3949ab);
    padding: 2rem 0;
    margin-top: 4rem;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    color: white;
}

footer p {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
}

    </style>

    <!-- Profil -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center">Mon Profil</h2>

                <div class="card">
                    <div class="card-header">
                        <h4>Informations de l'utilisateur</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Nom :</strong> <?php echo htmlspecialchars($user_name); ?></p>
                        <p><strong>Email :</strong> <?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                    <div class="card-footer text-center">
                        <a href="modifier_profil.php" class="btn btn-warning">Modifier mes informations</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <footer class="mt-5 bg-dark text-white text-center py-3">
        <p>&copy; 2025 CLAC LIGNE - Tous droits réservés</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
