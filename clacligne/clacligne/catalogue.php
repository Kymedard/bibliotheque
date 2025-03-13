<?php
include './config.php';
include 'header.php';

// Récupération des filtres
$search = isset($_GET['search']) ? $_GET['search'] : '';
$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : '';

// Requête SQL pour afficher les livres avec filtres
$query = "SELECT * FROM livres WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (titre LIKE ? OR auteur LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($categorie)) {
    $query .= " AND categorie = ?";
    $params[] = $categorie;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$livres = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- Section Catalogue -->
<div class="container mt-5">
    <h1 class="text-center">Catalogue des Livres</h1>

    <!-- Formulaire de recherche -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par titre ou auteur" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="categorie" class="form-control">
                    <option value="">Toutes les catégories</option>
                    <option value="Roman" <?= $categorie == "Roman" ? 'selected' : '' ?>>Litérature Fiction</option>
                    <option value="Science" <?= $categorie == "Science" ? 'selected' : '' ?>>litérature scientifique</option>
                    <option value="Histoire" <?= $categorie == "Histoire" ? 'selected' : '' ?>>Litérature Histoire</option>
                    <option value="Poème" <?= $categorie == "Poème" ? 'selected' : '' ?>>Litérature Poème</option>
                    <option value="Autre" <?= $categorie == "Autre" ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </div>
        </div>
    </form>

    <!-- Affichage des livres -->
    <div class="row">
        <?php if (count($livres) > 0): ?>
            <?php foreach ($livres as $livre): ?>
                <div class="col-md-3">
                    <div class="card mb-4">
                        <img src="./uploads/<?= htmlspecialchars($livre['couverture']) ?>" class="card-img-top" alt="<?= htmlspecialchars($livre['titre']) ?>" style="max-width: 400px; max-height: 300px">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($livre['titre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($livre['auteur']) ?></p>
                            <a href="livre.php?id=<?= $livre['id'] ?>" class="btn btn-info">Voir plus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Aucun livre trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<?php include './footer.php'; ?>
    
</body>
</html>
