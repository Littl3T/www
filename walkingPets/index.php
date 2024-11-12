<?php
    session_start();
    include "_bd.php"
?>
<?php
$informations = array();
$reqNoms = $bd->prepare('
    SELECT 
        pets.name, 
        pets.walks, 
        pets.meals, 
        pets.comment, 
        categories.name AS category_name, 
        categories.icon, 
        categories.max_walks, 
        pets.id_pet 
    FROM pets 
    LEFT JOIN categories 
    ON pets.id_cat = categories.id_cat
');
$reqNoms->execute();
$informations = $reqNoms->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WalkingPets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    include "_header.php"
    ?>
    <main>
        <?php
        foreach ($informations as $animal_info) {
            if(empty($animal_info["icon"])) {
                $animal_info["icon"] = "unknown.png";
            }
            echo "<a href='modif_pet.php?id_pet=".$animal_info['id_pet']."'>";
            echo "<figure>";
            echo "<img src='icons/" . str_replace("jpg","png",$animal_info["icon"]) . "' alt='" . $animal_info["category_name"]. " icon'>";
            echo "<figcaption>";
            echo "<h3>" . $animal_info["name"]."</h3>";
            if (isset($_SESSION['pets'][$animal_info['id_pet']])){
                $visited = 'class="visited"';
            } else{
                $visited = '';
            }
            if($animal_info["walks"] >$animal_info["max_walks"] and !empty($animal_info["max_walks"])) {
                echo "<p $visited> Walks: " . "<span class='wrong'>" .$animal_info["walks"] . "</span> ".$animal_info["max_walks"];
            } else{
                echo "<p $visited> Walks: " . $animal_info["walks"];
            }

            echo "<p $visited> Meals: " . $animal_info["meals"];
            if(!empty($animal_info["comment"])) {
                echo "<p $visited> <span $visited title=\"".$animal_info["comment"]."\">"."Remarque..."." </span>";
            }
            echo "</figcaption>";
            echo "</figure>";
            echo "</a>";
        }
        ?>
    </main>
    <p id="comment">*Seuls les chiens et les chats ont droit aux promenades avec au maximum 2 promenades par jours pour les chiens et 1 pour les chats.</p>
</body>
</html>


