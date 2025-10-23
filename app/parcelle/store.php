<?php

/**@var PDO $db*/
session_start();

require_once __DIR__ . "/../sql/connect.php";

if(empty($_POST('name')) || empty($_POST('surface_in_ha')) || $_POST('surface_in_ha') == 0 || empty($_POST("description"))) exit;

$id = $_SESSION('id');
$name = $_POST('name');
$surface = $_POST('surface_in_ha');
$description = $_POST('description');

/** @noinspection SqlNoDataSourceInspection */
$sql = "INSERT INTO parcels (user_id, name, surface_in_ha) VALUES (:user_id, :name, :surface_in_ha, :description)";
$stmt = $db->prepare($sql);
$stmt->bindParam(":user_id", $id);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':surface_in_ha', $surface);
$stmt->bindParam(':description', $description);

$stmt->execute();