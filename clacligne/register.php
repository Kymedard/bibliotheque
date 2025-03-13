<?php
session_start();
include './config.php';

// Vérifier si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // "utilisateur" ou "admin"
    $code_secret = isset($_POST['code_secret']) ? trim($_POST['code_secret']) : null;

    // Vérifier si tous les champs sont remplis
    if (empty($nom) || empty($email) || empty($password)) {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
        header("Location: register.php");
        exit;
    }

    // Hacher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Vérification du code secret si c'est un admin
    if ($role == "admin") {
        $admin_code_secret = "ADMIN2025"; // Code à changer
        if ($code_secret !== $admin_code_secret) {
            $_SESSION['message'] = "Code secret invalide.";
            header("Location: register.php");
            exit;
        }
    } else {
        $code_secret = null; // Un utilisateur normal n'a pas besoin de code secret
    }

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "Cet email est déjà utilisé.";
        header("Location: register.php");
        exit;
    }

    // Insérer l'utilisateur dans la base de données
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, password, role, code_secret) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$nom, $email, $hashed_password, $role, $code_secret])) {
        $_SESSION['message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['message'] = "Erreur lors de l'inscription.";
        header("Location: register.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="*
    ../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include './header.php'; ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
            <h2 class="text-center mb-4">Inscription</h2>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']); 
                    ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-warning"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            
            <form action="" method="post">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom complet</label>
                    <input type="text" name="nom" id="nom" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Type d'utilisateur</label>
                    <select name="role" id="role" class="form-select" onchange="toggleCodeSecret()">
                        <option value="utilisateur">Lecteur</option>
                        <option value="admin">Ecrivain</option>
                    </select>
                </div>

                <div class="mb-3" id="codeSecretContainer" style="display: none;">
                    <label for="code_secret" class="form-label">Code secret (Ecrivain)</label>
                    <input type="text" name="code_secret" id="code_secret" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
            </form>

            <p class="text-center mt-3">
                Déjà inscrit ? <a href="login.php">Se connecter</a>
            </p>
        </div>
    </div>

    <script>
        function toggleCodeSecret() {
            var role = document.getElementById("role").value;
            var codeSecretContainer = document.getElementById("codeSecretContainer");
            codeSecretContainer.style.display = (role === "admin") ? "block" : "none";
        }
    </script>
    <script>
        document.getElementById("role-select").addEventListener("change", function() {
            let codeSecretDiv = document.getElementById("code-secret-div");
            if (this.value === "admin") {
                codeSecretDiv.style.display = "block";
            } else {
                codeSecretDiv.style.display = "none";
            }
        });
    </script>

    <?php include './footer.php'; ?>
</body>
</html>
