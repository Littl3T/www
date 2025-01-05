<?php
$userID = null;
$userImgPath = "Ressources/img/account.png"; // Chemin par défaut

if (!empty($_SESSION["login"]["ID_User"]) or !0) {
    $userID = (int) $_SESSION["login"]["ID_User"];
    $potentialImg = "Ressources/userImg/" . $userID . ".webp";
    if (file_exists($potentialImg)) {
        $userImgPath = $potentialImg;
    }
    if(str_ends_with($_SERVER['PHP_SELF'],'shoppingList.php')){
        $shoppingList='class="selectedLink"';
    } elseif (str_ends_with($_SERVER['PHP_SELF'],'ShoppingHistory.php')){
        $shoppinghistory ='class="selectedLink"';
    } elseif (str_ends_with($_SERVER['PHP_SELF'],'products.php')){
        $products = 'class="selectedLink"';
    }
}
?>
<header id="main_header">
    <a href="home.php" id="logo">
        <img src="Ressources/img/shoptogether.png" alt="Logo">
    </a>
    <div id="burgerMenu" onclick="toggleMenu()">&#9776;</div>
    
    <nav id="top_nav">
        <a href="shoppingList.php" <?php if(isset($shoppingList)){echo $shoppingList;} ?> >ShoppingList</a>
        <a href="ShoppingHistory.php" <?php if(isset($shoppinghistory)){echo $shoppinghistory;} ?>>ShoppingHistory</a>
        <a href="products.php" <?php if(isset($products)){echo $products;} ?>>Products</a>
    </nav>

    <a href="account.php" id="accountBtn">
        <!-- On affiche l'image de profil si elle existe, sinon un icône par défaut -->
        <img src="<?php echo htmlspecialchars($userImgPath); ?>" alt="Your Account">
    </a>
</header>

<script>
function toggleMenu() {
    document.getElementById("top_nav").classList.toggle("open");
}
</script>
