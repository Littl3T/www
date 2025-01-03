<?php
session_start();
if(empty($_SESSION["login"])){
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset(); // Supprime toutes les variables de session
    session_destroy();
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
<body>
    <?php
    include "_header.php"
    ?>
    <main>
        <p>
            Dear users,

            Welcome to our ShoppingTogether platform. <b><?php echo $_SESSION['login']['Pseudo']; ?></b>

            The founders.
        </p>
    </main>
</body>
</html>