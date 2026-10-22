<?php

session_start();


require_once __DIR__. '/../config/db.php';
require_once __DIR__. '/Database.php' ;


$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$pdo=$db->getConnection();