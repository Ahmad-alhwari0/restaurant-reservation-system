<?php

session_start();
date_default_timezone_set('Asia/Amman');


require_once __DIR__. '/../config/db.php';
require_once __DIR__. '/Database.php' ;
require_once __DIR__. '/User.php';
require_once __DIR__.'/Auth.php';
require_once __DIR__.'/TimeSlot.php';



$db = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$pdo=$db->getConnection();