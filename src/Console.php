<?php

use Battleship\Color;

class Console
{
    function resetForegroundColor()
    {
        echo(Color::DEFAULT_GREY);
    }

    function setForegroundColor($color)
    {
        echo($color);
    }

    function println($line = "")
    {
        echo "$line\n";
    }
}
