<?php
# Vérifier la présence du get pillage, si présent check s'il existe, sinon redirige dans les deux cas d'invalidité
if(isset($_GET['id_pillage'])){
    include "_connexionBD.php";
    $id_pillage = (int)$_GET['id_pillage'];
    $reqcheckpillage = $bd->prepare('SELECT p.id_pillage FROM pillages as p WHERE p.id_pillage =:id');
    $reqcheckpillage->bindvalue('id',$id_pillage);
    $reqcheckpillage->execute();
    $sortie = $reqcheckpillage->fetch();
    if(!empty($sortie['id_pillage'])){
        
    } else {header('Location:index.php');exit();}
} else{header('Location:index.php');exit();}

# Définition des saisons
$saisons = ['hiver', 'printemps', 'été', 'automne'];
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Vikings</title>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<header>
			<div class="center">
				<h1><img src="icons/amulette.png" alt=""> Les Pillages d'Ingvar</h1>
				<p>Toutes les expéditions menées par Ingvar l'intrépide et son clan depuis l'an 812 (année où Ingvar décapita Arnulf le fourbe et prit le pouvoir).</p>
			</div>
		</header>
		<main>
			<div id="PillageCarte" class="center">
			<?php
            # Requete pour obtenir les infos du pillage avec jointure
            $reqpillage = $bd->prepare('SELECT p.drakkars,p.naufrages,p.esclaves,p.commentaire, c.nom,c.titre,c.icone AS cheficone,p.lieu, p.butin,p.vikings,p.pertes,p.id_pillage,p.annee,p.saison,p.icone,p.id_chef FROM pillages as p JOIN chefs as c ON c.id_chef=p.id_chef WHERE p.id_pillage=:id');
            $reqpillage->bindvalue('id',$id_pillage);
            $reqpillage->execute();
            $pillage=$reqpillage->fetch();

            # Définition des valiables
            $icone = 'icons/'.$pillage['cheficone'].'.png';
            $drakkars = (int) $pillage['drakkars'];
            $pertedrakkar = (int) $pillage['naufrages'];

            # Header du pillage
            echo '<div id="pillageheader">';
            echo '<div id="pillagetitle">';
            echo  '<img src="'.$icone.'" alt="LeaderIcone">';
            echo '<h2>'.$pillage['lieu'].', '.$saisons[$pillage['saison']].' '.$pillage['annee'].'</h2>';
            echo '</div>';
            echo '<p>Pillage Mené Par '.$pillage['nom'].' '.$pillage['titre'].'</p>';
            echo '</div>';
            echo '<div id="pillagecontenu">';
            # Liens index.php
            echo '<a href="index.php">⬅ Retour à la page d\'accueil</a>';
            
            # DRAKKARS ET PERTES
            echo '<div id="drakkars">';
            for($i=0;$i<$drakkars;$i++){
                echo '<img src="icons/drakkar.png" alt="ImageDrakkar">';
            }
            if($pertedrakkar>0){
                echo '<p>-'.$pertedrakkar.'</p>';
            }
            echo '</div>';

            # Partie générale, chaque info un div avec chiffre
            echo '<div id="ContenuPrincipalPillage">';  

            # NombreViking
            echo '<div class="img_chiffre">';
            echo '<img src="icons/viking.png" alt="ImageViking">';
            echo '<p>'.$pillage['vikings'].'</p>';
            echo '</div>';

            # Perte
            echo '<div class="img_chiffre">';
            echo '<img src="icons/crane.png" alt="ImageMort">';
            echo '<p>'.$pillage['pertes'].'</p>';
            echo '</div>';

            # Buttin
            echo '<div class="img_chiffre">';
            echo '<img src="icons/butin.png" alt="ImageButinTresorViking">';
            echo '<p>'.$pillage['butin'].'</p>';
            echo '</div>';

            # Escalve
            echo '<div class="img_chiffre">';
            echo '<img src="icons/esclave.png" alt="ImageEsclave">';
            echo '<p>'.$pillage['esclaves'].'</p>';
            echo '</div>';

            echo '</div>';  
            # Commentaires
            echo '<p id="comment">'.$pillage['commentaire'].'</p>';
            echo '</div>';
			?>
			</div>
		</main>
	</body>
</html>