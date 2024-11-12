<?php include "_bd.php" ?>
<?php
session_start();
$id_pet = (int)$_GET["id_pet"];

$reqinfos=$bd->prepare('SELECT p.id_pet,p.name,p.walks,p.meals,p.comment,c.max_walks FROM pets AS p LEFT JOIN categories AS c ON p.id_cat = c.id_cat WHERE p.id_pet=:id;');
$reqinfos->bindValue(':id', $id_pet);
$reqinfos->execute();
$count = $reqinfos->rowCount();
$infos=$reqinfos->fetch();

if ( !empty($infos) and $count === 1 ) { 
    $pet_name = $infos['name'];
    $pet_walks =(int) $infos['walks'];
    $pet_meals =(int) $infos['meals'];
    $pet_comment = $infos['comment'];
    $pet_max_walks =(int) $infos['max_walks'];
} else{
    header("Location:index.php");
};
$reqinfos->closeCursor();
?>

<?php
if (isset($_POST['save'])) {
    $walks = (int) $_POST['walks'];
    $meals = (int) $_POST['meals'];
    $comment = $_POST['comment'];
    $reqUpdate=$bd->prepare('UPDATE pets AS p SET p.walks=:walks,p.meals=:meals,p.comment=:comment WHERE id_pet=:id_pet;');
    $reqUpdate->bindvalue(':walks',$walks);
    $reqUpdate->bindvalue(':meals',$meals);
    $reqUpdate->bindvalue(':comment',$comment);
    $reqUpdate->bindvalue(':id_pet',$id_pet);
    $reqUpdate->execute();
    $_SESSION['pets'][$id_pet]=true;
    header("Location:index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WalkingPets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body id="form_body">
    <?php include "_header.php"; ?>
    <div id="form">
        <img id="leash" src="icons\leash.png" alt="Leash Wlaking Pets">
        <?php
        echo "<h3> $pet_name </h3>";
        ?>
        <form action="<?php echo "modif_pet.php?id_pet="."$id_pet"; ?>" method="post">
            <label for="walks">walks: </label> 
                <input type="number" id="walks" name="walks" step="1" min="0" value="<?php if (isset($pet_walks)) echo $pet_walks; ?>">
            <br>
            <label for="meals">meals: </label> 
                <input type="number" id="meals" name="meals" step="1" min="0" value="<?php if (isset($pet_meals)) echo $pet_meals; ?>">
            <br>
            <label for="comment">comment: </label>
    <textarea name="comment" id="comment" col="33" rows="5"><?php if (isset($pet_comment)) echo $pet_comment; ?></textarea>
            <br>
            <button type="submit" name="save" value="save">Enregistrement</button>
        </form>
    </div>
    <p id="comment">*Seuls les chiens et les chats ont droit aux promenades avec au maximum 2 promenades par jours pour les chiens et 1 pour les chats.</p>
</body>
</html>