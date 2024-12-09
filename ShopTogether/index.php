<?php
session_start(); // Démarre la session existante ou en crée une nouvelle
include "_connexionBD.php"; // Connexion à la base de données

if (isset($_POST["emailadress"]) && isset($_POST["password"])) {
    $reqlogin = $bd->prepare("SELECT * FROM users AS u WHERE emailAddress = :emailaddresslogin AND u.Password = SHA2(:passwordlogin, 256)");
    $reqlogin->bindValue(":emailaddresslogin", $_POST["emailadress"], PDO::PARAM_STR);
    $reqlogin->bindValue(":passwordlogin", $_POST["password"], PDO::PARAM_STR);
    $reqlogin->execute();
    $count = $reqlogin->rowCount();

    if ($count > 0) {
        // Identifiants valides, on configure la session
        $login = $reqlogin->fetch(PDO::FETCH_ASSOC);
        $_SESSION["login"] = $login; // Stocke les données de l'utilisateur
        header("Location: home.php"); // Redirection vers la page d'accueil
        exit; // Toujours arrêter l'exécution après un header
    } else {
        // Identifiants invalides, on nettoie correctement la session
        $_SESSION = []; // Vide toutes les variables de session
        session_unset(); // Détruit toutes les variables de session
        session_destroy(); // Détruit la session active
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopTogether</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="body_login">
    <main class="main_login">
        <form action="index.php" method="POST" class="LoginForm">
            <h1>
                <a href="#" class="title">ShopTogether</a>
            </h1>
            <label for="emailadress">Email</label>
            <input type="email" name="emailadress" required>
            <label for="password">Password</label>  
            <input type="password" name="password" required>
            <div>
                <input type="submit" value="Sign In" class="buttonsignin">
                <p><a href="signup.php">Sign up</a></p>
            </div>
            <p><a href="#">Forgot password</a></p>
            <?php
            if (isset($_POST["emailadress"]) && isset($_POST["password"]) && !isset($_SESSION["login"])) {
                echo "<p id='wrongcredentials'>Wrong Credentials... Try again</p>";
            }
            ?>
        </form>
    </main>
</body>
</html>
