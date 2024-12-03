<?php

use Battleship\GameController;
use Battleship\Position;
use Battleship\Letter;
use Battleship\Color;
use Battleship\Ship;

class App
{
    /**
     * @var array|Ship[]
     */
    private static $myFleet = array();
    /**
     * @var array|Ship[]
     */
    private static $enemyFleet = array();
    private static $console;

    static function run()
    {
        self::$console = new Console();
        self::$console->setForegroundColor(Color::MAGENTA);

        self::$console->println("                                     |__");
        self::$console->println("                                     |\\/");
        self::$console->println("                                     ---");
        self::$console->println("                                     / | [");
        self::$console->println("                              !      | |||");
        self::$console->println("                            _/|     _/|-++'");
        self::$console->println("                        +  +--|    |--|--|_ |-");
        self::$console->println("                     { /|__|  |/\\__|  |--- |||__/");
        self::$console->println("                    +---------------___[}-_===_.'____                 /\\");
        self::$console->println("                ____`-' ||___-{]_| _[}-  |     |_[___\\==--            \\/   _");
        self::$console->println(" __..._____--==/___]_|__|_____________________________[___\\==--____,------' .7");
        self::$console->println("|                        Welcome to Battleship                         BB-61/");
        self::$console->println(" \\_________________________________________________________________________|");
        self::$console->println();
        self::$console->resetForegroundColor();
        self::InitializeGame();
        self::StartGame();
    }

    public static function InitializeEnemyFleet()
    {
        self::$enemyFleet = GameController::initializeShips();

        self::$enemyFleet[0]->addPosition(new Position('B', 4));
        self::$enemyFleet[0]->addPosition(new Position('B', 5));
        self::$enemyFleet[0]->addPosition(new Position('B', 6));
        self::$enemyFleet[0]->addPosition(new Position('B', 7));
        self::$enemyFleet[0]->addPosition(new Position('B', 8));

        self::$enemyFleet[0]->addPosition(new Position('E', 6));
        self::$enemyFleet[0]->addPosition(new Position('E', 7));
        self::$enemyFleet[0]->addPosition(new Position('E', 8));
        self::$enemyFleet[0]->addPosition(new Position('E', 9));

        self::$enemyFleet[0]->addPosition(new Position('A', 3));
        self::$enemyFleet[0]->addPosition(new Position('B', 3));
        self::$enemyFleet[0]->addPosition(new Position('C', 3));

        self::$enemyFleet[1]->addPosition(new Position('F', 8));
        self::$enemyFleet[1]->addPosition(new Position('G', 8));
        self::$enemyFleet[1]->addPosition(new Position('H', 8));

        self::$enemyFleet[2]->addPosition(new Position('C', 5));
        self::$enemyFleet[2]->addPosition(new Position('C', 6));
    }

    public static function getRandomPosition()
    {
        $rows = 8;
        $lines = 8;

        $letter = Letter::value(random_int(0, $lines - 1));
        $number = random_int(0, $rows - 1);

        return new Position($letter, $number);
    }

    public static function InitializeMyFleet()
    {
        self::$myFleet = GameController::initializeShips();

        self::$console->setForegroundColor(Color::YELLOW);
        self::$console->println("=== FLEET POSITIONING ===");
        self::$console->println("Please position your fleet (Game board has size from A to H and 1 to 8):");

        foreach (self::$myFleet as $ship) {
            self::$console->println();
            self::$console->setForegroundColor($ship->getColor());
            printf("Positioning %s (size: %s)\n", $ship->getName(), $ship->getSize());

            for ($i = 1; $i <= $ship->getSize(); $i++) {
                self::$console->setForegroundColor(Color::DEFAULT_GREY);
                printf("Enter position %s of %s (i.e A3):", $i, $ship->getSize());

                self::$console->setForegroundColor(Color::DEFAULT_GREY);
                $input = readline("");
                $ship->addPosition($input);
            }
        }
    }

    public static function beep()
    {
        echo "\007";
    }

    public static function InitializeGame()
    {
        self::InitializeMyFleet();
        self::InitializeEnemyFleet();
    }

