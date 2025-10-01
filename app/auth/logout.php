<?php

session_start();

// Détruire la session
$_SESSION = [];
session_destroy();

//Supprimer le cookie de connexion
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

echo json_encode(["success" => "Déconnexion réussie"]);