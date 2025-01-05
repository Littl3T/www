<?php
session_start(); 
include "_connexionBD.php";

$errorMessage = ""; 
$pseudo   = isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : '';
$sex      = isset($_POST['sex']) ? (int)$_POST['sex'] : '';
$country  = isset($_POST['country']) ? (int)$_POST['country'] : '';
$currency = isset($_POST['currency']) ? (int)$_POST['currency'] : '';
$password = $_POST['password'] ?? '';
$confirmpassword = $_POST['confirmpassword'] ?? '';

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
        $req_newUser = $bd->prepare("
            INSERT INTO users (Pseudo, Password, emailAddress, ID_sex, ID_Country, ID_currency) 
            VALUES (:userpseudo, SHA2(:userpassword,256), :useremail, :usersex, :usercountry, :currency)
        ");
        $req_newUser->bindValue("userpseudo", $pseudo);
        $req_newUser->bindValue("userpassword", $password);
        $req_newUser->bindValue("useremail", $email);
        $req_newUser->bindValue("usersex", $sex);
        $req_newUser->bindValue("usercountry", $country);
        $req_newUser->bindValue("currency", $currency);
        $req_newUser->execute();
        header("location:index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ShopTogether - Sign Up</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <!-- Idem : tu peux inclure le header si tu veux un menu global
    <?php //include "_header.php"; ?>
    -->

    <main class="AuthPage">
        <form action="signup.php" method="POST" class="AuthForm">
            <div class="AuthForm__logo">
                <a href="index.php">
                    <img src="Ressources/img/shoptogether.png" alt="ShopTogether"/>
                </a>
            </div>

            <!-- Affiche une éventuelle erreur -->
            <?php if (!empty($errorMessage)) : ?>
                <p class="errorMsg"><?php echo $errorMessage; ?></p>
            <?php endif; ?>

            <label for="pseudo">Pseudo</label>
            <input type="text" name="pseudo" value="<?php echo $pseudo; ?>" required />

            <label for="sex">Gender</label>
            <select name="sex" id="sex">
                <?php
                $reqGenders = $bd->prepare("SELECT * FROM sex");
                $reqGenders->execute();
                foreach ($reqGenders->fetchAll(PDO::FETCH_ASSOC) as $gender) {
                    $selected = ($gender['id'] == $sex) ? 'selected' : '';
                    echo "<option value='{$gender['id']}' $selected>{$gender['name']}</option>";
                }
                ?>
            </select>

            <label for="country">Country</label>
            <select name="country" id="country">
                <?php
                $reqcountries = $bd->query("SELECT * FROM countries");
                foreach ($reqcountries->fetchAll(PDO::FETCH_ASSOC) as $countryOption) {
                    $selected = ($countryOption['ID'] == $country) ? 'selected' : '';
                    echo "<option value='{$countryOption['ID']}' $selected>{$countryOption['NAME']}</option>";
                }
                ?>
            </select>

            <label for="currency">Currency</label>
            <select name="currency" id="currency">
                <?php
                $reqcurrencies = $bd->query("SELECT * FROM currency ORDER BY name");
                foreach ($reqcurrencies->fetchAll(PDO::FETCH_ASSOC) as $currenciesOption) {
                    $selected = ($currenciesOption['ID_currency'] == $currency) ? 'selected' : '';
                    echo "<option value='{$currenciesOption['ID_currency']}' $selected>
                            {$currenciesOption['name']} {$currenciesOption['code']}{$currenciesOption['symbol']}
                          </option>";
                }
                ?>
            </select>

            <label for="emailadress">Email</label>
            <input type="email" name="emailadress" id="emailadress" required />

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required />

            <label for="confirmpassword">Confirm password</label>
            <input type="password" name="confirmpassword" id="confirmpassword" required />

            <div class="AuthForm__actions">
                <button type="submit" class="btn">Sign Up</button>
            </div>
        </form>
    </main>
</body>
</html>
