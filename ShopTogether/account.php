<?php
session_start();
if (empty($_SESSION["login"])) {
    header("location:index.php");
    exit();
    session_unset();
    session_destroy();
} else {
    include "_connexionBD.php";
}
$userID      = (int) $_SESSION["login"]["ID_User"];
$userPseudo  = $_SESSION["login"]["Pseudo"];
$userEmail   = $_SESSION["login"]["emailAddress"];
$userSex     = (int) $_SESSION["login"]["ID_sex"];
$userCtry    = (int) $_SESSION["login"]["ID_Country"];
$userCurr    = (int) $_SESSION["login"]["ID_currency"];
$userPassDB  = $_SESSION["login"]["Password"];

$userImgPath = "Ressources/userImg/" . $userID . ".webp";

$message = "";

/*----------------------------------------
   1) TRAITEMENT DU FORMULAIRE DE COMPTE
-----------------------------------------*/
if (isset($_POST["updateAccount"])) {
    // 1.a) Récupérer / sécuriser les nouvelles données
    $newPseudo   = trim($_POST["Pseudo"]);
    $newEmail    = trim($_POST["emailAddress"]);
    $newSex      = (int) $_POST["ID_sex"];
    $newCountry  = (int) $_POST["ID_Country"];
    $newCurr     = (int) $_POST["ID_currency"];

    // 1.b) Gestion des mots de passe
    $oldPassword = $_POST["oldPassword"] ?? "";
    $newPassword = $_POST["password"] ?? "";
    $confirmPass = $_POST["passwordConfirm"] ?? "";
    $changePassword = false;

    // S'il y a un nouveau mot de passe souhaité...
    if (!empty($newPassword)) {
        if (!empty($oldPassword)) {
            $oldPassHash = hash("sha256", $oldPassword);
            if ($oldPassHash === $userPassDB) {
                if ($newPassword === $confirmPass) {
                    $newPasswordHash = hash("sha256", $newPassword);
                    $changePassword  = true;
                } else {
                    $message = "New passwords do not match.";
                }
            } else {
                $message = "Old password is incorrect.";
            }
        } else {
            $message = "You must provide your old password to set a new password.";
        }
    }

    // 1.c) Si pas d’erreur, on met à jour en base
    if (empty($message)) {
        try {
            if ($changePassword) {
                $reqUpdate = $bd->prepare("
                    UPDATE users
                    SET Pseudo       = :pseudo, 
                        emailAddress = :email, 
                        ID_sex       = :sex, 
                        ID_Country   = :ctry, 
                        ID_currency  = :curr,
                        Password     = :pass
                    WHERE ID_User = :id
                ");
                $reqUpdate->bindValue("pass", $newPasswordHash);
            } else {
                $reqUpdate = $bd->prepare("
                    UPDATE users
                    SET Pseudo       = :pseudo, 
                        emailAddress = :email, 
                        ID_sex       = :sex, 
                        ID_Country   = :ctry, 
                        ID_currency  = :curr
                    WHERE ID_User = :id
                ");
            }

            $reqUpdate->bindValue("pseudo", $newPseudo);
            $reqUpdate->bindValue("email",  $newEmail);
            $reqUpdate->bindValue("sex",    $newSex);
            $reqUpdate->bindValue("ctry",   $newCountry);
            $reqUpdate->bindValue("curr",   $newCurr);
            $reqUpdate->bindValue("id",     $userID);
            $reqUpdate->execute();

            // Mettre à jour la session
            $_SESSION["login"]["Pseudo"]       = $newPseudo;
            $_SESSION["login"]["emailAddress"] = $newEmail;
            $_SESSION["login"]["ID_sex"]       = $newSex;
            $_SESSION["login"]["ID_Country"]   = $newCountry;
            $_SESSION["login"]["ID_currency"]  = $newCurr;

            if ($changePassword) {
                $_SESSION["login"]["Password"] = $newPasswordHash;
            }
            $message = "Your account has been updated successfully.";
        } catch (Exception $e) {
            $message = "Error updating your account: " . $e->getMessage();
        }
    }

    // 1.d) Réactualiser les variables pour le formulaire
    $userPseudo  = $_SESSION["login"]["Pseudo"];
    $userEmail   = $_SESSION["login"]["emailAddress"];
    $userSex     = (int) $_SESSION["login"]["ID_sex"];
    $userCtry    = (int) $_SESSION["login"]["ID_Country"];
    $userCurr    = (int) $_SESSION["login"]["ID_currency"];
    $userPassDB  = $_SESSION["login"]["Password"];
}

/*------------------------------------------
   2) TRAITEMENT DU UPLOAD DE L'IMAGE
-------------------------------------------*/
if (isset($_POST["uploadImage"]) && isset($_FILES["profileImage"])) {
    $file = $_FILES["profileImage"];
    if ($file["error"] === UPLOAD_ERR_OK) {
        $tmpName = $file["tmp_name"];
        $imgInfo = getimagesize($tmpName);

        if ($imgInfo) {
            // Création de l'image source à partir du fichier uploadé
            $src = imagecreatefromstring(file_get_contents($tmpName));
            if ($src) {
                // Récupère la largeur et hauteur de l'image
                $origWidth  = imagesx($src);
                $origHeight = imagesy($src);

                // Déterminer la plus petite dimension
                // pour créer un carré (minDim x minDim).
                $minDim = min($origWidth, $origHeight);

                // Calcul pour rogner au centre
                $x = (int)(($origWidth  - $minDim) / 2);
                $y = (int)(($origHeight - $minDim) / 2);

                // Créer une image carrée rognée
                $cropImg = imagecreatetruecolor($minDim, $minDim);

                // Copie la zone centrale de l'image source dans cropImg
                imagecopy($cropImg, $src, 0, 0, $x, $y, $minDim, $minDim);

                // Maintenant, on réduit ce carré à 100×100
                $resized = imagescale($cropImg, 100, 100, IMG_BICUBIC);

                if ($resized) {
                    // Sauvegarde en WebP, qualité ~80
                    imagewebp($resized, $userImgPath, 80);
                    imagedestroy($resized);
                    imagedestroy($cropImg);
                    $message = "Profile image updated successfully.";
                } else {
                    $message = "Failed to resize the image.";
                }
                imagedestroy($src);
            } else {
                $message = "Invalid image content.";
            }
        } else {
            $message = "Uploaded file is not a valid image.";
        }
    } else {
        $message = "Failed to upload image (error code: {$file["error"]}).";
    }
}
$sexes = $bd->prepare("SELECT ID_sex, name FROM sex");
$sexes->execute();
$allSexes = $sexes->fetchAll(PDO::FETCH_ASSOC);

$countries = $bd->prepare("SELECT ID, NAME FROM countries");
$countries->execute();
$allCountries = $countries->fetchAll(PDO::FETCH_ASSOC);

$currencies = $bd->prepare("SELECT ID_currency, name, code, symbol FROM currency");
$currencies->execute();
$allCurr = $currencies->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ShopTogether - Account</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php include "_header.php"; ?>

<main class="AccountPage">
    <h2>My Account</h2>
    <?php
    if (!empty($message)) {
        echo '<div class="AccountMessage">'.htmlspecialchars($message).'</div>';
    }
    ?>
    <div class="AccountImage">
        <?php
        if (file_exists($userImgPath)) {
            echo '<img src="'.$userImgPath.'" alt="Profile Image">';
        } else {
            echo '<img src="Ressources/userImg/default.webp" alt="Profile Image">';
        }
        ?>
    </div>
    
    <form action="account.php" method="POST" enctype="multipart/form-data" class="ImageUploadForm">
        <label for="profileImage">Upload Profile Image</label>
        <input type="file" name="profileImage" id="profileImage" accept="image/*"/>
        <button type="submit" name="uploadImage" class="UploadImageBtn">Upload Image</button>
    </form>
    <form action="account.php" method="POST" class="AccountForm">
        <label for="Pseudo">Pseudo</label>
        <input type="text" name="Pseudo" id="Pseudo" required 
               value="<?php echo htmlspecialchars($userPseudo); ?>"/>
        
        <label for="emailAddress">Email</label>
        <input type="email" name="emailAddress" id="emailAddress" required 
               value="<?php echo htmlspecialchars($userEmail); ?>"/>
        
        <label for="ID_sex">Sex</label>
        <select name="ID_sex" id="ID_sex" required>
            <?php
            foreach ($allSexes as $s) {
                $selected = ($s["ID_sex"] == $userSex) ? 'selected' : '';
                echo '<option value="'.$s["ID_sex"].'" '.$selected.'>'
                     .htmlspecialchars($s["name"]).
                     '</option>';
            }
            ?>
        </select>
        
        <label for="ID_Country">Country</label>
        <select name="ID_Country" id="ID_Country" required>
            <?php
            foreach ($allCountries as $c) {
                $selected = ($c["ID"] == $userCtry) ? 'selected' : '';
                echo '<option value="'.$c["ID"].'" '.$selected.'>'
                     .htmlspecialchars($c["NAME"]).
                     '</option>';
            }
            ?>
        </select>
        
        <label for="ID_currency">Currency</label>
        <select name="ID_currency" id="ID_currency" required>
            <?php
            foreach ($allCurr as $cur) {
                $selected = ($cur["ID_currency"] == $userCurr) ? 'selected' : '';
                echo '<option value="'.$cur["ID_currency"].'" '.$selected.'>'
                     .htmlspecialchars($cur["name"]).' ('.htmlspecialchars($cur["symbol"]).')'
                     .'</option>';
            }
            ?>
        </select>
        
        <hr/>
        
        <label for="oldPassword">Old Password</label>
        <input type="password" name="oldPassword" id="oldPassword" 
               placeholder="Enter old password to set a new one"/>

        <label for="password">New Password</label>
        <input type="password" name="password" id="password" 
               placeholder="Leave blank to keep current password"/>
        
        <label for="passwordConfirm">Confirm New Password</label>
        <input type="password" name="passwordConfirm" id="passwordConfirm" 
               placeholder="Repeat your new password"/>
        
        <hr/>
        
        <button type="submit" name="updateAccount" class="UpdateAccountBtn">
            Save changes
        </button>
    </form>
    <div class="LogoutZone">
        <a href="index.php" class="LogoutBtn">Logout</a>
    </div>
</main>
</body>
</html>
