<?php
session_start();
if (empty($_SESSION["login"])) {
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset();
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
    # Récupération de la monnaie préférée de l'utilisateur
    $reqcurrency = $bd->prepare('SELECT * FROM currency as c WHERE ID_currency=:id');
    $reqcurrency->bindvalue('id',$_SESSION['login']['ID_currency']);
    $reqcurrency->execute();
    $currencyselected = $reqcurrency->fetch();
    $currencysymbol = $currencyselected['symbol'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ShopTogether</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <?php include "_header.php"; ?>
    <main id="ArchivedListMain">
        <div id="InformationsFromArchivedList">
            <h2><?php echo $list['Name'] ?></h2>
            <p>Created : <?php echo $list['DateCreation'] ?></p>
            <p>Shopped :<?php echo $list['DateValidation'] ?></p>
        </div>
        <div id="ProductsFromArchivedList">
            <?php
            $reqProdList = $bd->prepare('SELECT p.ProductName, ps.note, ps.Bought, ps.Price  FROM products_shopping as ps JOIN products as p ON p.ID_product=ps.ID_product WHERE ps.ID_shoppingList=:list');
            $reqProdList->bindvalue('list',$idshop);
            $reqProdList->execute();
            while ($prod = $reqProdList->fetch()){
                if($prod['Bought']==1){
                    $classarch='bought';
                } else $classarch ='notbought';
                echo '<div class="ArchivedProduct '.$classarch.'" >';
                echo '<p>'.$prod['ProductName'].'</p>';
                if(!empty($prod['note'])){echo '<p>'.$prod['note'].'</p>';} 
                if(!empty($prod['Price'])){echo '<p>'.$prod['Price'].' '.$currencysymbol.'</p>';} 
                echo '</div>';
            }
            ?>
        </div>
    </main>
</body>
</html>
