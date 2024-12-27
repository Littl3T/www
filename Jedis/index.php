<?php
include "_connexionBD.php";

if(isset($_GET['id_heros'])){
    $id_heros = (int)$_GET['id_heros'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jedis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <main>
    <h1>Jedis</h1>
    <div id="listheros">
        <div class="jedis">
        <?php
            $reqSith = $bd->prepare('SELECT h.id_heros, h.cote_obscur, h.nom, h.surnom, h.sabres, h.premiere_apparition FROM heros as h WHERE h.cote_obscur=0 and h.secondaire=0 ORDER BY h.premiere_apparition;');
            $reqSith->execute();
            while ( $sith=$reqSith->fetch() ) {
                $sabres = explode(';',$sith['sabres']);
                echo '<a href="index.php?id_heros='.$sith['id_heros'].'">';
                echo '<p';
                if(isset($id_heros)){
                    if((int)$id_heros === (int)$sith['id_heros']){
                        echo ' id="selected_hero" ';
                    }
                }
                echo '>'.$sith['nom'].' ';
                if(!empty($sith['surnom'])){
                    echo '"'.$sith['surnom'].'"';
                }
                if(!empty($sabres[0])){
                    foreach ($sabres as $key=>$val){
                      echo "<span class='$val'>|</span>";
                  }  
                  }
                echo '</p>';
                echo '</a>';
               }               
            ?>
        </div>
        <div class="jedis">
            <?php
            $reqSith = $bd->prepare('SELECT h.id_heros, h.cote_obscur, h.nom, h.surnom, h.sabres, h.premiere_apparition FROM heros as h WHERE h.cote_obscur=1 and h.secondaire=0 ORDER BY h.premiere_apparition;');
            $reqSith->execute();
            while ( $sith=$reqSith->fetch() ) {
                $sabres = explode(';',$sith['sabres']);
                echo '<a href="index.php?id_heros='.$sith['id_heros'].'">';
                echo '<p';
                if(isset($id_heros)){
                    if((int)$id_heros === (int)$sith['id_heros']){
                        echo ' id="selected_hero" ';
                    }
                }
                echo '>'.$sith['nom'].' ';
                if(!empty($sith['surnom'])){
                    echo '"'.$sith['surnom'].'"';
                }
                if(!empty($sabres[0])){
                  foreach ($sabres as $key=>$val){
                    echo "<span class='$val'>|</span>";
                }  
                }
                echo '</p>';
                echo '</a>';
               }               
            ?>
        </div> 
    </div>
    <div id="citation">
        <h2>Citations de
            <?php 
            if(isset($id_heros)){
                $reqname = $bd->prepare('SELECT h.nom, h.surnom FROM heros as h WHERE id_heros = :name');
                $reqname->bindvalue("name",$id_heros);
                $reqname->execute();
                $myboy = $reqname->fetch();
                if(!empty($myboy['nom'])){
                    echo $myboy['nom'];
                } else echo $myboy['surnom'];
            } else echo ' ...';
            ?>
        </h2>
        
        <?php
        if(isset($id_heros)){
            $reqcitation = $bd->prepare('SELECT c.id_citation, c.id_heros, c.citation, f.titre FROM citations as c JOIN films AS f on c.id_film=f.id_film WHERE c.id_heros=:hero ORDER BY f.annee;');
            $reqcitation->bindvalue("hero",$id_heros);
            $reqcitation->execute();
            while ($citation=$reqcitation->fetch() ) {
                if(empty($citation['citation'])){
                    echo "Auncune citation trouvée...";
                } else echo '<p>"'.$citation['citation'].'" - <span class="nom_film">'. $citation['titre'].'</span> </p>';
            }
        } else echo '<p class="nom_film"> Cliquez sur un jedi pour connaitre ses citations ! </p>';
        ?>
        <a href="citation_ajout.php">Ajouter une citation</a>
    </div>

   </main> 
</body>
</html>