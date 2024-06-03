<?php
declare(strict_types=1);

namespace App\Scripts;

class FooBar
{
    public function runFooBar(): void
    {
        for ($x = 1; $x <= 100; $x++) {
            $divBy3 = $x % 3 === 0;
            $divBy5 = $x % 5 === 0;

            $output = match (true) {
                $divBy3 && $divBy5 => 'foobar',
                $divBy3 => 'foo',
                $divBy5 => 'bar',
                default => '',
            };
            $result = $output ? $output : $x;
            echo $result . PHP_EOL;
        }
    }
}