    public static function StartGame()
    {
        self::$console->setForegroundColor(Color::YELLOW);
        self::$console->println("\033[2J\033[;H");
        self::$console->println("                  __");
        self::$console->println("                 /  \\");
        self::$console->println("           .-.  |    |");
        self::$console->println("   *    _.-'  \\  \\__/");
        self::$console->println("    \\.-'       \\");
        self::$console->println("   /          _/");
        self::$console->println("  |      _  /\" \"");
        self::$console->println("  |     /_\'");
        self::$console->println("   \\    \\_/");
        self::$console->println("    \" \"\" \"\" \"\" \"");
        $userHitFields =[];
        while (true) {
            self::$console->setForegroundColor(Color::YELLOW);
            self::$console->println("\n=== PLAYER'S TURN ===");
            self::$console->println("Enter coordinates for your shot:");

            self::$console->setForegroundColor(Color::DEFAULT_GREY);
            $position = readline("");

            $isHit = self::getUserHit($userHitFields);

            if ($isHit) {
                self::beep();
                GameController::addHit(self::parsePosition($position), true);
                self::$console->setForegroundColor(Color::RED);
                self::$console->println("DIRECT HIT!");

                $sunkShips = GameController::getSunkShips(self::$enemyFleet, true);
                if (!empty($sunkShips)) {
                    self::$console->println("\nSunk enemy ships:");
                    foreach ($sunkShips as $ship) {
                        self::$console->println("- " . $ship->getName());
                    }
                }

                $remainingShips = GameController::getRemainingShips(self::$enemyFleet, true);
                if (!empty($remainingShips)) {
                    self::$console->println("\nRemaining enemy ships:");
                    foreach ($remainingShips as $ship) {
                        self::$console->println("- " . $ship->getName());
                    }
                }

                if (count($sunkShips) > count(GameController::getSunkShips(self::$enemyFleet, true)) - 1) {
                    self::$console->println("                \\         .  ./");
                    self::$console->println("              \\      .:\" \";'.:..\" \"   /");
                    self::$console->println("                  (M^^.^~~:.'\" \").");
                    self::$console->println("            -   (/  .    . . \\ \\)  -");
                    self::$console->println("               ((| :. ~ ^  :. .|))");
                    self::$console->println("            -   (\\- |  \\ /  |  /)  -");
                    self::$console->println("                 -\\  \\     /  /-");
                    self::$console->println("                   \\  \\   /  /");
                }
            } else {
                self::$console->setForegroundColor(Color::BLUE);
                self::$console->println("SPLASH! Miss...");
            }

            self::$console->setForegroundColor(Color::YELLOW);
            self::$console->println("\n=== COMPUTER'S TURN ===");

            $position = self::getRandomPosition();
            $isHit = GameController::checkIsHit(self::$myFleet, $position);

            self::$console->setForegroundColor($isHit ? Color::RED : Color::BLUE);
            printf("Computer shoots at %s%s - %s\n",
                $position->getColumn(),
                $position->getRow(),
                $isHit ? "HIT!" : "Miss"
            );

            if ($isHit) {
                self::beep();
                GameController::addHit($position, false);

                $sunkShips = GameController::getSunkShips(self::$myFleet, false);
                if (!empty($sunkShips)) {
                    self::$console->println("\nYour sunk ships:");
                    foreach ($sunkShips as $ship) {
                        self::$console->println("- " . $ship->getName());
                    }
                }

                $remainingShips = GameController::getRemainingShips(self::$myFleet, false);
                if (!empty($remainingShips)) {
                    self::$console->println("\nYour remaining ships:");
                    foreach ($remainingShips as $ship) {
                        self::$console->println("- " . $ship->getName());
                    }
                }

                if (count($sunkShips) > count(GameController::getSunkShips(self::$myFleet, false)) - 1) {
                    self::$console->println("                \\         .  ./");
                    self::$console->println("              \\      .:\" \";'.:..\" \"   /");
                    self::$console->println("                  (M^^.^~~:.'\" \").");
                    self::$console->println("            -   (/  .    . . \\ \\)  -");
                    self::$console->println("               ((| :. ~ ^  :. .|))");
                    self::$console->println("            -   (\\- |  \\ /  |  /)  -");
                    self::$console->println("                 -\\  \\     /  /-");
                    self::$console->println("                   \\  \\   /  /");
                }
            }

            self::$console->setForegroundColor(Color::YELLOW);
            self::$console->println("\n=== END OF ROUND ===");
            self::$console->setForegroundColor(Color::DEFAULT_GREY);
        }
    }
    /**
     * @throws Exception
     */
    public static function parsePosition($input): Position
    {
        $letter = substr($input, 0, 1);
        $number = substr($input, 1, 1);

        if(!is_numeric($number)) {
            throw new Exception("Not a number: $number");
        }

        return new Position($letter, $number);
    }
    /**
     * @throws Exception
     */
    private static function getUserHit(array &$userHitFields): bool
    {
        self::$console->setForegroundColor(Color::YELLOW);
        self::$console->println("Enter coordinates for your shot :");
        self::$console->setForegroundColor(Color::DEFAULT_GREY);

        $position = readline("");
        if(in_array($position, $userHitFields)){
            self::$console->println("You have already hit this field. Please enter another one.");
            return self::getUserHit($userHitFields);
        }

        try {
            $isHit = GameController::checkIsHit(self::$enemyFleet, self::parsePosition($position));
        } catch (Exception $e) {
            self::$console->println($e->getMessage());
            return self::getUserHit($userHitFields);
        }
        $userHitFields[] = $position;
        return $isHit;
    }
}
