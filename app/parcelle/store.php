<?php

require_once __DIR__ . "/../auth/JwtManager.php";

/**@var PDO $db*/

require_once __DIR__ . "/../sql/connect.php";

// Debug: afficher les données reçues
error_log("POST data: " . print_r($_POST, true));

if(empty($_POST['name']) || empty($_POST['surface_in_ha']) || $_POST['surface_in_ha'] == 0 || empty($_POST["description"])) {
    echo json_encode([
        'success' => false,
        'error' => "Veuillez remplir tous les champs",
        'debug_post' => $_POST
    ]);
    exit;
}

// Récupérer le header Authorization (compatible avec tous les hébergeurs)
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
error_log("Authorization header: " . $authHeader);
$jwtManager = new JwtManager();

if (empty($authHeader)) {
    echo json_encode([
        'success' => false,
        'error' => "Token manquant",
        'debug_server' => [
            'HTTP_AUTHORIZATION' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'not set',
            'REDIRECT_HTTP_AUTHORIZATION' => $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 'not set'
        ]
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

    $parcelId = $db->lastInsertId();
    error_log("Parcelle créée avec succès - ID: " . $parcelId);

    echo json_encode([
        'success' => true,
        'message' => "Parcelle créée avec succès !",
        'parcel_id' => $parcelId,
        'data' => [
            'name' => $name,
            'surface_in_ha' => $surface,
            'description' => $description
        ]
    ]);
} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la création de la parcelle : ' . $e->getMessage()
    ]);
}