<header id="main_header">
    <a href="home.php" id="logo"><h1>ShopTogether</h1></a>
    <div id="burgerMenu" onclick="toggleMenu()">&#9776;</div>
    <nav id="top_nav">
        <a href="shoppinglist.php">ShoppingList</a>
        <a href="ShoppingHistory.php">ShoppingHistory</a>
        <a href="products.php">Products</a>
    </nav>
    <a href="#" id="accountBtn"><img src="Ressources/img/account.png" alt="Your Account"></a>
</header>
<script>
function toggleMenu() {
    document.getElementById("top_nav").classList.toggle("open");
}
</script>
