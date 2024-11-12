<?php 
date_default_timezone_set('Europe/Brussels'); //Fuseau horaire
$jours = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
$mois = array('Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre','Octobre', 'Novembre','Decembre')
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Jours avant le weekend</title>
</head>
<body>
	<h1>Vivement le weekend !</h1>
	<?php 
	//Calcul du nombre de jours restant avant samedi
	$joursAvantWE = max( 6 - date('w') ,0);
	
	//Affichage
	echo '<p>Vivement ',$jours[6],' !</p>'; 
	echo '<p>Nous sommes ',$jours[date('w')], ', le ' , date('j'),' ', $mois[date('n')],' ',date('Y'),' ', date('H'),'h',date('i') ,'... plus que ',$joursAvantWE,' jours avant ',$jours[6],'.</p>';
	echo '<p>Il s\'est écoulé ',number_format(time()-mktime(0,0,0,1,6,2004),0),' secondes depuis votre naissances.' ;
	echo '<p>Il s\'est écoulé ',round((time()-mktime(0,0,0,1,6,2004))/86400),' jours depuis votre naissances.' ;
	?>
</body>
</html>