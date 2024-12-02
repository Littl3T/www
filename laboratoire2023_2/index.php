<?php
include "_connexionBD.php";
$selectedcountries = array();
if(isset($_GET["continent"])){
    $_GET["continent"]=(int)$_GET["continent"];

    $pays = $bd->prepare('SELECT e.pays,e.code,p.place FROM equipes AS e JOIN continents AS c ON c.id_continent=e.id_continent JOIN participations AS p ON p.code=e.code WHERE c.id_continent=:continentAcronyme');
    $pays->bindValue('continentAcronyme',$_GET["continent"]);
    $pays->execute();
    $pays->setFetchMode(PDO::FETCH_OBJ); while ($result=$pays->fetch() ) { 
        $selectedcountries[$result->code]=$result->code;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorldCup2018</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    ?>
    <main>
        <section id="top8">
            <h2> Les 8 derniers matchs </h2>
            <div id="flexinverse">
                <?php
                    $matchs = $bd->prepare('SELECT e1.code AS pays1Code,e2.code AS pays2Code, e1.pays AS pays1 ,e2.pays AS pays2 ,m.score1,m.score2 FROM matchs AS m JOIN equipes AS e1 ON e1.code=m.equipe1 JOIN equipes AS e2 ON e2.code=m.equipe2 ORDER BY m.date DESC LIMIT 8;');
                    $matchs->execute();
                    $matchs->setFetchMode(PDO::FETCH_OBJ); while ($result=$matchs->fetch() ) { 
                        echo "<p> <span ";
                        if(isset($selectedcountries[$result->pays1Code])){ echo "class='bold'";}
                        echo ">$result->pays1</span>-<span ";
                        if(isset($selectedcountries[$result->pays2Code])){ echo "class='bold'";}
                        echo ">$result->pays2</span> : $result->score1 - $result->score2";
                    }
                    $matchs->closeCursor();
                ?>
            </div>
        </section>
        <section id="stade">
            <h2>Principaux Stades</h2>
            <div>
                <?php
                    $stade = $bd->prepare('SELECT s.nom AS nomstade,AVG(m.spectateurs) AS nombreSpectateur, COUNT(m.id_match) AS NombreMatch FROM stades AS s JOIN matchs AS m ON m.id_stade=s.id_stade GROUP BY s.nom HAVING NombreMatch > 5 ORDER BY nombreSpectateur DESC;
');
                    $stade->execute();
                    $stade->setFetchMode(PDO::FETCH_OBJ); while ($result=$stade->fetch() ) { 
                        $num = round($result->nombreSpectateur,1);
                        echo "<p> <span class='bold'> $result->nomstade </span> : $num spectateur moy. ; $result->NombreMatch matchs ";
                    } 
                ?>
            </div>
        </section>
        <section id="Pays">
            <h2>Pays Participants</h2>

            <form action="index.php" method="get">
                <fieldset>
                    <legend> Choix du continent </legend>
                    <select name="continent" id="continent">
                        <?php
                        $continent = $bd->prepare('SELECT * FROM `continents`');
                        $continent->execute();
                        $continent->setFetchMode(PDO::FETCH_OBJ); while ($result=$continent->fetch() ) { 
                            echo "<option value='$result->id_continent'";
                            if(isset($_GET["continent"])){
                                if ($_GET["continent"]===$result->id_continent){
                                    echo "selected";
                                }
                            }
                            echo ">$result->acronyme : $result->continent </option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Voir les pays participants</button>
                </fieldset>
            </form>

            <div>
                <?php
                    $pays = $bd->prepare('SELECT e.pays,e.code,p.place FROM equipes AS e JOIN continents AS c ON c.id_continent=e.id_continent JOIN participations AS p ON p.code=e.code WHERE c.id_continent=:continentAcronyme');
                    $pays->bindValue('continentAcronyme',$_GET["continent"]);
                    $pays->execute();
                    $pays->setFetchMode(PDO::FETCH_OBJ); while ($result=$pays->fetch() ) { 
                        echo "<div> <img src='flags/$result->code.webp'> $result->pays";
                        if($result->place <9){
                            echo " : $result->place <sup>e</sup>";
                        }
                        echo "</div>";
                    }
                ?>
            </div>
        </section>
    </main>
</body>
</html>