<?php
session_start();
if(empty($_SESSION["login"])){
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset(); // Supprime toutes les variables de session
    session_destroy();
} else include "_connexionBD.php";

if(isset($_GET['ID_shoppingList']) or isset($_POST['ID_shoppingList'])){
    if(isset($_GET['ID_shoppingList'])){
        $idshop =(int) $_GET['ID_shoppingList'];
    } else $idshop =(int) $_POST['ID_shoppingList'];
    # Vérification pour savoir' si le user a bien le droit sur l'utilsiation de la liste. Dans la table de partage jointure des users et list
    $requserlist = $bd->prepare('SELECT p.ID_user, p.ID_shopping,p.ID_UserShopping FROM user_shopping AS p WHERE p.ID_user=:usr AND p.ID_shopping=:shop');
    $requserlist->bindvalue('usr',(int) $_SESSION["login"]["ID_User"]);
    $requserlist->bindvalue('shop',$idshop);
    $requserlist->execute();
    $check = $requserlist->fetch();
    if(empty($check['ID_UserShopping'])){
        header("location:shoppinglist.php");
    } else{
        # Récupération des informations de la liste avec nom et id vérifié dans la bd
        $reqNameList=$bd->prepare('SELECT l.ID_shoppingList,l.DateCreation,l.DateValidation,l.Name FROM shoppinglist as l WHERE l.ID_shoppingList=:list');
        $reqNameList->bindvalue('list',$idshop);
        $reqNameList->execute();
        $list = $reqNameList->fetch(); 
    }
} else header("location:shoppinglist.php");

