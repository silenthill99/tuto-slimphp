<?php /** @noinspection SqlNoDataSourceInspection */

require_once "../sql/connect.php";
require_once "JwtManager.php";
/**@var PDO $db */
if ($_POST) {
    if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirmation']) && !empty($_POST['email_confirmation'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_confirmation = $_POST['password_confirmation'];
        $email_confirmation = $_POST['email_confirmation'];

        if ($email != $email_confirmation) {
            echo json_encode(['success' => false, 'message' => "Les email doivent être identiques !"]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => "Format d'email invalide"]);
            exit;
        }

        $sql = "SELECT COUNT(email) FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            echo json_encode(['success' => false, 'message' => 'Adresse mail incorrecte']);
            exit;
        }

        if ($password != $password_confirmation) {
            echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
            exit;
        }

        $pw_hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':password' => $pw_hashed
        ]);

//            echo json_encode(['success' => true, 'message' => 'Compte créé avec succès !']);
        $userId = $db->lastInsertId();

        $jwtManager = new JwtManager();
        $token = $jwtManager->generateToken([
            'id' => $userId,
            'email' => $email,
        ]);
        echo json_encode([
            'success' => true,
            'message' => 'Compte créé avec succès !',
            'token' => $token,
            'user' => [
                'id' => $userId,
                'email' => $email,
            ]
        ]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune donnée POST reçue']);
    }
}
