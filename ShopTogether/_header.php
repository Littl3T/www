<?php
// Assure-toi d'avoir fait session_start() quelque part avant ce fichier
// (souvent dans l'index.php ou un init global).

$userID = null;
$userImgPath = "Ressources/img/account.png"; // Chemin par défaut

if (!empty($_SESSION["login"]["ID_User"])) {
    $userID = (int) $_SESSION["login"]["ID_User"];
    // Chemin possible de l'image de profil
    $potentialImg = "Ressources/userImg/" . $userID . ".webp";
    // Si le fichier existe, on l'utilise
    if (file_exists($potentialImg)) {
        $userImgPath = $potentialImg;
    }
}
?>
<header id="main_header">
    <a href="home.php" id="logo">
        <h1>ShopTogether</h1>
    </a>
    <div id="burgerMenu" onclick="toggleMenu()">&#9776;</div>
    
    <nav id="top_nav">
        <a href="shoppinglist.php">ShoppingList</a>
        <a href="ShoppingHistory.php">ShoppingHistory</a>
        <a href="products.php">Products</a>
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
