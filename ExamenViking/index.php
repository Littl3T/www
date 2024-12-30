<?php
#	Examen Blanc HEH 2024-25	 
#		 Tom Deneyer
# 	Pour cours BackEnd PHP
include "_connexionBD.php";

# Définition des saisons
$saisons = ['hiver', 'printemps', 'été', 'automne'];

# Récupération du formulaire GET
if(isset($_GET['lieu']) and isset($_GET['annee']) and isset($_GET['saison'])){
	$saisonselected = (int) $_GET['saison'];
	$annee = (int) $_GET['annee'];
	$lieu = htmlspecialchars($_GET['lieu']);

	# Vérification des bon formats des données
	if(!empty($lieu)){
		$error_lieu = false;
	} else $error_lieu = true;
	if($annee){
		$error_annee = false;
	} else $error_annee = true;
	if($saisonselected >=0 and $saisonselected <=3){
		$error_saison = false;
	} else $error_saison = true;

	# Verification de présence d'erreur pour envoie du formulaire en db
	if(!$error_annee and !$error_lieu and !$error_saison){
		# Formulaire ok, Définition des variables de base
		$imgadd = 'casque';
		# Requete insert
		$reqadd = $bd->prepare('INSERT INTO pillages (id_pillage, annee, saison, lieu, id_chef, icone, drakkars,
								naufrages, butin, esclaves, vikings, pertes, météo, commentaire)
								VALUES (NULL,:annee,:saison ,:lieu ,NULL ,:icone ,NULL ,NULL ,NULL ,NULL ,NULL ,NULL ,NULL ,NULL )');
		$reqadd->bindvalue("annee", $annee);
		$reqadd->bindvalue("saison", $saisonselected);
		$reqadd->bindvalue("lieu", $lieu);
		$reqadd->bindvalue("icone", $imgadd);
		$reqadd->execute();

		# Redirection pour raffréchir la page
		header('Location:index.php');
	}

} else{
	# Si pas de formulaire, pas d'erreur
	$error_annee = false;
	$error_lieu = false;
	$error_saison = false;
}
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
			<div class="center">
			<?php
			$reqpillage = $bd->prepare('SELECT p.lieu, p.butin,p.vikings,p.pertes,p.id_pillage,p.annee,p.saison,p.icone,p.id_chef FROM pillages as p ORDER BY p.annee DESC, p.saison DESC, p.id_pillage DESC; ');
			$reqpillage->execute();
			while ( $pillage=$reqpillage->fetch() ) {
				# Définition de l'icone selons critères définis sur l'exercice
				if(!empty($pillage['icone'])){
					$icone ='icons/'.$pillage['icone'].'.png';
				} elseif($pillage['vikings']==$pillage['pertes']){
					$icone = 'icons/crane.png';
				} elseif(($pillage['butin']/($pillage['vikings']-$pillage['pertes']))>=50){
					$icone = 'icons/butin.png';
				} elseif($pillage['pertes']>($pillage['vikings']/3)){
					$icone = 'icons/bataille.png';
				} elseif($pillage['pertes']==0){
					$icone = 'icons/bouclier.png';
				} else $icone = 'icons/epee.png';

				# Définition du lien, si pas de chef même page, si oui pillage.php avec value GET du pillage
				if(!empty($pillage['id_chef'])){
					$lien = 'pillage.php?id_pillage='.$pillage['id_pillage'];
				} else $lien = '#';

				# Création des liens des pillages avec informations de la base de données
				echo '<a href="'.$lien.'" class="pillagesliens">';
				echo '<img src="'.$icone.'" alt="Icone de pillage">'; 
				echo '<p>'.$pillage['lieu'].', '.$saisons[$pillage['saison']].' '.$pillage['annee'].'</p>';
				echo '</a>';
			   }
			?>
			</div>
		</main>
		<footer>
			<div class="center">
			   <form id="FormulairePillage" action="index.php" method='GET'>
				<h2>Enregistrer un nouveau pillage</h2>
				<label 	<?php if($error_lieu){echo 'class="errorform"';} ?> for="lieu">Lieu</label>
				<input type="text" name="lieu" id="lieu">
				<label	<?php if($error_annee){echo 'class="errorform"';} ?> for="annee">Année</label>
				<input type="number" name="annee" id="annee">
				<label  <?php if($error_saison){echo 'class="errorform"';} ?> for="saison">Saison</label>
				<select name="saison" id="saison">
					<?php
						foreach($saisons as $key=>$val){
							echo '<option value="'.$key.'">'.$val.'</option>';
						}
					?>
				</select>
				<button type="submit">Enregistrer</button>
			   </form>
			</div>
		</footer>
	</body>
</html>