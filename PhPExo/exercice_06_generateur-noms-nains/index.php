<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>Générateur de noms de nains</title>
	<style>
	@font-face { 
		font-family:"ARJulian"; 
		src:url("ar-julian.ttf"); 
	}

	body { font-family:"ARJulian", Arial;
			background-color: #159 }
	.couleur0 {color: blue }
	.couleur1 {color: red}
	div{
		background-color : grey;
		box-shadow: 2px 2px 20px 23px #111;
	}
	ul{	
		column-count: auto;
		column-width: 8rem;}
	</style>
</head>
<body>
	<h1>Noms de nains</h1>
	<?php
	$noms=array();
	$nain_debut=array('A','Ba','Bo','Bu','Bra','Bre','	Bro','Da','Dra','Dro','Du','Ga','Go','Gu','Gra','Gri','Gro','I','Ka','Ko','Ku','O','U','Ta','Ti','To','Tu');
	$nain_liaison=array('ka','ko','la','lo','ra','rba','ro','rbo');
	$nain_fin=array('ban','dar','dir','dor','dur','gal','gan','gar','gor','grim','gur','kan','lan','lar','lek','li','lin','lion','lir','rak','ran','rek','rgrim','rgor','rik','ril','rion','rok','ron','tar','trek','tron');
	for($i=0;$i<20;$i++){
		$noms[$i]=$nain_debut[array_rand($nain_debut)];
		while(rand(0,10)<4){
			$noms[$i].=$nain_liaison[array_rand($nain_liaison)];
		}
		$noms[$i].= $nain_fin[array_rand($nain_fin)];
		if( in_array($noms[$i],array_slice($noms,0,count($noms)-1))){
			array_pop($noms);
			$i--;
		}
	}
	sort($noms);
	?>
	<div>
		<ul>
			<?php
			foreach($noms as $key=>$val){
				if($key%2 == 0){
					$couleur = 'couleur0';
				}
				else $couleur = 'couleur1';
				echo "<li class= \"$couleur\"> $val";
			}
			?>
		</ul>
	</div>
</body>
</html>