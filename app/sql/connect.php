<?php

require_once "../../config/env.php";

try {
    $DB_NAME = $_ENV["DB_NAME"];
    $DB_HOST = $_ENV["DB_HOST"];
    $DB_PORT = $_ENV["DB_PORT"];
    $DB_USER = $_ENV["DB_USER"];
    $DB_PASSWORD = $_ENV["DB_PASSWORD"];

    $db = new PDO("mysql:host=".$DB_HOST.";port=".$DB_PORT.";dbname=".$DB_NAME, $DB_USER, $DB_PASSWORD);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // echo "Connexion établie avec la BDD !"; // À commenter en production
    return $db;
} catch(PDOException $e) {
    return $e->getMessage();
}