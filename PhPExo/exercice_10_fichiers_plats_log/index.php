<?php 
session_start();
$aprobation_list = array('mauvais', 'moyen', 'bon', 'trés bon');
$aprobation = $aprobation_list[1];
$errors = array();
$form_correct = False;
if(isset($_POST['form'])){

    if(!empty($_POST['pseudo']) && preg_match("/^[a-zA-Z0-9À-ÿ.'_-]+$/", $_POST['pseudo'])){
        $pseudo = trim(strip_tags($_POST['pseudo']));
    }
    else $errors['pseudo'] = TRUE;

    if (!empty(trim(strip_tags($_POST['email']))) && preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", trim(strip_tags($_POST['email'])))) {
        $email =trim(strip_tags($_POST['email']));
    }
    else $errors['email'] = TRUE;

    if (!empty($_POST['apreciation']) && in_array($_POST['apreciation'],$aprobation_list)){
        $aprobation = $_POST['apreciation'];
    }
    else $errors['apreciation'] = TRUE; 

    if(!empty(trim(strip_tags($_POST['comment'])))){
        $comment = trim(strip_tags($_POST['comment']));
    }
    else $comment = "";

    if (empty($errors)) { 
		$form_correct = True;
	}
}
?>

<?php
if($form_correct){
    $data = "$pseudo [$email]"."\n".
            "Appréciation : $aprobation"."\n".
            "\"$comment\""."\n".
            "-----------------------------------------------------"."\n";
    $fichier = fopen('C:\laragon\www\PhPExo\exercice_10_fichiers_plats_log\fichiers_plats\avis.txt', 'a+');
    fputs($fichier,$data);
    $aprobation = $aprobation_list[1];
    $email = "";
}
?>

<?php
if(file_exists('C:\laragon\www\PhPExo\exercice_10_fichiers_plats_log\fichiers_plats\avis.txt')){
    $comments_files_exist = True;
    $content = file_get_contents('C:\laragon\www\PhPExo\exercice_10_fichiers_plats_log\fichiers_plats\avis.txt');
    $content = str_replace("\n","<br>",$content);
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="author" content="Ivan Miller">
<title>Exercices PHP</title>
<style>
h1,h2,h3,p{
    user-select:none;
}
.red {
    color:red;
}
main, body {
    margin:0;
    padding:0;
    }
input, select, textarea{
    border:0;
}
main {
    margin:0;
    height:100vh;
    display: flex;
    flex-flow:column nowrap;
    justify-content: center;
    align-items: center;
    background-image:url(img/background2.jpg);
    background-size:cover;
    background-repeat: no-repeat, repeat;
    background-position: center top;

    font-family: Calibri, Helvetica, sans-serif;
}
#formulaire{
    backdrop-filter: blur(10px);
    padding:1rem;
    border-radius: 40px;
    -webkit-box-shadow: 3px 0px 83px -6px rgba(0,0,0,0.5);
    -moz-box-shadow: 3px 0px 83px -6px rgba(0,0,0,0.5);
    box-shadow: 3px 0px 83px -6px rgba(0,0,0,0.6);
}
footer{
    background-color:#47637B;
    height:max-content;
    color:white;
    padding:0.5rem;
    text-align:center;
    font-size:1.5rem;
}
footer h2{
    font-size:2rem;
    margin-bottom:3rem;
}
</style>
</head>
<body>
    <main>
        <div id="formulaire">
            <h1> Rate your friend!</h1>
            <p>Give us a feedback...</p>
            <form method="post" action="index.php" autocomplete="off">
                <p <?php if ( isset($errors['pseudo']) ) echo 'class="red"'; ?>>
                    <label for="pseudo" >Name : </label>
                    <input type="text" name="pseudo" id="pseudo" <?php if(!empty($_POST['pseudo']) && !empty($errors)){echo "value=\"$pseudo\"";} ?>>
                </p>
                <p <?php if ( isset($errors['email']) ) echo 'class="red"'; ?>>
                    <label for="email"> Email :  </label>
                    <input type="text" name="email" id="email" <?php if(!empty($_POST['email']) && !empty($email)){echo "value=\"$email\"";} ?>>
                </p>
                <p>
                    <label for="apreciation">Appreciation: </label>
                    <select id="apre" name="apreciation">
                        <?php
                        foreach ($aprobation_list as $a) {
                            echo '<option value="',$a,'" ', ($aprobation==$a)?'selected':'' ,'>',$a;
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <label for="comment">Tell us more about him/her</label> <br>
                    <textarea id="textarea" name="comment" rows="5" cols="30"><?php if(!empty($_POST['comment']) && !empty($errors)){echo "$comment";} ?></textarea>
                </p>
                <input type="submit" name="form" value="Send" id="submit">
            </form>
            <?php 
            if($form_correct){ echo "<h2>Thank you !</h2>";} else echo "<h3>Try again...</h3>"
            ?>
        </div>
    </main>
    <footer>
        <h2>Comments we got, maybe you'll find yourself...</h2>
        <p id="comments">
            <?php
            echo "$content"
            ?>
        </p>
    </footer>
</body>