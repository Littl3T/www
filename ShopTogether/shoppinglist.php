<?php
session_start();
if(empty($_SESSION["login"])){
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset(); // Supprime toutes les variables de session
    session_destroy();
} else include "_connexionBD.php"
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
        <a href="new_list.php">New ShoppingList</a>
        <div id="shoppingListGallery">
            <?php
            $reqShoppingList = $bd->prepare("SELECT s.ID_shoppingList,s.Name,s.DateCreation,s.DateValidation FROM user_shopping as us JOIN shoppinglist as s ON s.ID_shoppingList= us.ID_shopping WHERE us.ID_user=:usr ORDER BY s.DateCreation;");
            $reqShoppingList->bindvalue('usr',(int) $_SESSION["login"]["ID_User"]);
            $reqShoppingList->execute();
            while ( $list=$reqShoppingList->fetch() ) {
                echo "<div>";
                echo "<a href='List.php?ID_shoppingList=".$list['ID_shoppingList']."'>";
                echo "<h3>".$list['Name']."</h3>";
                echo "<p> Created :".$list['DateCreation']."</p>";
                echo "<p> Validated :".$list['DateValidation']."</p>";
                echo "</a>";
                echo "</div>";
               }
            ?>
        </div>
    </main>
</body>
</html>