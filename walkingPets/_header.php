<?php
$animeaux = array();
$reqNoms = $bd->prepare('SELECT name FROM categories');
$reqNoms->execute();
while ($cat = $reqNoms->fetch()) {
    $animeaux[] = $cat['name'];
}
$ani = implode(" - ", $animeaux);
?>

<header>
    <h1>Walking Pets</h1>
    <p> <?php echo htmlspecialchars($ani); ?> </p>
    <p> L'équipe de <span class="bold">Walking Pets</span> s'occupe de vos animaux de compagnie. <br>
        Au menu: plusieurs promenades* et repas par jour.
    </p>
</header>