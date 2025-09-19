<?php

session_start();
/**@var PDO $db*/
require_once "../sql/connect.php";
$error = "";

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $mailValue = htmlspecialchars($email);

    $sql = "SELECT * FROM users WHERE email = :email";
    $request = $db->prepare($sql);
    $request->execute(["email" => $mailValue]);

    $user = $request->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        $error = "Identifiants invalides";
    } else {
        $_SESSION['user_id'] = $user['id'];
    }
} else {
    $error = "Email et mot de passe requis";
}

header('Content-Type: application/json');
if ($error) {
    echo json_encode(['success' => false, 'message' => $error]);
} else {
    echo json_encode(['success' => true, 'loggedIn' => true]);
}
