<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class DatabaseConn
{
    private static ?DatabaseConn $instance = null;
    private PDO $pdo;

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     * @param PDO|null $pdo
     */
    public function __construct(string $dsn, string $username, string $password, array $options = [], PDO $pdo = null)
    {
        try {
            $this->pdo = $pdo ?? new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new PDOException('Database Connection Failed: ' . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * @param DatabaseConn|null $inst
     * @return void
     */
    public static function setInstance(?DatabaseConn $inst):void
    {
        self::$instance = $inst;
    }

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     * @return DatabaseConn
     */

    public static function getInstance(string $dsn, string $username, string $password, array $options = []): DatabaseConn
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseConn($dsn, $username, $password, $options);
            self::$instance->initializeDatabase('test_db');
        }
        return self::$instance;
    }

    /**
     * @param string $dbName
     * @return bool
     */
    public function initializeDatabase(string $dbName): bool
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100),
                surname VARCHAR(100),
                email VARCHAR(100) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            $this->pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName");
            $this->pdo->exec("USE $dbName");
            $this->pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            throw new PDOException('Database Creation Failed: ' . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
