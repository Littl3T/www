<?php
session_start();
if(empty($_SESSION["login"])){
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset(); // Supprime toutes les variables de session
    session_destroy();
} else include "_connexionBD.php";

if(isset($_GET['ID_shoppingList'])){
    $id = $_GET['ID_shoppingList'];
} else $id = "nop";

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
        <div>
            <a href=""></a>
        </div>
        <p>
            <?php
            echo $id;
            ?>
        </p>
    </main>
</body>
</html>