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
       <a href="new_product.php">Add Products</a> 
       <div id="listesproduits">
            <?php
            $reqproductsget = $bd->prepare("SELECT p.ID_product,p.ProductName,p.ID_user, GROUP_CONCAT(c.CategoryName) AS categoriesconcate FROM product_categories as pc JOIN products AS p ON pc.ID_Product=p.ID_product JOIN categories as c ON c.ID_Category=pc.ID_Category GROUP BY p.ID_product HAVING p.ID_user=0 OR p.ID_user=:userlogin;");
            $reqproductsget->bindvalue("userlogin",$_SESSION["login"]['ID_User']);
            $reqproductsget->execute();

            $productsgot = $reqproductsget->fetchAll(PDO::FETCH_ASSOC);
            foreach($productsgot as $product){
                $product['categoriesconcate'] = str_replace(' ','_',$product['categoriesconcate']);
                $categories = explode(',',$product['categoriesconcate']);

                echo "<div>";
                echo "<h3>". $product['ProductName'] . "</h3>";
                echo "<div id='descproduct'>";
                foreach($categories as $cat){
                    echo "<p> $cat";
                    echo "<img src='Ressources/img/".$cat.".webp' alt='categoriesimage'";
                }
                echo "</div>";
                echo "</div>";
            }
            ?>
       </div>
    </main>
</body>
</html>