<?php

$driver = 'mysql';
$host = $configDB['host'];
$db_name = $configDB['db_name'];
$db_user = $configDB['db_user'];
$db_pass = $configDB['db_pass'];
$charset = 'utf8mb4';
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
	$db = new PDO("$driver:host=$host;dbname=$db_name;charset=$charset",$db_user,$db_pass,$options);
} catch(PDOException $e) {
	exit('Ошибка подключения к базе данных'.$e);
}