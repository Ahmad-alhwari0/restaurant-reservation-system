<?php


class Auth
{
    public static function isLoggedIn()
    {

        return isset($_SESSION['user_id']);
    }

    public static function isAdmin()
    {
        return self::isLoggedIn() && $_SESSION['user_role'] === 'admin';
    }
    public static function requireLogin($basePath = '')
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . $basePath . 'login.php');
            exit;
        }
    }
    public static function requireAdmin($basePath = '')
    {
        self::requireLogin($basePath);
        if (!self::isAdmin()) {
            http_response_code(403);
            die('Access denied. Admins only.');
        }
    }
}
