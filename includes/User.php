<?php


class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    public function register($name, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users(name,email,password) VALUES (? , ? , ?)");


        return $stmt->execute([$name, $email, $hash]);
    }

    public function login($email, $password)
    {

        $user = $this->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return 'Invalid email or password.';
        }
        if($user['is_active']==0){
            return 'Your account has been disabled.';
        }
        return $user;
    }

    public function countUsers(){
        $stmt=$this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
        return (int) $stmt->fetchColumn();
    }
}
