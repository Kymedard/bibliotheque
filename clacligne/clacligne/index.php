<?php 
include 'config.php'; 
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>
<body>
<!-- Section Accueil -->
<section class="container mt-5">
    <h1 class="text-center">Bienvenue sur BiblioTogo</h1>
    <p class="text-center">Explorez notre collection de livres numériques et physiques.</p>

    <!-- Moteur de recherche -->
    <form action="./catalogue.php" method="GET" class="d-flex justify-content-center">
        <input type="text" name="search" class="form-control w-50" placeholder="Rechercher un livre...">
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>
</section>

<!-- Section des livres populaires -->
<section class="container mt-5">
    <h2 class="text-center">Livres populaires</h2>
    <div class="row">
        <?php
        $query = $pdo->query("SELECT * FROM livres ORDER BY date_ajout DESC LIMIT 8");
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
