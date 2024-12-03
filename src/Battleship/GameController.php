<?php

namespace Battleship;

use InvalidArgumentException;

class GameController
{
    private static $playerHits = array();
    private static $computerHits = array();

    public static function checkIsHit(array $fleet, $shot)
    {
        if ($fleet == null) {
            throw new InvalidArgumentException("ships is null");
        }

        if ($shot == null) {
            throw new InvalidArgumentException("shot is null");
        }

        foreach ($fleet as $ship) {
            foreach ($ship->getPositions() as $position) {
                if ($position == $shot) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function initializeShips()
    {
        return Array(
            new Ship("Aircraft Carrier", 5, Color::CADET_BLUE),
            new Ship("Battleship", 4, Color::RED),
            new Ship("Submarine", 3, Color::CHARTREUSE),
            new Ship("Destroyer", 3, Color::YELLOW),
            new Ship("Patrol Boat", 2, Color::ORANGE));
    }

    public static function isShipValid($ship)
    {
        return count($ship->getPositions()) == $ship->getSize();
    }

    public static function getRandomPosition()
    {
        $rows = 8;
        $lines = 8;

        $letter = Letter::value(random_int(0, $lines - 1));
        $number = random_int(0, $rows - 1);

        return new Position($letter, $number);
    }

    public static function addHit($position, $isPlayer = true)
    {
        if ($isPlayer) {
            self::$playerHits[] = $position;
        } else {
            self::$computerHits[] = $position;
        }
    }

    public static function getSunkShips($fleet, $isPlayer = true)
    {
        $sunkShips = array();
        $hits = $isPlayer ? self::$playerHits : self::$computerHits;
        
        foreach ($fleet as $ship) {
            if ($ship->isSunk($hits)) {
                $sunkShips[] = $ship;
            }
        }
        return $sunkShips;
    }

    public static function getRemainingShips($fleet, $isPlayer = true)
    {
        $remaining = array();
        $hits = $isPlayer ? self::$playerHits : self::$computerHits;
        
        foreach ($fleet as $ship) {
            if (!$ship->isSunk($hits)) {
                $remaining[] = $ship;
            }
        }
        return $remaining;
    }
}