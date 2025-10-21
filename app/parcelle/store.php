<?php

/**@var PDO $db*/

require_once __DIR__ . "/../sql/connect.php";

if(empty($_POST('name')) || empty($_POST('surface_in_ha')) || $_POST('surface_in_ha') === 0 || empty($_POST("description"))) exit;

$name = $_POST('name');
$surface = $_POST('surface_in_ha');
$description = $_POST('description');

/** @noinspection SqlNoDataSourceInspection */
$sql = "INSERT INTO parcels (name, surface_in_ha) VALUES (:name, :surface_in_ha, :description)";
$stmt = $db->prepare($sql);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':surface_in_ha', $surface);
$stmt->bindParam(':description', $description);

$stmt->execute();