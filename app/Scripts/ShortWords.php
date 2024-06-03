<?php

namespace App\Scripts;

class ShortWords
{

    /**
     * @param string $filename
     * @return void
     */
    public function readFile(string $filename): void
    {
        $fileLines = file($filename, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

        if (!empty($fileLines)) {
            foreach ($fileLines as $line) {
                echo $this->getShortestWord($line) . PHP_EOL;
            }
        }
    }

    public function getShortestWord(string $line): string
    {
        $words = explode(' ', $line);
        $shortestWord = $words[0];
        foreach ($words as $word) {
            if (strlen($word) < strlen($shortestWord)) {
                $shortestWord = $word;
            }
        }
        return $shortestWord;
    }
}
