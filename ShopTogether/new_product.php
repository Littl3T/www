<?php
session_start();
if (empty($_SESSION["login"])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("location:index.php");
    exit(); // Exit to prevent further execution
} else include "_connexionBD.php";
$message = "";
$name ="";
if(isset($_POST['productName'])){
    $name = $_POST['productName'];
    if(isset($_POST['categories'])){
        $reqCheckName = $bd->prepare("Select * FROM products WHERE ProductName=:namecheck");
        $reqCheckName->bindvalue("namecheck",$name);
        $reqCheckName->execute();
        $countrw = $reqCheckName->rowCount();
        if($countrw<1){
            $reqNewProduct = $bd->prepare("INSERT INTO products (ID_product,ID_user, ProductName, DateCreation, DateDeleted, StateUp) VALUES (NULL,:userid , :productName, NOW(), NULL, 1);");
            $reqNewProduct->bindvalue("productName",$name);
            $reqNewProduct->bindvalue("userid",$_SESSION["login"]['ID_User']);
            $reqNewProduct->execute();
            $message = "Product Added";

            $reqgetidname = $bd->prepare("Select * FROM products WHERE ProductName=:namecheck");
            $reqgetidname->bindvalue("namecheck",$name);
            $reqgetidname->execute();
            $Product = $reqgetidname->fetch(PDO::FETCH_ASSOC);
            $Product_ID = $Product['ID_product'];
            $name="";
            
            $reqAddCategories = $bd->prepare("INSERT INTO product_categories (ID_ProductCategories, ID_Category, ID_Product) VALUES(NULL, :idcat, :idproduct);");
            foreach($_POST['categories'] as $cat){
                $reqAddCategories->bindvalue("idcat",$cat);
                $reqAddCategories->bindvalue('idproduct',$Product_ID);
                $reqAddCategories->execute();
            }
        } else $message = "This Product already exists";
    } else $message = "Select at least 1 category";
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
    if (file_exists("_header.php")) {
        include "_header.php";
    } else {
        echo "<p>Header file missing.</p>";
    }
    ?>
    <main>
        <form action="" method="POST"> <!-- Submit to the same page -->
            <?php
            echo "<i> $message </i>"
            ?>
            <h2 id="titleproduct">New Product</h2>
            <div id="createproduct">
                <div id="infosproductcreate">
                    <label for="productName">Product Name</label>
                    <?php echo" <input type='text' id='productName' name='productName' value='$name' required>"; ?>                  
                </div>
                <button type="submit">Create</button>
            </div>
            <fieldset id="field_categories">
                <legend>Categories</legend>
                <?php
                $reqcategoies = $bd->prepare("SELECT * FROM categories");
                $reqcategoies->execute();
                $categories = $reqcategoies->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) {
                    $src = str_replace(" ","_",$category['CategoryName']).".webp";
                    echo "<div id='categories'>";
                    echo "<input type='checkbox' id='" . $category['ID_Category'] . "' name='categories[]' value='" . $category['ID_Category'] . "'>";
                    echo "<label for='" . $category['ID_Category'] . "'>" . htmlspecialchars($category['CategoryName']);
                    echo "<img src='Ressources/img/$src' alt='categoryImage'>";
                    echo " </label> </div>";
                }
                ?>
            </fieldset>
        </form>

        <?php
        if (isset($_POST["categories"])) {
            print_r($_POST); // Use correct square bracket syntax
        }
        ?>
    </main>
</body>
</html>
