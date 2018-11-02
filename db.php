<?php

$dbHost = 'localhost';
$dbName = 'stats';
$dbUser = 'root';
$dbPassword = "";

$db = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName) or die("Не возможно подключиться к БД");