<?php
//valeur par défaut si pas de paramètre GET
$page = 1 ;	
//récupération du paramètre GET s'il existe et conversion (int) pour sécurité
if (isset($_GET['page'])) $page = (int) $_GET['page'] ;
if($page < 1 || $page > 3){
	$page=1;
	header('Location:index.php?page=1');
	exit;
	
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Paramètres GET</title>
	<style>
		#page1, #page1 * {color:red}
		#page2, #page2 *{color:blue}
		#page3, #page3 *{color:green}
		a{text-decoration:none}
	</style>
</head>
<body <?php echo "id=\"page$page\"" ?> >
	<h1>Page <?php echo $page ; ?>/3</h1>
	<nav>
		<ul>
			<li><a href="index.php?page=1">Page 1</a>
			<li><a href="index.php?page=2">Page 2</a>
			<li><a href="index.php?page=3">Page 3</a>
			<li>
				<?php
				$i = $page+1;
				echo "<a href=\"index.php?page=$i\"> Page Suivante </a>";
				?>
			</li>
			<li>
				<?php
				$i = $page-1;
				if($i == 0) $i=3;
				echo "<a href=\"index.php?page=$i\"> Page précédente </a>";
				?>
			</li>
		</ul>
	</nav>
</body>
</html>