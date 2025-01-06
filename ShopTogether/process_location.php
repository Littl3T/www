<?php
header('Content-Type: application/json');
session_start();
// Vérifie si l'utilisateur est connecté
if (empty($_SESSION["login"])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non authentifié']);
    exit();
}
// Vérifie si les données sont envoyées via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "_connexionBD.php";
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['ID_shoppingList']) && isset($input['latitude']) && isset($input['longitude'])) {
        $idshop = (int) htmlspecialchars($input['ID_shoppingList']);
        $latitude = htmlspecialchars($input['latitude']);
        $longitude = htmlspecialchars($input['longitude']);

        $reqlocation = $bd->prepare('INSERT INTO shoppinglocation(ID_shopLocation,ID_shoppingList,Longitude,Latitude) VALUES (NULL,:idshop,:long,:lat)');
        $reqlocation->bindValue('idshop',$idshop);
        $reqlocation->bindvalue('long',$longitude);
        $reqlocation->bindvalue('lat',$latitude);
        $reqlocation->execute();
        echo json_encode(['status' => 'success', 'message' => 'Localisation enregistrée avec succès']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    exit();
}
?>
