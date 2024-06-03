<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

/**
 * @return void
 */
function displayWelcomeMessage(): void
{
    echo <<<WELCOME
      Welcome to the Catalyst Coding Challenge.
       
    WELCOME;
}

displayWelcomeMessage();