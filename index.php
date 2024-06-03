<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\DatabaseConn;
use App\Controllers\CsvController;
use App\Models\CsvModel;
use App\Scripts\FooBar;
use App\Scripts\ShortWords;

/**
 * @return void
 */
function displayWelcomeMessage(): void
{
    echo <<<WELCOME
      Welcome to the Catalyst Coding Challenge.
      To run the scripts, use the following options:
      --foobar                Run a Logic Test in the style of "FizzBuzz"
      --short_words           Run a program to find & display the shortest word from each line of a text file
      --create_table          Build the MySQL users table and exit
      --dry_run               Run the CSV read script without altering the database
      -u                      MySQL username
      -p                      MySQL password
      -h                      MySQL host
      --help                  Display this help message
    
    Examples:
     php index.php --foobar
     php index.php --short_words --file path_to_file.txt
     php index.php --create_table -u user -p password -h localhost
     php index.php --file path_to__csv_file.csv --dry_run -u user -p password -h localhost
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
  --foobar                Run a Logic Test in the style of "FizzBuzz"
  --short_words           Run a program to find & display the shortest word from each line of a text file
  --create_table          Build the MySQL users table and exit
  --file [csv file name]  Parse the specified CSV file & save the data to the database
  --dry_run               Run the CSV read script without updating the database
  -u                      MySQL username
  -p                      MySQL password
  -h                      MySQL host
  --help                  Display this help message

Examples:
  php index.php --foobar
  php index.php --short_words --file path_to_file.txt
  php index.php --create_table -u user -p password -h localhost
  php index.php --file path_to_csv/foo.csv -u user -p password -h localhost
  php index.php --file path_to__csv/bar.csv --dry_run -u user -p password -h localhost
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


$options = getopt("u:p:h:", ["foobar", "short_words", "file:", "create_table", "dry_run", "help"]);

match (true) {

    isset($options['help']) || (empty($options) && $argc === 1) => displayWelcomeMessage(),
    isset($options['foobar']) => runFooBar(),
    isset($options['short_words']) && isset($options['file']) => runShortWords($options),
    isset($options['create_table']) => createDataBaseTable(),
    isset($options['dry_run']) || isset($options['file']) &&
    checkRequiredOptions(['u', 'p', 'h'], $options) => executeCsvTasks($options),
    default => displayHelpMessage()
};

function checkRequiredOptions(array $requiredOptions, array $options): bool
{
    foreach ($requiredOptions as $option) {
        if (!isset($options[$option])) {
            return false;
        }
    }
    return true;
}

function executeCsvTasks(array $options): void
{
    try {
        $db = initializeDatabaseConnection();
        $csvModel = new CsvModel($db);
        $csvController = new CsvController($csvModel, $options['file']);
        if (isset($options['dry_run'])) {
            echo "Dry Run executed. Database not altered." . PHP_EOL;
        } else {
            $csvController->writeToDatabase();
            echo count($csvController->getValidData()) . ' records were written to the MySQL users table successfully.' . PHP_EOL;
        }

    } catch (Exception $e) {
        echo "An Error occurred : " . $e->getMessage() . PHP_EOL;
    }
}

/**
 * @return void
 */
function runFooBar(): void
{
    $fizzBuzz = new FooBar();
    $fizzBuzz->runFooBar();
}

/**
 * @param $options
 * @return void
 */
function runShortWords($options): void
{
    try {
        $filename = $options['file'];
        $shortWords = new ShortWords();
        $shortWords->readFile($filename);
    } catch (Exception $e) {
        echo "An Error occurred : " . $e->getMessage() . PHP_EOL;
    }
}
