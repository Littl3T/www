<?php
session_start();
if(empty($_SESSION["login"])){
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset(); 
    session_destroy();
} else {
    include "_connexionBD.php";
}

/* ---------------------------
   1) SUPPRESSION DU PRODUIT :
   On met StateUp=0 si le produit
   appartient à l'utilisateur.
---------------------------- */
if (isset($_POST["delete_product"]) && !empty($_POST["delete_product"])) {
    $idProdToDelete = (int) $_POST["delete_product"];
    
    // Vérifier que ce produit appartient à l'user en session, et qu'il est actif (StateUp=1)
    $checkOwner = $bd->prepare("
        SELECT p.ID_product
        FROM products AS p
        WHERE p.ID_product = :idProd
          AND p.ID_user = :idUser
          AND p.StateUp = 1
    ");
    $checkOwner->bindValue("idProd", $idProdToDelete, PDO::PARAM_INT);
    $checkOwner->bindValue("idUser", (int)$_SESSION["login"]["ID_User"], PDO::PARAM_INT);
    $checkOwner->execute();
    $ownerRes = $checkOwner->fetch(PDO::FETCH_ASSOC);
    
    // S'il existe, on fait l'UPDATE => StateUp=0
    if (!empty($ownerRes["ID_product"])) {
        $updateProd = $bd->prepare("
            UPDATE products
            SET StateUp = 0
            WHERE ID_product = :idProd
        ");
        $updateProd->bindValue("idProd", $idProdToDelete, PDO::PARAM_INT);
        $updateProd->execute();
        
        // On redirige pour éviter la resoumission du form
        header("Location: products.php");
        exit();
    }
}

/* ---------------------------
   2) RÉCUPERATION DES PRODUITS :
   - Actifs (StateUp=1)
   - Soit user=0 (produits génériques), 
     soit user=<ID de l’utilisateur en session>.
   - On GROUP_CONCAT les catégories dans un champ
     qu'on va ensuite retravailler (remplacer les espaces, etc.).
---------------------------- */
$reqproductsget = $bd->prepare("
    SELECT 
        p.ID_product,
        p.ProductName,
        p.ID_user,
        GROUP_CONCAT(c.CategoryName SEPARATOR ',') AS categoriesconcate
    FROM products AS p
    JOIN product_categories AS pc ON pc.ID_Product = p.ID_product
    JOIN categories AS c         ON c.ID_Category = pc.ID_Category
    WHERE p.StateUp = 1
      AND (p.ID_user = 0 OR p.ID_user = :userlogin)
    GROUP BY p.ID_product
    ORDER BY p.ID_user DESC, p.ProductName ASC
");
$reqproductsget->bindValue("userlogin", $_SESSION["login"]['ID_User'], PDO::PARAM_INT);
$reqproductsget->execute();
$productsgot = $reqproductsget->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopTogether - Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "_header.php"; ?>
    
    <!-- MAIN SPÉCIFIQUE À LA PAGE DES PRODUITS -->
    <main class="ProductsPage">
        <!-- Lien vers la page d’ajout d’un nouveau produit -->
        <a href="new_product.php" id="addNewProductBtn">Add Product</a>
        
        <div id="productsGallery">
        <?php
            foreach ($productsgot as $product) {
                // Transformer la liste de catégories en un tableau
                // et remplacer les espaces par des underscores
                // (comme dans ton code initial).
                $product['categoriesconcate'] = str_replace(' ', '_', $product['categoriesconcate']);
                $categories = explode(',', $product['categoriesconcate']);
                
                // On va afficher chaque produit dans un <form> 
                // (pour pouvoir gérer le bouton de suppression).
                echo '<form method="POST" class="product_card">';
                
                // -- Bouton poubelle si l'user en session est proprio
                if ((int)$product['ID_user'] === (int)$_SESSION["login"]["ID_User"]) {
                    echo '<button type="submit" name="deleteBtn" class="trashBtn" title="Delete this product">';
                    echo '<img class="trash" src="Ressources/img/Trash.png" alt="trash icon">';
                    echo '</button>';
                    echo '<input type="hidden" name="delete_product" value="'.$product["ID_product"].'">';
                }
                
                // -- Nom du produit
                echo '<h3>'.htmlspecialchars($product['ProductName']).'</h3>';
                
                // -- Les catégories
                echo '<div class="categories_for_product">';
                foreach ($categories as $cat) {
                    $catSafe = htmlspecialchars(trim($cat));
                    echo '<span class="single_cat">';
                    echo $catSafe . ' ';
                    // On suppose l'image s’appelle 'NomCategorie.webp'
                    echo '<img src="Ressources/img/'.$catSafe.'.webp" alt="cat image" />';
                    echo '</span>';
                }
                echo '</div>';
                
                echo '</form>';
            }
        ?>
        </div>
    </main>
</body>
</html>
