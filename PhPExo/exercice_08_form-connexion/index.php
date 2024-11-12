<?php
session_start();	//démarrage de la session
//si le formulaire a été soumis...
if ( isset($_POST['connexion']) ) {
	//on réceptionne, on trime les chaînes et on hache le mot de passe
	$login = trim($_POST['login']) ;
	$password = SHA1( trim($_POST['password']) ) ;
	
	//si le login et le mot de passe (bill) sont bons... 
	if ( $login=='bill' and $password=='c692d6a10598e0a801576fdd4ecf3c37e45bfbc4') { 
		$_SESSION['nom']='Bill';	//on enregistre en SESSION
		$_SESSION['titre']='Monsieur'; //on enregistre en Session le titre
		$_SESSION['admin']=TRUE;
		header('Location:index.php');	//on redirige pour vider $_POST
		exit();
	}
	elseif($login=='bob' and $password== SHA1("bob")){
		$_SESSION['nom']='Bob';	//on enregistre en SESSION
		$_SESSION['titre']='Monsieur'; //on enregistre en Session le titre
		$_SESSION['admin']=False;
		header('Location:index.php');	//on redirige pour vider $_POST
		exit();
	}
	elseif($login=='betty' and $password== SHA1("betty")){
		$_SESSION['nom']='Betty';	//on enregistre en SESSION
		$_SESSION['titre']='Madame'; //on enregistre en Session le titre
		$_SESSION['admin']=True;
		header('Location:index.php');	//on redirige pour vider $_POST
		exit();
	}
	else sleep(1);
}
else if ( isset($_POST['deconnexion']) ) {
	session_destroy() ;		//on détruit la session
	header('Location:index.php');	//on redirige pour vider $_POST
	exit();
}
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Formulaire de connexion</title>
	<style>
		h1{
			font-size:2.5rem
		}
		p {
			font-size:1.5rem
		}
		body{
			margin:0;
			height:100vh;
			display: flex;
			justify-content: center;
			align-items: center;
			background-image:url(background.JPG);
			background-size:cover;
			background-repeat: no-repeat, repeat;
			background-position: center top;
		}
		#logindiv{
			font-family: Calibri, Helvetica, sans-serif;
			-webkit-box-shadow: 3px 0px 83px -6px rgba(0,0,0,0.5);
			-moz-box-shadow: 3px 0px 83px -6px rgba(0,0,0,0.5);
			box-shadow: 3px 0px 83px -6px rgba(0,0,0,0.6);

			display: flex;
			flex-flow: column nowrap;
			justify-content: center;
			align-items: center;
			background-color: #FFF;
			width: 18rem;
			border-radius: 40px;
		}
		.sub{
			font-size:1rem
		}
		.sub:hover{
			background-color:#BBB
		}
	</style>
</head>
<body>
	<div id="logindiv">
		<?php
		if ( empty($_SESSION) ) {
			?>
			<h1>Connexion</h1>
			<form method="post" action="index.php">
				<p><label for="login">Identifiant :<br> 
					<input type="text" name="login" id="login">
				</label>
				<p><label for="password">Mot de passe :<br> 
					<input type="password" name="password" id="password">
				</label>
				<p><input type="submit" name="connexion" value="Connexion" class="sub">
			</form>
			<?php
		}
		else {
			?>
			<h1>Déconnexion</h1>
			<?php
			if($_SESSION['admin']){
				echo "<p> Bonjour ", $_SESSION['titre'];
			}
			?>
			<form method="post" action="index.php">
				<p><input type="submit" name="deconnexion" value="Déconnexion"  class="sub">
			</form>
			<?php
		}
		?>
	</div>
</body>
</html>