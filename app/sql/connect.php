<?php

try {
    $DB_NAME = "flo";
    $DB_HOST = "127.0.0.1";
    $DB_PORT = 3306;
    $DB_USER = "homestead";
    $DB_PASSWORD = "secret";

    $db = new PDO("mysql:host=".$DB_HOST.";port:".$DB_PORT.";dbname:".$DB_NAME, $DB_USER, $DB_PASSWORD);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    echo "Connexion établie avec la BDD !";
    return $db;
} catch(PDOException $e) {
    return $e->getMessage();
}