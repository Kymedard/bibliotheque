<?php 
include 'config.php'; 
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - CLAC LIGNE</title>
    <style>
        .hero-section {
            background: linear-gradient(rgba(26, 35, 126, 0.9), rgba(57, 73, 171, 0.9)), url('assets/images/library-bg.jpg');
            background-size: cover;
            background-position: center;
            padding: 6rem 0;
            color: white;
            margin-top: -2rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .search-form {
            background: rgba(255,255,255,0.1);
            padding: 2rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .search-input {
            border: none;
            border-radius: 30px;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
        }

        .search-button {
            background: #ffd700;
            color: #1a237e;
            border: none;
            border-radius: 30px;
            padding: 1rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-button:hover {
            background: #ffed4a;
            transform: translateY(-2px);
        }

        .popular-books {
            background: #f8f9fa;
            padding: 4rem 0;
            margin-top: 3rem;
        }

        .section-title {
            color: #1a237e;
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 4px;
            background: #ffd700;
            margin: 1rem auto;
        }

        .book-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .book-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .book-card .card-body {
            padding: 1.5rem;
        }

        .book-title {
            color: #1a237e;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .book-author {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .btn-info {
            background: #3949ab;
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-info:hover {
            background: #1a237e;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="hero-title">Bienvenue à CLAC LIGNE</h1>
        <p class="hero-subtitle">Découvrez notre vaste collection de livres numériques et physiques</p>

        <!-- Moteur de recherche -->
        <form action="./catalogue.php" method="GET" class="search-form">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-input" placeholder="Rechercher un livre, un auteur...">
                        <button type="submit" class="btn search-button">Rechercher</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Section des livres populaires -->
<section class="popular-books">
    <div class="container">
        <h2 class="section-title text-center">Livres Populaires</h2>
        <div class="row">
        <?php
        $query = $pdo->query("SELECT * FROM livres ORDER BY date_ajout DESC LIMIT 10");
        while ($livre = $query->fetch()) {
            echo '<div class="col-md-3">';
            echo '<div class="card">';
            echo '<img src="uploads/'.$livre['couverture'].'" class="card-img-top" alt="'.$livre['titre'].' " style="max-width: 400px; max-height: 300px">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">'.$livre['titre'].'</h5>';
            echo '<p class="card-text">'.$livre['auteur'].'</p>';
            echo '<a href="livre.php?id='.$livre['id'].'" class="btn btn-info">Voir plus</a>';
            echo '</div></div></div>';
        }
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
