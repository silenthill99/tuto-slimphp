<?php

require_once __DIR__ . "/../auth/JwtManager.php";

/**@var PDO $db*/

require_once __DIR__ . "/../sql/connect.php";

if(empty($_POST['name']) || empty($_POST['surface_in_ha']) || $_POST['surface_in_ha'] == 0 || empty($_POST["description"])) {
    echo json_encode([
        'success' => false,
        'error' => "Veuillez remplir tous les champs"
    ]);
    exit;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$jwtManager = new JwtManager();

if (empty($authHeader)) {
    echo json_encode([
        'success' => false,
        'error' => "Token manquant"
    ]);
    exit;
}

$token = $jwtManager->extractTokenFromHeader($authHeader);
$userData = $jwtManager->validateToken($token);

if (empty($userData)) {
    echo json_encode([
        'success' => false,
        'error' => "Token invalide ou expiré"
    ]);
    exit;
}

$id = $userData["user_id"];
$name = $_POST['name'];
$surface = $_POST['surface_in_ha'];
$description = $_POST['description'];

try {
    /** @noinspection SqlNoDataSourceInspection */
    $sql = "INSERT INTO parcels (user_id, name, surface_in_ha, description) VALUES (:user_id, :name, :surface_in_ha, :description)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":user_id", $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':surface_in_ha', $surface);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
    echo json_encode([
        'success' => true,
        'message' => "Parcelle créée avec succès !"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la création de la parcelle : ' . $e->getMessage()
    ]);
}