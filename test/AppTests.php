<?php


use Battleship\Position;
use PHPUnit\Framework\TestCase;


class AppTests extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../src/App.php';
    }

    public function testParsePosition()
    {
        $actual = App::parsePosition("A1");
        $expected = new Position('A', 1);
        $this->assertEquals($expected, $actual);
    }

    public function testParsePosition2()
    {
        $expected = new Position('B', 1);
        $actual = App::parsePosition("B1");
        $this->assertEquals($expected, $actual);
    }
}
