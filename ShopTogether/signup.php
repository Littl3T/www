<?php
session_start(); 
include "_connexionBD.php"; 

$errorMessage = ""; 
$pseudo = isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : '';
$sex = isset($_POST['sex']) ? (int)$_POST['sex'] : '';
$country = isset($_POST['country']) ? (int)$_POST['country'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmpassword = isset($_POST['confirmpassword']) ? $_POST['confirmpassword'] : '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST["emailadress"]) ? htmlspecialchars($_POST["emailadress"]) : '';
    
    // Vérifier si l'email existe déjà
    $req_checkEmail = $bd->prepare("SELECT COUNT(*) FROM users WHERE emailAddress = :email");
    $req_checkEmail->bindValue(":email", $email);
    $req_checkEmail->execute();
    $emailExists = $req_checkEmail->fetchColumn() > 0;

    if ($emailExists) {
        $errorMessage = "This email address is already registered.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email address format.";
    } elseif ($password !== $confirmpassword) {
        $errorMessage = "Passwords do not match.";
    } else {
        $req_newUser = $bd->prepare("INSERT INTO `users` (`Pseudo`, `Password`, `emailAddress`, `ID_sex`, `ID_Country`) 
                                     VALUES (:userpseudo, SHA2(:userpassword,256), :useremail, :usersex, :usercountry)");
        $req_newUser->bindValue("userpseudo", $pseudo);
        $req_newUser->bindValue(":userpassword", $password);
        $req_newUser->bindValue("useremail", $email);
        $req_newUser->bindValue("usersex", $sex);
        $req_newUser->bindValue("usercountry", $country);
        $req_newUser->execute();
        header("location:index.php");
        exit();
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
            
            <?php if (!empty($errorMessage)) : ?>
                <p id="infos" class="error"><?php echo $errorMessage; ?></p>
            <?php endif; ?>

            <label for="pseudo">Pseudo</label>
            <input type="text" name="pseudo" value="<?php echo $pseudo; ?>" required>

            <label for="sex">Gender</label>
            <select name="sex" id="sex">
                <?php
                $reqGenders = $bd->prepare("SELECT * FROM sex");
                $reqGenders->execute();
                $genders = $reqGenders->fetchAll(PDO::FETCH_ASSOC);
                foreach ($genders as $gender) {
                    $selected = $gender['id'] == $sex ? "selected" : "";
                    echo "<option value='{$gender['id']}' $selected>{$gender['name']}</option>";
                }
                ?>
            </select>
            
            <label for="country">Country</label>
            <select name="country" id="country">
                <?php
                $reqcountries = $bd->prepare("SELECT * FROM countries");
                $reqcountries->execute();
                $countries = $reqcountries->fetchAll(PDO::FETCH_ASSOC);
                foreach ($countries as $countryOption) {
                    $selected = $countryOption['ID'] == $country ? "selected" : "";
                    echo "<option value='{$countryOption['ID']}' $selected>{$countryOption['NAME']}</option>";
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
        </form>
    </main>
</body>
</html>
