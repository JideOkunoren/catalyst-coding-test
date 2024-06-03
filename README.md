# Catalyst coding Test


## Overview

This is Repo is in conjunction with Catalyst's Programming Evaluation for PHP & consists of a script task & a logic task.


## Setup
 
- Clone the repo from https://github.com/JideOkunoren/catalyst-coding-test.git
- The repo has a Dockerfile & a docker-compose.yml which will spin up a mysql container & php 8.1
  (composer installation is also automated). This is the easiest way to spin up an environment.
- For ease of use, the app/assets folder has a `users.csv` file which can be supplied as the --file param i.e.
  `php index.php --file app/assets/users.csv -u root -p test -h localhost`
- Otherwise, if docker isn't preferable:
  - modify the parameters in  index.php ->  `initializeDatabaseConnection`  to match the params for your Mysql Db
  - run composer install

## Running the scripts
-  From the root directory, run php index.php
- This should display the following UI
```
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
 
```

## Running the tests
There are 2 tests suites in the `tests` directory
- From the root directory run ``vendor\bin\phpunit`` to run the unit tests
