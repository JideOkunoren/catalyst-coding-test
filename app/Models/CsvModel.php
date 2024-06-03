<?php

namespace App\Models;

use PDO;
use App\Config\DatabaseConn;

class CsvModel
{
 private PDO $pdo;

 /*
  *
  */
 public function __construct(DatabaseConn $dbConn)
 {
   $this->pdo = $dbConn->getPDO();
 }

    /**
     * @param array $userData
     * @return bool
     */
    public function insertData(array $userData): bool
    {
        if($this->emailExists($userData['email'])) {
             echo "Duplicate email found in database: " . $userData['email'] .PHP_EOL;
            return false;
        }

        $sql = "INSERT INTO users (name, surname, email) VALUES (:name, :surname, :email)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($userData);
    }

    /**
     * @param string $email
     * @return bool
     */
    private function emailExists(string $email): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }
}