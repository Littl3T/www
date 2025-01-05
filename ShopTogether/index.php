<?php
session_start();
include "_connexionBD.php"; 

if (isset($_POST["emailadress"]) && isset($_POST["password"])) {
    $reqlogin = $bd->prepare("
        SELECT * 
        FROM users AS u 
        WHERE emailAddress = :emailaddresslogin 
          AND u.Password = SHA2(:passwordlogin, 256)
    ");
    $reqlogin->bindValue(":emailaddresslogin", $_POST["emailadress"], PDO::PARAM_STR);
    $reqlogin->bindValue(":passwordlogin", $_POST["password"], PDO::PARAM_STR);
    $reqlogin->execute();

    if ($reqlogin->rowCount() > 0) {
        $login = $reqlogin->fetch();
        $_SESSION["login"] = $login;
        header("Location: home.php");
        exit;
    } else {
        // Mauvais identifiants : on nettoie la session
        $_SESSION = [];
        session_unset();
        session_destroy();
        $wrongpassword = $_POST["emailadress"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ShopTogether - Login</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <!-- Si tu souhaites le menu commun, décommente la ligne :
    <?php //include "_header.php"; ?>
    -->

    <main class="AuthPage">
        <form action="index.php" method="POST" class="AuthForm">
            <!-- Logo ou titre -->
            <div class="AuthForm__logo">
                <img src="Ressources/img/shoptogether.png" alt="ShopTogether" />
            </div>

            <!-- Email -->
            <label for="emailadress">Email</label>
            <input 
                type="email" 
                name="emailadress" 
                id="emailadress" 
                required 
                placeholder="Enter your email" 
                <?php if(isset($wrongpassword)){echo 'value="'.$wrongpassword.'"';} ?>/>
            <!-- Password -->
            <label for="password">Password</label>  
            <input 
                type="password" 
                name="password" 
                id="password" 
                required 
                placeholder="Enter your password" />

            <div class="AuthForm__actions">
                <button type="submit" class="btn">Sign In</button>
                <p><a href="signup.php">Sign up</a></p>
            </div>

            <p class="AuthForm__forgot">
                <a href="#">Forgot password?</a>
            </p>

            <!-- Message d'erreur en cas de mauvais identifiants -->
            <?php
            if (isset($_POST["emailadress"], $_POST["password"]) && !isset($_SESSION["login"])) {
                echo '<p class="errorMsg">Wrong Credentials... Try again</p>';
            }
            ?>
        </form>
    </main>
</body>
</html>
