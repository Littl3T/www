<?php
include "_connexionBD.php";

if(isset($_GET['villeselected'])){
    $villeID= (int) $_GET['villeselected'];
} else $villeID = 1

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <div id="Top10Restaurants">
            <?php
            $reqrestaurants = $bd->prepare('SELECT v.id_ville,v.ville,v.code_pays, p.pays, COUNT(r.id_restaurant) AS NombreRestaurant FROM villes AS v JOIN restaurants AS r ON r.id_ville=v.id_ville JOIN pays AS p ON p.code=v.code_pays GROUP BY r.id_ville ORDER BY NombreRestaurant DESC, v.ville LIMIT 10;');
            $reqrestaurants->execute();
            $x = 1;
            while($restaurant = $reqrestaurants->fetch()){
                echo '<a href="index.php?villeselected='.$restaurant['id_ville'].'" class="restuarantsinfos ';
                if($x<=3){echo "boldy";}
                echo '">';
                echo $x.' '.$restaurant['ville'].' :';
                echo '<img src="flags/'.$restaurant['code_pays'].'.webp" alt="Drapeau Pays Restaurant">';
                echo ' :'.$restaurant['pays'].': ';
                for($y=0;$y< (int)$restaurant['NombreRestaurant'];$y++) {
                    echo '<img src="icones/restaurant.png" alt="Restaurants" class="restoimg">';
                }
                echo '</a>';
                $x+=1;
            }
            ?>
        </div>
        <form action="index.php" method="GET">
            <select name="villeselected" id="">
                <?php 
                $reqvilles = $bd->prepare('SELECT v.ville,v.id_ville FROM restaurants AS r JOIN villes as v ON r.id_ville=v.id_ville GROUP BY r.id_ville HAVING COUNT(r.id_ville>0) ORDER BY v.ville;');
                $reqvilles->execute();
                while($ville = $reqvilles->fetch()){
                    echo '<option value="'.$ville['id_ville'].'" ';
                    if(isset($villeID)){if($ville['id_ville']==$villeID){echo "selected"; $nomVille=$ville['ville'];}}
                    echo ' >'.$ville['ville'].'</option>';
                } ?>
            </select>
            <button type="submit">Voir les restaurants</button>
        </form>
        <?php 
        if(isset($villeID)){
            $reqRestoVille = $bd->prepare('SELECT r.nom,r.description,SUM(v.nombre*b.prix) AS totalVentes FROM ventes as v JOIN commandes AS c ON v.id_commande=c.id_commande JOIN employes AS e ON e.id_employe=c.id_employe JOIN restaurants as r ON r.id_restaurant=e.id_restaurant JOIN burgers as b ON b.id_burger=v.id_burger WHERE r.id_ville=:ville AND r.ouvert=1 GROUP BY r.id_restaurant ORDER BY r.nom;');
            $reqRestoVille->bindvalue('ville',$villeID);
            $reqRestoVille->execute();
            echo '<div id="listesrestaurantsactfs">';
            $total = 0;
            while($restaurantActif = $reqRestoVille->fetch()){
                echo '<p><b>'.$restaurantActif['nom'].'</b> ';
                echo $restaurantActif['description'].'<br>';
                echo $restaurantActif['totalVentes'].' € </p>';
                $total+=$restaurantActif['totalVentes'];
            }
            echo '<p> Le total des ventes pour'. $nomVille.' est de: '.$total.' €</p>';
        }
        ?>
    </main>
</body>
</html>