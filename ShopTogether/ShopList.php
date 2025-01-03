<?php
session_start();
if(empty($_SESSION["login"])){
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset(); // Supprime toutes les variables de session
    session_destroy();
} else {
    include "_connexionBD.php";
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

        if(isset($_POST['products'])){
            # Pour tous les produits qui sont coché de la liste, ils sont en index de la $ListProductsChecked et leur valeur est le prix. si pas de prix par user==> NULL
            $ListProductsChecked = array();
            foreach($_POST['products'] as $c){
                $prodId = (int) $c;
                $clef = 'Price'.$c;
                if(!empty($_POST[$clef])){
                    $price = (float) $_POST[$clef];
                } else {$price='NULL';}
                $ListProductsChecked[$c] = $price;
            }
            print_r($ListProductsChecked);
            # Une fois tous les éléments définis=> Modification du prix et "bought" dans la table pour eux.
            $reqModPriceAndBought = $bd->prepare('UPDATE products_shopping AS ps SET ps.Price=:price, ps.Bought=1 WHERE ps.ID_ProductsShopping=:prod');
            foreach($ListProductsChecked as $key=>$val){
                if($val=='NULL'){
                    $reqModPriceAndBought->bindvalue('price',NULL);
                } else $reqModPriceAndBought->bindvalue('price',$val);
                $reqModPriceAndBought->bindvalue('prod',$key);
                $reqModPriceAndBought->execute();
            }
            # Validation de la liste même.
            $reqValidationList = $bd->prepare('UPDATE shoppinglist AS s SET s.DateValidation=:validationdate WHERE s.ID_shoppingList=:list');
            $reqValidationList->bindvalue('validationdate',date("Y/m/d"));
            $reqValidationList->bindvalue('list',$idshop);
            $reqValidationList->execute();
            # Redirection vers la page des listes actualisées. La liste validée sera visible dans l'historique
            header("location:shoppinglist.php");
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
    include "_header.php"
    ?>
    <main class="ShopListPage">
        <form action="ShopList.php" method="POST" id="ShopListForm">
        <input type="hidden" name="ID_shoppingList" value="<?php echo $idshop; ?>" />
        <?php
            $reqProdList = $bd->prepare('SELECT p.ProductName, ps.ID_ProductsShopping, ps.note FROM products_shopping as ps JOIN products as p ON p.ID_product=ps.ID_product WHERE ps.ID_shoppingList=:list');
            $reqProdList->bindvalue('list',$idshop);
            $reqProdList->execute();
            while ($prod = $reqProdList->fetch()){
                $prodID   = $prod['ID_ProductsShopping'];
                $prodName = htmlspecialchars($prod['ProductName']);
                if(isset($prod['note'])){
                   $prodNote = htmlspecialchars($prod['note']); 
                } else $prodNote = '';
                echo '<div class="ListCheckBoxDiv">';
                echo '  <label class="CheckItem">';
                echo '    <input type="checkbox" name="products[]" value="' . $prodID . '" />';
                echo '    <span class="ProductName">' . $prodName . '</span>';
                if (!empty($prodNote)) {
                    echo '    <span class="ProductNote">' . $prodNote . '</span>';
                }
                echo '    <input type="number" step="0.01" min="0" name="Price' . $prodID . '" class="PriceInput" placeholder="Price" />';
                echo '  </label>';
                echo '</div>';
            }
            ?>
            <button type="submit" class="DoneButton">DONE</button>
        </form>
    </main>
</body>
</html>

