<?php
include "_connexionBD.php";
if(isset($_GET['heroselect']) and isset($_GET['filmselect']) and isset($_GET['citationform'])){
    $heroselected = (int)$_GET['heroselect'];
    $filmselected = (int)$_GET['filmselect'];
    $citationform = $_GET['citationform'];

    #Vérificaiton du héro non secondaire et existant
    $reqname = $bd->prepare('SELECT * FROM heros as h WHERE h.id_heros = :hero and h.secondaire=0');
    $reqname->bindvalue("hero",$heroselected);
    $reqname->execute();
    $myboy = $reqname->fetch();
    if(!empty($myboy['id_heros'])){
        $errorjedi = false;
    } else {$errorjedi = true;}
    
    #Vérification du film existant
    $reqcheckmovie = $bd->prepare('SELECT f.id_film, f.titre FROM films as f WHERE id_film=:movie');
    $reqcheckmovie->bindvalue("movie",$filmselected);
    $reqcheckmovie->execute();
    $mymovie = $reqcheckmovie->fetch();
    if(!empty($mymovie['id_film'])){
        $errorfilm = false;
    } else {$errorfilm = true;}

    #Vérification de la présence d'une citation
    if(!empty($_GET['citationform'])){
        $errorcitation = false;
    } else {$errorcitation = true;}

    #Si toutes les vérifications passées, ajout de la citation et redirection vers index.php avec le get ciblé sur le héro sélectionné
    if(!$errorcitation and !$errorfilm and !$errorjedi){
        $reqinsertcitation = $bd->prepare('INSERT INTO `citations` (`id_citation`, `id_heros`, `id_film`, `citation`) VALUES (NULL, :hero, :film, :citation)');
        $reqinsertcitation->bindvalue("hero", $heroselected);
        $reqinsertcitation->bindvalue("film", $filmselected);
        $reqinsertcitation->bindvalue("citation", $citationform);
        $reqinsertcitation->execute();
        header('Location:index.php?id_heros='.$heroselected);
    }
} else{
    #Si pas de get, pas d'erreur...
    $errorcitation = false;
    $errorfilm = false;
    $errorjedi = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CitationJedi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1><a href="index.php" id='lien'>Jedis</a></h1>
        <form action="citation_ajout.php" id="formulaire">
            <h2>Enregistrer une nouvelle citation</h2>
            <label for="heroselect" <?php if($errorjedi){echo 'class="error"';}?>>Héro</label>
            <select name="heroselect" id="heroselect">
                <?php
                    $reqselecthero = $bd->prepare('SELECT h.id_heros, h.nom, h.surnom FROM heros as h WHERE h.secondaire=0 ORDER BY h.id_heros');
                    $reqselecthero->execute();
                    while ( $hero=$reqselecthero->fetch() ){
                        echo '<option value="'.$hero["id_heros"].'">'. $hero["nom"].' '.$hero["surnom"] .'</option>';
                    }
                ?>
            </select>
            <label for="filmselect" <?php if($errorfilm){echo 'class="error"';}?>>Film</label>
            <select name="filmselect" id="filmselect">
                <?php
                $reqmovie = $bd->prepare('SELECT f.id_film, f.titre FROM films as f');
                $reqmovie->execute();
                while ( $movie=$reqmovie->fetch() ){
                    echo '<option value="'.$movie["id_film"].'">'. $movie["titre"] .'</option>';
                }
                ?>
            </select>
            <label for="citationform" <?php if($errorcitation){echo 'class="error"';}?>>Citation</label>
<textarea name="citationform" id="citationform" rows="6" cols="34">
</textarea>
            <button type="submit">Enregistrer</button>
        </form>
    </main>
</body>
</html>