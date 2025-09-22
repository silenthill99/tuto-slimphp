<?php /** @noinspection SqlNoDataSourceInspection */

var_dump($_POST);
exit;

require_once "../sql/connect.php";
/**@var PDO $db */
if ($_POST) {
    if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirmation']) &&!empty($_POST['email_confirmation']) && !empty($_POST['pseudo'])) {
        $errors = [];

        $pseudo = htmlspecialchars($_POST['pseudo']);
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_confirmation = $_POST['password_confirmation'];
        $email_confirmation = $_POST['email_confirmation'];

        $sql = "SELECT COUNT(pseudo) FROM users WHERE pseudo = :pseudo";
        $stmt = $db->prepare($sql);
        $stmt->execute([':pseudo' => $pseudo]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errors[] = "Pseudo déjà utilisé";
        }

        if ($email != $email_confirmation) {
            $errors[] = "Les email doivent être identiques !";
        }

        $sql = "SELECT COUNT(email) FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $errors[] = "Adresse mail incorrecte";
        }

        if ($password != $password_confirmation) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }

        if (empty($errors)) {
            $pw_hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (email, pseudo, password) VALUES (:email, :pseudo, :password)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':pseudo' => $pseudo,
                ':password' => $pw_hashed
            ]);

            echo json_encode(['success' => true, 'message' => 'Compte créé avec succès !']);
        } else {
            echo json_encode(['success' => false, 'message' => $errors]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucune donnée POST reçue']);
    }
}
