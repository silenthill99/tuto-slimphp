<?php /** @noinspection SqlNoDataSourceInspection */

header('Content-Type: application/json');
require_once __DIR__ . '/../sql/connect.php';
require_once __DIR__ . '/../auth/JwtManager.php';
/**@var PDO $db*/

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$jwtManager = new JwtManager();

if (empty($authHeader)) {
    http_response_code(401);
    exit(json_encode(["error" => "Token manquant"]));
}

$token = $jwtManager->extractTokenFromHeader($authHeader);
$userData = $jwtManager->validateToken($token);

if (!$userData) {
    http_response_code(401);
    exit(json_encode(["error" => "Token invalide"]));
}

$userId = $userData["user_id"];
$sql = "SELECT parcels.*, users.name as user_name, users.email FROM parcels INNER JOIN users ON parcels.user_id = users.id WHERE parcels.user_id = :user_id";

try {
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $parcels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($parcels);
} catch (PDOException $e) {
    http_response_code(500);
    exit(json_encode(["error" => "Erreur serveur"]));
}