<?php
    include "_bd.php"
?>
<?php
$id_pet = $_GET["id_pet"]
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WalkingPets</title>
</head>
<body>
    <?php
    echo $id_pet;
    ?>
</body>
</html>