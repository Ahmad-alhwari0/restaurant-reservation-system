<?php

require_once 'includes/init.php';
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;