<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Tirages du Lotto</title>
	<style>
	table {text-align:center;}
	table td {border:1px solid #000; padding:0.25rem;}
	</style>
</head>
<body>
	<h1>Tirages du Lotto</h1>
	<?php 
	for ($i=0 ; $i<6 ; $i++) {
		$tirage[$i] = rand(1,50);
	}
	?>
	<table>
		<tr>
		<?php
		sort($tirage);
		foreach($tirage as $t){
			echo "<td style=\"text-align:center;width:100px ; height:100px; font-size:3rem\"> $t </td>";
		}
		?>
		</tr>
	</table>
</body>
</html>