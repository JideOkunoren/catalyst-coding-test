<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\DatabaseConn;


/**
 * @return void
 */
function displayWelcomeMessage(): void
{
    echo <<<WELCOME
      Welcome to the Catalyst Coding Challenge.
      To run the scripts, use the following options:
      --create_table          Build the MySQL users table and exit
      -u                      MySQL username
      -p                      MySQL password
      -h                      MySQL host
      --help                  Display this help message
    
    Examples:
     php index.php --create_table -u user -p password -h localhost
     php index.php --help
            
    WELCOME;
}

/**
 * @return void
 */
function displayHelpMessage(): void
{
    echo <<<HELP
Usage: php index.php [options]

Options:
  --create_table          Build the MySQL users table and exit
  -u                      MySQL username
  -p                      MySQL password
  -h                      MySQL host
  --help                  Display this help message

Examples:
  php index.php --create_table -u user -p password -h localhost
     php index.php --help  
HELP;
}


/**
 * @return DatabaseConn
 */
function initializeDatabaseConnection(): DatabaseConn
{
    $dbHost = $_SERVER['DB_HOST'] ?? 'localhost';
    $dbUser = $_SERVER['DB_USER'] ?? 'root';
    $dbPass = $_SERVER['DB_PASSWORD'] ?? '';
    $dbName = $_SERVER['DB_DATABASE'] ?? '';

    return DatabaseConn::getInstance("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass);
}

/**
 * @return void
 */
function createDataBaseTable(): void
{
    try {
        $db = initializeDatabaseConnection();
        $db->initializeDatabase('test_db');
        echo "MySQL users table created successfully." . PHP_EOL;
    } catch (PDOException $e) {
        echo "Error creating MySQL users table: " . $e->getMessage() . PHP_EOL;
    }
    exit();
}


$options = getopt("u:p:h:", ["create_table:", "help:"]);

match (true) {
    isset($options['help']) || (empty($options) && $argc === 1) => displayWelcomeMessage(),
    isset($options['create_table']) => createDataBaseTable($options),
    default => displayHelpMessage()
};
