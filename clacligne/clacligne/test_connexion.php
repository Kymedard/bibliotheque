<?php
    include 'config.php';

    try {
        echo "Connexion réussie à la base de données";
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
?>

<?php
  $nom = "hj";
  echo "le no ". $nom;
