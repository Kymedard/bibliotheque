<?php
include './config.php';

try {
    // Création de la table favoris si elle n'existe pas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS favoris (
            id INT PRIMARY KEY AUTO_INCREMENT,
            utilisateurs_id INT NOT NULL,
            livres_id INT NOT NULL,
            date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateurs_id) REFERENCES utilisateurs(id),
            FOREIGN KEY (livres_id) REFERENCES livres(id),
            UNIQUE KEY unique_favori (utilisateurs_id, livres_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Mise à jour de la table emprunts si elle n'existe pas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS emprunts (
            id INT PRIMARY KEY AUTO_INCREMENT,
            id_livre INT NOT NULL,
            utilisateurs_id INT NOT NULL,
            date_emprunt DATETIME NOT NULL,
            date_retour_prevue DATE NOT NULL,
            date_retour DATETIME NULL,
            statut ENUM('en attente', 'emprunté', 'retourné') NOT NULL DEFAULT 'en attente',
            FOREIGN KEY (id_livre) REFERENCES livres(id),
            FOREIGN KEY (utilisateurs_id) REFERENCES utilisateurs(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    echo "Les tables ont été créées ou mises à jour avec succès !";
} catch (PDOException $e) {
    echo "Erreur lors de l'initialisation des tables : " . $e->getMessage();
}
?>
