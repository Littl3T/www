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
        $note = $_POST['note'];
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
if(isset($_POST['sharedemail']) and !empty($_POST['sharedemail'])){
    # Vérification de la présence d'un email partagé dans le post.
    # Vérification de la présance de l'email dans la table user
    $FriendEmail= htmlspecialchars($_POST['sharedemail']);
    $reqCheckFriendUser = $bd->prepare('SELECT u.Pseudo,u.ID_user FROM users AS u WHERE u.emailAddress=:friend');
    $reqCheckFriendUser->bindvalue('friend',$FriendEmail);
    $reqCheckFriendUser->execute();
    $friend = $reqCheckFriendUser->fetch();
    if(!empty($friend['Pseudo'])){
        # Vérification que l'utilisateur partagé n'est pas déjà partagé
        $reqCheckFriendP = $bd->prepare('SELECT us.ID_UserShopping FROM user_shopping AS us WHERE ID_user=:usr AND ID_shopping=:list');
        $reqCheckFriendP->bindvalue('usr',(int)$friend['ID_user']);
        $reqCheckFriendP->bindvalue('list',$idshop);
        $reqCheckFriendP->execute();
        $checkedfriend = $reqCheckFriendP->fetch();
        if(empty($checkedfriend['ID_UserShopping'])){
            # Partage à l'utilisateur de la liste
            $reqAddFriend = $bd->prepare('INSERT INTO user_shopping(ID_UserShopping,ID_user,ID_shopping) VALUES(NULL,:usr,:list)');
            $reqAddFriend->bindvalue('usr',(int)$friend['ID_user']);
            $reqAddFriend->bindvalue('list',$idshop);
            $reqAddFriend->execute();
            $f = $friend['Pseudo'].' succefully shared to your list';
        } else $f ='This list is already shared to '.$friend['Pseudo'];
    } else $f = 'This email address has no ShopTogether account linked';
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
    <main class="ListPage">
        <header id="List">
            <div id="listTitle">
                <?php
                echo '<h2>'.$list['Name'].'</h2>';
                $dateSQL = $list['DateCreation'];        // "2025-01-01"
                $dateObj = new DateTime($dateSQL);
                echo '<p>Created: '.$dateObj->format('d F Y').'</p>';   // 01 January 2025
                ?>    
            </div>
            <form action="List.php" id="sharingList" method="POST">
                <input type="hidden" name="ID_shoppingList" value="<?php echo $idshop; ?>" />
                <label for="sharedemail">Friend's email</label>
                <input type="email" name="sharedemail" id="sharedemail">
                <button type="submit">Share This list</button>
                <?php if(isset($f)){ echo '<p>'.$f.'</p>';} ?>
            </form>
        </header>

        <div id="listElements">
            <?php
                $reqProdList = $bd->prepare('SELECT p.ProductName, ps.ID_ProductsShopping, ps.note FROM products_shopping as ps JOIN products as p ON p.ID_product=ps.ID_product WHERE ps.ID_shoppingList=:list');
                $reqProdList->bindvalue('list',$idshop);
                $reqProdList->execute();
                while ($prod = $reqProdList->fetch()){
                    echo '<form action="List.php" method="POST">';
                    echo '<input type="hidden" name="ID_shoppingList" value="'.$idshop.'">';
                    echo '<input type="hidden" name="DeletePorudct" value="'.$prod['ID_ProductsShopping'].'">';
                    echo '<button type="submit">';
                    echo '<img class="trash" src="Ressources/img/Trash.png" alt="Delete Trash product">';
                    echo '</button>';
                    echo '<div id="product_informations">';
                    echo '<p>'.$prod['ProductName'].'</p>';
                    echo '<p>'.$prod['note'].'</p>';   
                    echo '</div>';
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
                    if (isset($IDcategory) and $IDcategory != 0) {
                        $reqproducts = $bd->prepare('
                            SELECT DISTINCT p.ID_product, p.ProductName 
                            FROM product_categories AS pc
                            JOIN products AS p ON p.ID_product = pc.ID_Product
                            WHERE pc.ID_Category = :cat
                            AND p.StateUp = 1
                            AND (p.ID_user = :usr OR p.ID_user = 0)
                        ');
                        $reqproducts->bindValue('cat', (int)$IDcategory);
                        $reqproducts->bindValue('usr', (int)$_SESSION["login"]["ID_User"]);
                        $reqproducts->execute();
                        while($product =$reqproducts->fetch()){
                                echo '<option value="'.$product['ID_product'].'">'.$product['ProductName'].'</option>';
                        }
                    } else {
                        $reqproducts = $bd->prepare('
                            SELECT DISTINCT p.ID_product, p.ProductName 
                            FROM products AS p
                            WHERE p.StateUp = 1
                              AND (p.ID_user = :usr OR p.ID_user = 0)
                        ');
                        $reqproducts->bindValue('usr', (int)$_SESSION["login"]["ID_User"]);
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

<div id="ShopTheList">
    <?php echo '<a href="#" id="shopNowBtn">Shop it NOW</a>'; ?>
</div>

<div id="rgpdPopup">
    <div class="popup-content">
        <h2>Save your shop location?</h2>
        <p>
            To enhance your shopping experience and provide you with more relevant information, you can save the store you're currently in by allowing us to access your geographic coordinates.
            This app will only use your browser's location data after you click the accept button below. Your data will not be used for advertising purposes or sold to third parties. It will solely be used for internal analysis reports and kept strictly confidential.
        </p>
        <button id="acceptLocation">Accept</button>
        <button id="declineLocation">Deny</button>
    </div>
</div>

<script>
    document.getElementById('shopNowBtn').addEventListener('click', (e) => {
        e.preventDefault(); // Empêche la redirection immédiate
        document.getElementById('rgpdPopup').style.display = 'flex';
    });

    document.getElementById('acceptLocation').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    const idshop = "<?php echo $idshop; ?>";

                    fetch('process_location.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ID_shoppingList: "<?php echo $idshop; ?>",
                            latitude: latitude,
                            longitude: longitude
                        })
                    })
                    .then(response => {
                        console.log('Statut HTTP:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Réponse serveur:', data);
                        if (data.status === 'success') {
                            window.location.href = `ShopList.php?ID_shoppingList=<?php echo $idshop; ?>`;
                        } else {
                            alert('Erreur : ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau :', error);
                        alert('Une erreur est survenue lors de l\'envoi des données.');
                    });
                },
                (error) => {
                    alert('Erreur de géolocalisation : ' + error.message);
                    const idshop = "<?php echo $idshop; ?>";
                    window.location.href = `ShopList.php`;
                }
            );
        } else {
            alert('La géolocalisation n\'est pas prise en charge par ce navigateur.');
            const idshop = "<?php echo $idshop; ?>";
            window.location.href = `ShopList.php`;
        }
        document.getElementById('rgpdPopup').style.display = 'none';
    });

    document.getElementById('declineLocation').addEventListener('click', () => {
        const idshop = "<?php echo $idshop; ?>";
        window.location.href = `ShopList.php?ID_shoppingList=${idshop}`;
        document.getElementById('rgpdPopup').style.display = 'none';
    });
</script>
    </main>
</body>
</html>