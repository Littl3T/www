<?php
session_start(); // Démarre la session existante ou en crée une nouvelle
include "_connexionBD.php"; // Connexion à la base de données

if(isset($_POST["password"]) and isset($_POST["confirmpassword"])){
    if($_POST["password"] === $_POST["confirmpassword"]){
        $req_newUser = $bd->prepare("INSERT INTO `users` (`ID_User`, `Pseudo`, `Password`, `emailAddress`, `ID_sex`, `ID_Country`) VALUES (NULL, :userpseudo, SHA2(:userpassword,256), :useremail, :usersex, :usercountry);");
        $req_newUser->bindvalue("userpseudo", $_POST["pseudo"]);
        $req_newUser->bindvalue(":userpassword", $_POST["password"]);
        $req_newUser->bindvalue("useremail", $_POST["emailadress"]);
        $req_newUser->bindvalue("usersex", (int)$_POST["sex"]);
        $req_newUser->bindValue("usercountry", (int) $_POST["country"]);
        $req_newUser->execute();
        header("location:index.php");
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
        <form action="signup.php" method="POST" class="LoginForm">
            <h1>
                <a href="index.php" class="title">ShopTogether</a>
            </h1>
            <label for="pseudo">Pseudo</label>
            <input type="text" name="pseudo" required>

            <label for="sex">Gender</label>
            <select name="sex" id="sex">
                <?php
                $reqGenders = $bd->prepare("SELECT * FROM sex");
                $reqGenders->execute();
                $genders = $reqGenders->fetchAll(PDO::FETCH_ASSOC);
                foreach ($genders as $gender) {
                    echo "<option value='{$gender['id']}'>{$gender['name']}</option>";
                }
                ?>
            </select>
            
            <label for="country">Country</label>
            <select name="country" id="country">
                <?php
                $reqcountries = $bd->prepare("SELECT * FROM countries");
                $reqcountries->execute();
                $countries = $reqcountries->fetchAll(PDO::FETCH_ASSOC);
                foreach ($countries as $countries) {
                    echo "<option value='{$countries['ID']}'>{$countries['NAME']}</option>";
                }
                ?>
            </select>

            <label for="emailadress">Email</label>
            <input type="email" name="emailadress" required>

            <label for="password">Password</label>  
            <input type="password" name="password" required>

            <label for="confirmpassword">Confirm password</label>  
            <input type="password" name="confirmpassword" required>

            <div>
                <input type="submit" value="Sign Up" class="buttonsignin">
            </div>
            <?php
            if(isset($_POST["password"]) and isset($_POST["confirmpassword"])){
                if($_POST["password"] === $_POST["confirmpassword"]){
                } else echo "<p id='infos'>Paswords don't match... Try again</p>";
            }
            ?>
        </form>
    </main>
</body>
</html>
