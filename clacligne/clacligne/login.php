<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include './config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $utilisateurs = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateurs && password_verify($password, $utilisateurs['password'])) {
        // Stocker les informations de l'utilisateur en session
        $_SESSION['utilisateurs_id'] = $utilisateurs['id'];
        $_SESSION['utilisateurs_name'] = $utilisateurs['nom'];
        $_SESSION['utilisateurs_role'] = $utilisateurs['role']; // "admin" ou "utilisateur"

        // Redirection en fonction du rôle
        if ($utilisateurs['role'] === 'admin') {
            header("Location: ./admin.php"); // Page admin
        } else {
            header("Location: ./espace_membre.php"); // Page utilisateur
        }
        exit;
    } else {
        $_SESSION['message'] = "Email ou mot de passe incorrect.";
        header("Location: login.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<?php include './header.php'; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center">Connexion</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <div class="mb-3">
                <label class="form-label">Email :</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe :</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            <p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
        </form>
    </div>
</div>

<?php include './footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
