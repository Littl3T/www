<?php  
$prix_EUR = 100 ;
$taux_USD = 0.884 ;
$taux_GBP = 1.225 ;
$taux_SEK = 0.096 ;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Opérations sur les variables</title>
</head>
<body>
	<?php 
	echo '<p>Prix en euros : ',$prix_EUR,' €';
	echo '<p>Prix en dollats : ', round($prix_EUR / $taux_USD, 2), ' $';
	echo '<p>Prix en livres sterling : ',round($prix_EUR/$taux_GBP,2),' £';
	echo '<p>Prix en couronnes suédoises : ',round($prix_EUR/$taux_SEK,2),' SEK';
	?>
</body>
</html>