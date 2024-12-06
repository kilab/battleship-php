<?php

namespace Battleship;

use InvalidArgumentException;

class GameController
{
    private static $playerHits = array();
    private static $computerHits = array();
    private static $playerSunkShips = array();
    private static $computerSunkShips = array();
    private static $computerShots = array();

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
        do {
            $letter = Letter::value(random_int(0, 7));
            $number = random_int(1, 8);
            $position = new Position($letter, $number);
        } while (in_array((string)$position, self::$computerShots));

        self::$computerShots[] = (string)$position;
        return $position;
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
        $hits = $isPlayer ? self::$playerHits : self::$computerHits;
        $sunkShipsArray = $isPlayer ? self::$playerSunkShips : self::$computerSunkShips;
        
        foreach ($fleet as $ship) {
            if ($ship->isSunk($hits) && !in_array($ship, $sunkShipsArray)) {
                if ($isPlayer) {
                    self::$playerSunkShips[] = $ship;
                } else {
                    self::$computerSunkShips[] = $ship;
                }
            }
        }
        
        return $isPlayer ? self::$playerSunkShips : self::$computerSunkShips;
    }

    public static function getRemainingShips($fleet, $isPlayer = true)
    {
        $sunkShips = self::getSunkShips($fleet, $isPlayer);
        return array_filter($fleet, function($ship) use ($sunkShips) {
            return !in_array($ship, $sunkShips);
        });
    }

    public static function resetGame()
    {
        self::$playerHits = array();
        self::$computerHits = array();
        self::$playerSunkShips = array();
        self::$computerSunkShips = array();
        self::$computerShots = array();
    }
}