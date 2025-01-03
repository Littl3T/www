<?php
session_start();
if(empty($_SESSION["login"])){
    header("location:index.php");
    $_SESSION = [];
    exit();
    session_unset();
    session_destroy();
} else include "_connexionBD.php";

$message = "";
if(isset($_POST["Name"]) && !empty($_POST["Name"])){
    $reqnew_list = $bd->prepare("INSERT INTO shoppinglist (ID_shoppingList, DateCreation, DateValidation, Name)
                                 VALUES(NULL, :ajd, NULL, :name)");
    $reqnew_list->bindValue(':ajd', date('Y-m-d'));
    $reqnew_list->bindValue(':name', $_POST['Name']);
    $reqnew_list->execute();
    
    $lastId = $bd->lastInsertId(); // Récupère l'ID du dernier enregistrement inséré
    
    $reqAddJoinTable= $bd->prepare("INSERT INTO user_shopping (ID_UserShopping, ID_user, ID_shopping)
                                    VALUES(NULL, :user, :idshoppingList)");
    $reqAddJoinTable->bindValue(':user', $_SESSION["login"]['ID_User']);
    $reqAddJoinTable->bindValue(':idshoppingList', $lastId);
    $reqAddJoinTable->execute();
    
    $message = "Shopping List Added with success";
} else {
    $message = "Please enter a valid shopping list name";
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

    <!-- Le main spécifique à la création de liste -->
    <main class="NewListPage">
        <p>Each list is a unique collection of products usable in multiple lists.</p>
        <p>Name the list as wished and once done, add products to it!</p>
        
        <form action="new_list.php" method="POST" id="createListForm">
            <i><?php echo $message; ?></i>
            
            <label for="Name">List name</label>
            <input type="text" name="Name" id="Name" />
            
            <button type="submit">Create List</button>
        </form>
    </main>
</body>
</html>
