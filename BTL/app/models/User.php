<?php

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM users
                WHERE username = :username
                AND status = 1
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ":username" => $username
        ]);

        return $stmt->fetch();
    }
}