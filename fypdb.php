<?php

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);

$active_group = 'default';
$query_builder = TRUE;

//connect to DB
$conn = mysqli_connect($server, $username, $password, $db);


?>