<?php /** @noinspection SqlNoDataSourceInspection */

/**@var PDO $db*/
require_once __DIR__ . "/../sql/connect.php";
require_once "JwtManager.php";

header('Content-Type: application/json');

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $mailValue = htmlspecialchars($email);

    $sql = "SELECT * FROM users WHERE email = :email";
    $request = $db->prepare($sql);
    $request->execute(["email" => $mailValue]);

    $user = $request->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        echo json_encode(['success' => false, 'error' => "Identifiants invalides"]);
    } else {
        $jwtManager = new JwtManager();
        $token = $jwtManager->generateToken([
            'id' => $user["id"],
            'email' => $user["email"]
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user["id"],
                'email' => $user["email"]
            ]
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => "Email et mot de passe requis"]);
}
