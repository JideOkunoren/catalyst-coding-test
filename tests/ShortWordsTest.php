<?php
declare(strict_types=1);

namespace tests;

use Covers;
use PHPUnit\Framework\TestCase;
use App\Scripts\ShortWords;
use Test;

class ShortWordsTest extends TestCase
{

    #[Test]
    #[Covers(ShortWords::class)]
    public function testGetShortestWord(): void
    {
        $shortWords = new ShortWords();
        $this->assertEquals('get', $shortWords->getShortestWord('get the shortest word'));
    }
}
