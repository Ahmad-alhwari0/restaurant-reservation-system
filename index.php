<?php
require_once 'includes/init.php';

if (Auth::isAdmin()) {
    header('Location: admin/index.php');
} elseif (Auth::isLoggedIn()) {
    header('Location: slots.php');
} else {
    header('Location: login.php');
}
exit;
