<?php
include "_connexionBD.php";
if(isset($_GET['burgerselected'])){
    $id_buger = (int)$_GET['burgerselected'];
}
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
        <div id='topburger'>
            <?php
            $reqburger = $bd->prepare('SELECT b.id_burger,b.nom, b.prix, b.stock, GROUP_CONCAT(i.nom) AS ingredient FROM burgers as b JOIN liste_ingredients as li ON li.id_burger=b.id_burger JOIN ingredients as i ON li.id_ingredient = i.id_ingredient GROUP BY b.id_burger ORDER BY b.stock DESC LIMIT 10');
            $reqburger->execute();
            while ($burger = $reqburger->fetch()) {
                # Listes des ingrédients en array
                $ingredients = explode(',',$burger["ingredient"]);
                # Formatage de nom de l'image via l'id burger avec des 0 devant et png derniere
                $idFormatte = $burger['id_burger'];
                while (strlen($idFormatte) < 3) {
                    $idFormatte = '0' . $idFormatte;
                }
                $idFormatte = 'b'.$idFormatte.'.png';

                # structure et iniformations html
                echo '<a href="index.php?burgerselected='.$burger['id_burger'].'" class="burgerlist">';
                echo '<img class=burger src="images/'.$idFormatte.'" alt="BurgerImage">';
                echo "<p>". $burger['nom'].' - '. $burger['prix'].'€' . ' - '. $burger['stock'].': </p>';
                foreach($ingredients as $ig){
                    echo '<img class="ingredient" src="icones/'.$ig.'.png'.'" alt="IngredientImage">';
                }
                echo "</a>";
            }
            ?>
        </div>
        <div id="formulaire">
            <form action="index.php">
                <select name="burgerselected" id="burgerselected" method="GET">
                    <?php
                    $reqburgerfrom = $bd->prepare('SELECT b.nom, b.id_burger FROM burgers as b ORDER BY b.nom');
                    $reqburgerfrom->execute();
                    while ($burgeroption = $reqburgerfrom->fetch()){
                        if(isset($id_buger)){
                            if($id_buger === $burgeroption['id_burger']){
                                $m='selected';
                            } else $m ='';
                        } else $m='';
                        echo '<option value="'.$burgeroption["id_burger"].'"'.$m.'>'. $burgeroption["nom"] .'</option>';
                    }
                     ?>
                </select>
                <button type="submit">Voir les employés</button>
            </form>
            <div>
                <?php
                if(isset($id_buger)){
                    $reqlistemployeerburger = $bd->prepare('SELECT br.nom as BurgerName, v.id_burger , e.nom, e.prenom, SUM(v.nombre) AS NombreVendu, br.prix*SUM(v.nombre) AS Somme FROM ventes AS v JOIN commandes AS c ON c.id_commande = v.id_commande JOIN employes AS e ON e.id_employe = c.id_employe JOIN burgers as br ON br.id_burger = v.id_burger WHERE v.id_burger=:id GROUP BY c.id_employe ORDER BY e.nom;');
                    $reqlistemployeerburger->bindvalue('id',$id_buger);
                    $reqlistemployeerburger->execute();
                    $somme = 0;
                    while ($employee = $reqlistemployeerburger->fetch()){
                        echo '<p>'.$employee['nom'].' - '.$employee['prenom'].' : '.$employee['NombreVendu'].'</p>';
                        echo '<p>'.$employee['Somme'].' €</p>';
                        $somme += $employee['Somme'];
                        $burgerName = $employee['BurgerName'];
                    }
                    echo "Le total des ventes pour".$burgerName."est de :".$somme.'€';
                }
                ?>
            </div>
        </div>
    </main>
</body>
</html>