if(isset($_GET['categoryfilter'])){
    $IDcategory = (int) $_GET['categoryfilter'];
}
if(isset($_POST['ID_shoppingList']) and isset($_POST['note']) and isset($_POST['productselected']) and isset($_SESSION["login"]["ID_User"])){
    # Table products_shopping : ID_productsShopping(PK) | IDproduct (FK) | ID_shoppingList (FK) | note | GradeOverTen | Price 
    if(!empty($_POST['note'])){
        $note = htmlspecialchars($_POST['note']);
        $note = trim($note);
    } else $note = null;
    $product = (int) $_POST['productselected'];
    $reqaddproduct = $bd->prepare('INSERT INTO products_shopping(ID_productsShopping,ID_product,ID_shoppingList,Note,GradeOverTen,Price) VALUES(NULL,:product,:list,:note,NULL,NULL)');
    $reqaddproduct->bindValue('product',$product);
    $reqaddproduct->bindValue('list',$idshop);
    $reqaddproduct->bindValue('note',$note);
    $reqaddproduct->execute();
    header("location:List.php?ID_shoppingList=".$idshop);
}
if(isset($_POST['DeletePorudct'])){
    # Vérification de la présence du produit dans la table. Si présent alors suppression basé sur les formulaire de la liste
    $deletedproduct = (int) $_POST['DeletePorudct'];
    $reqcheckproductvalidity = $bd->prepare('SELECT ps.ID_ProductsShopping FROM products_shopping AS ps WHERE ID_ProductsShopping=:prod AND ID_shoppingList=:list');
    $reqcheckproductvalidity->bindvalue('prod',$deletedproduct);
    $reqcheckproductvalidity->bindvalue('list',$idshop);
    $reqcheckproductvalidity->execute();
    $reqreturnvalidity = $reqcheckproductvalidity->fetch();
    if(!empty($reqreturnvalidity['ID_ProductsShopping'])){
        $reqDeleteProduct = $bd->prepare('DELETE FROM products_shopping WHERE ID_productsShopping=:delid');
        $reqDeleteProduct->bindvalue('delid',$deletedproduct);
        $reqDeleteProduct->execute();
        header("location:List.php?ID_shoppingList=".$idshop);
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
<body>
    <?php
    include "_header.php";
    ?>
    <main>
        <div id="listTitle">
            <?php
            echo '<h2>'.$list['Name'].'</h2>';
            $dateSQL = $list['DateCreation'];        // "2025-01-01"
            $dateObj = new DateTime($dateSQL);
            echo '<p>Created: '.$dateObj->format('d F Y').'</p>';   // 01 January 2025
            ?>    
        </div>
        <div id="listElements">
            <?php
                $reqProdList = $bd->prepare('SELECT p.ProductName, ps.ID_ProductsShopping, ps.note FROM products_shopping as ps JOIN products as p ON p.ID_product=ps.ID_product WHERE ps.ID_shoppingList=:list');
                $reqProdList->bindvalue('list',$idshop);
                $reqProdList->execute();
                while ($prod = $reqProdList->fetch()){
                    echo '<form action="List.php" method="POST">';
                    echo '<input type="hidden" name="ID_shoppingList" value="'.$idshop.'">';
                    echo '<input type="hidden" name="DeletePorudct" value="'.$prod['ID_ProductsShopping'].'">';
                    echo '<div id="product_informations">';
                    echo '<p>'.$prod['ProductName'].'</p>';
                    echo '<p>'.$prod['note'].'</p>';   
                    echo '</div>';
                    echo '<button type="submit">';
                    echo '<img src="Ressources/img/Trash.png" alt="Delete Trash product" width="40" height="40">';
                    echo '</button>';
                    echo '</form>';
                }
            ?>
        </div>
        <div id="AjoutProduit">
            <form action="List.php" method="GET">
                <!-- On remet en GET l'ID pour ne pas le perdre -->
                <input type="hidden" name="ID_shoppingList" value="<?php echo $idshop; ?>" />
                <label for="categoryfilter">Category</label>
                <select name="categoryfilter" id="categoryfilter">
                    <option value="0">No filter selected</option>
                    <?php
                    $reqcat = $bd->prepare('SELECT c.ID_Category, c.CategoryName FROM categories AS c'); 
                    $reqcat->execute();
                    while($category =$reqcat->fetch()){
                        if(isset($IDcategory)){
                            if($category['ID_Category'] == $IDcategory){$x='selected';} else $x='';
                        } else $x='';
                        echo '<option value="'.$category['ID_Category'].'" '.$x.'>'.$category['CategoryName'].'</option>';
                    }
                    ?>
                </select>
                <button type="submit">Filter</button>
            </form>
            <form action="List.php" method="POST">
                <!-- On remet en GET l'ID pour ne pas le perdre -->
                <input type="hidden" name="ID_shoppingList" value="<?php echo $idshop; ?>" />
                <label for="productselected">Product</label>
                <select name="productselected" id="productselected">
                    <?php
                    if(isset($IDcategory) and $IDcategory !=0){
                        $reqproducts = $bd->prepare('SELECT DISTINCT p.ID_product,p.ProductName FROM product_categories as pc JOIN products as p ON p.ID_product=pc.ID_Product WHERE pc.ID_Category=:cat and p.StateUp=1 and p.ID_user=:usr ;'); 
                        $reqproducts->bindvalue('cat',(int)$IDcategory);
                        $reqproducts->bindvalue('usr',(int)$_SESSION["login"]["ID_User"]);
                        $reqproducts->execute();
                        while($product =$reqproducts->fetch()){
                                echo '<option value="'.$product['ID_product'].'">'.$product['ProductName'].'</option>';
                        }
                    } else {
                        $reqproducts = $bd->prepare('SELECT DISTINCT p.ID_product,p.ProductName FROM products AS p WHERE p.StateUp=1 and p.ID_user=:usr or p.ID_user=0'); 
                        $reqproducts->bindvalue('usr',(int)$_SESSION["login"]["ID_User"]);
                        $reqproducts->execute();
                        while($product =$reqproducts->fetch()){
                                echo '<option value="'.$product['ID_product'].'">'.$product['ProductName'].'</option>';
                        }
                    }
                    ?>
                </select>
                <input type="text" name="note" id="note" placeholder="Note">
                <button type="submit">Add</button>
            </form>   
        </div>
    </main>
</body>
</html>