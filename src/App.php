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

    static function run($args)
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

        GameController::resetGame();
        self::InitializeEnemyFleet();

        if ($args instanceof \Composer\Script\Event) {
            $args = $args->getArguments();
        }

        if (in_array('--debug', $args)) {
            self::showEnemyFleet();
        }

        self::InitializeMyFleet();
        self::StartGame();
    }

    private static function showEnemyFleet()
    {
        self::$console->setForegroundColor(Color::CADET_BLUE);
        self::$console->println("=== ENEMY FLEET POSITIONS ===");
        foreach (self::$enemyFleet as $ship) {
            self::$console->println($ship->getName() . ":");
            foreach ($ship->getPositions() as $position) {
                self::$console->println("- " . $position->getColumn() . $position->getRow());
            }
        }
        self::$console->resetForegroundColor();
    }

    public static function InitializeEnemyFleet()
    {
        $fleetConfigurations = [
            [
                [new Position('B', 4), new Position('B', 5), new Position('B', 6), new Position('B', 7), new Position('B', 8)],
                [new Position('E', 5), new Position('E', 6), new Position('E', 7), new Position('E', 8)],
                [new Position('A', 3), new Position('B', 3), new Position('C', 3)],
                [new Position('F', 8), new Position('G', 8), new Position('H', 8)],
                [new Position('C', 5), new Position('C', 6)]
            ],
            [
                [new Position('A', 1), new Position('A', 2), new Position('A', 3), new Position('A', 4), new Position('A', 5)],
                [new Position('D', 2), new Position('D', 3), new Position('D', 4), new Position('D', 5)],
                [new Position('G', 1), new Position('G', 2), new Position('G', 3)],
                [new Position('H', 5), new Position('H', 6), new Position('H', 7)],
                [new Position('E', 8), new Position('F', 8)]
            ],
            [
                [new Position('C', 1), new Position('C', 2), new Position('C', 3), new Position('C', 4), new Position('C', 5)],
                [new Position('F', 3), new Position('F', 4), new Position('F', 5), new Position('F', 6)],
                [new Position('B', 7), new Position('C', 7), new Position('D', 7)],
                [new Position('E', 1), new Position('F', 1), new Position('G', 1)],
                [new Position('H', 3), new Position('H', 4)]
            ],
            [
                [new Position('D', 5), new Position('D', 6), new Position('D', 7), new Position('D', 8), new Position('D', 9)],
                [new Position('A', 6), new Position('A', 7), new Position('A', 8), new Position('A', 9)],
                [new Position('F', 2), new Position('G', 2), new Position('H', 2)],
                [new Position('B', 4), new Position('C', 4), new Position('D', 4)],
                [new Position('E', 7), new Position('F', 7)]
            ],
            [
                [new Position('G', 3), new Position('G', 4), new Position('G', 5), new Position('G', 6), new Position('G', 7)],
                [new Position('B', 1), new Position('B', 2), new Position('B', 3), new Position('B', 4)],
                [new Position('E', 5), new Position('F', 5), new Position('G', 5)],
                [new Position('C', 8), new Position('D', 8), new Position('E', 8)],
                [new Position('H', 1), new Position('H', 2)]
            ]
        ];

        $selectedConfiguration = $fleetConfigurations[array_rand($fleetConfigurations)];

        self::$enemyFleet = GameController::initializeShips();

        foreach (self::$enemyFleet as $index => $ship) {
            foreach ($selectedConfiguration[$index] as $position) {
                $ship->addPosition($position);
            }
        }
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
                $position = null;
                while ($position === null) {
                    self::$console->setForegroundColor(Color::DEFAULT_GREY);
                    printf("Enter position %s of %s (i.e A3):", $i, $ship->getSize());

                    $input = readline("");

                    try {
                        $position = self::parsePosition($input);
                        $ship->addPosition((string)$position, self::$myFleet);
                    } catch (\Exception $e) {
                        self::$console->setForegroundColor(Color::RED);
                        self::$console->println("Error: " . $e->getMessage());
                        $position = null;
                        continue;
                    }
                }
            }
        }
    }

    public static function beep()
    {
        echo "\007";
    }

    public static function InitializeGame()
    {
        GameController::resetGame();
        self::InitializeEnemyFleet();
        self::InitializeMyFleet();
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

        while (true) {
            self::$console->setForegroundColor(Color::YELLOW);
            self::$console->println("\n=== PLAYER'S TURN ===");

            $position = null;
            while ($position === null) {
                self::$console->setForegroundColor(Color::DEFAULT_GREY);
                self::$console->println("Enter coordinates for your shot (A-H + 1-8, e.g. A5):");
                $input = readline("");

                try {
                    $position = self::parsePosition($input);
                } catch (\Exception $e) {
                    self::$console->setForegroundColor(Color::RED);
                    self::$console->println("Error: " . $e->getMessage());
                    self::$console->setForegroundColor(Color::YELLOW);
                    continue;
                }
            }

            $isHit = GameController::checkIsHit(self::$enemyFleet, self::parsePosition($position));

            if ($isHit) {
                self::beep();
                $previousSunkShips = GameController::getSunkShips(self::$enemyFleet, true);
                $previousCount = count($previousSunkShips);
                GameController::addHit($position, true);
                $currentSunkShips = GameController::getSunkShips(self::$enemyFleet, true);
                $currentCount = count($currentSunkShips);

                self::$console->setForegroundColor(Color::RED);
                self::$console->println("DIRECT HIT!");

                if ($currentCount > $previousCount) {
                    $justSunkShip = end($currentSunkShips);
                    self::$console->println(sprintf("\nYou just sunk the enemy's %s!", $justSunkShip->getName()));

                    $remainingShips = GameController::getRemainingShips(self::$enemyFleet, true);
                    if (!empty($remainingShips)) {
                        self::$console->println("\nRemaining enemy ships:");
                        foreach ($remainingShips as $ship) {
                            self::$console->println("- " . $ship->getName());
                        }
                    }
                }
            } else {
                self::$console->setForegroundColor(Color::BLUE);
                self::$console->println("SPLASH! Miss...");
            }

            if (count(GameController::getSunkShips(self::$enemyFleet, true)) == count(self::$enemyFleet)) {
                self::$console->setForegroundColor(Color::CHARTREUSE);
                self::$console->println("\n=== GAME OVER ===");
                self::$console->println("~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~     ▓██   ██▓ ▒█████   █    ██      ██████  █    ██  ███▄    █  ██ ▄█▀     ~");
                self::$console->println("~      ▒██  ██▒▒██▒  ██▒ ██  ▓██▒   ▒██    ▒  ██  ▓██▒ ██ ▀█   █  ██▄█▒      ~");
                self::$console->println("~       ▒██ ██░▒██░  ██▒▓██  ▒██░   ░ ▓██▄   ▓██  ▒██░▓██  ▀█ ██▒▓███▄░      ~");
                self::$console->println("~       ░ ▐██▓░▒██   ██░▓▓█  ░██░     ▒   ██▒▓▓█  ░██░▓██▒  ▐▌██▒▓██ █▄      ~");
                self::$console->println("~       ░ ██▒▓░░ ████▓▒░▒▒█████▓    ▒██████▒▒▒▒█████▓ ▒██░   ▓██░▒██▒ █▄     ~");
                self::$console->println("~        ██▒▒▒ ░ ▒░▒░▒░ ░▒▓▒ ▒ ▒    ▒ ▒▓▒ ▒ ░░▒▓▒ ▒ ▒ ░ ▒░   ▒ ▒ ▒ ▒▒ ▓▒     ~");
                self::$console->println("~      ▓██ ░▒░   ░ ▒ ▒░ ░░▒░ ░ ░    ░ ░▒  ░ ░░░▒░ ░ ░ ░ ░░   ░ ▒░░ ░▒ ▒░     ~");
                self::$console->println("~      ▒ ▒ ░░  ░ ░ ░ ▒   ░░░ ░ ░    ░  ░  ░   ░░░ ░ ░    ░   ░ ░ ░ ░░ ░      ~");
                self::$console->println("~      ░ ░         ░ ░     ░              ░     ░              ░ ░  ░        ~");
                self::$console->println("~      ░ ░                                                                   ~");
                self::$console->println("~        ▄▄▄█████▓ ██░ ██  ▄▄▄      ▄▄▄█████▓    ███▄ ▄███▓  █████▒          ~");
                self::$console->println("~        ▓  ██▒ ▓▒▓██░ ██▒▒████▄    ▓  ██▒ ▓▒   ▓██▒▀█▀ ██▒▓██   ▒           ~");
                self::$console->println("~        ▒ ▓██░ ▒░▒██▀▀██░▒██  ▀█▄  ▒ ▓██░ ▒░   ▓██    ▓██░▒████ ░           ~");
                self::$console->println("~        ░ ▓██▓ ░ ░▓█ ░██ ░██▄▄▄▄██ ░ ▓██▓ ░    ▒██    ▒██ ░▓█▒  ░           ~");
                self::$console->println("~          ▒██▒ ░ ░▓█▒░██▓ ▓█   ▓██▒  ▒██▒ ░    ▒██▒   ░██▒░▒█░              ~");
                self::$console->println("~          ▒ ░░    ▒ ░░▒░▒ ▒▒   ▓▒█░  ▒ ░░      ░ ▒░   ░  ░ ▒ ░              ~");
                self::$console->println("~            ░     ▒ ░▒░ ░  ▒   ▒▒ ░    ░       ░  ░      ░ ░                ~");
                self::$console->println("~          ░       ░  ░░ ░  ░   ▒     ░         ░      ░    ░ ░              ~");
                self::$console->println("~                  ░  ░  ░      ░  ░                   ░                     ~");
                self::$console->println("~                    ▓█████▄  ▒█████   █     █░ ███▄    █                    ~");
                self::$console->println("~                    ▒██▀ ██▌▒██▒  ██▒▓█░ █ ░█░ ██ ▀█   █                    ~");
                self::$console->println("~                    ░██   █▌▒██░  ██▒▒█░ █ ░█ ▓██  ▀█ ██▒                   ~");
                self::$console->println("~                    ░▓█▄   ▌▒██   ██░░█░ █ ░█ ▓██▒  ▐▌██▒                   ~");
                self::$console->println("~                    ░▒████▓ ░ ████▓▒░░░██▒██▓ ▒██░   ▓██░                   ~");
                self::$console->println("~                     ▒▒▓  ▒ ░ ▒░▒░▒░ ░ ▓░▒ ▒  ░ ▒░   ▒ ▒                    ~");
                self::$console->println("~                     ░ ▒  ▒   ░ ▒ ▒░   ▒ ░ ░  ░ ░░   ░ ▒░                   ~");
                self::$console->println("~                     ░ ░  ░ ░ ░ ░ ▒    ░   ░     ░   ░ ░                    ~");
                self::$console->println("~                       ░        ░ ░      ░             ░                    ~");
                self::$console->println("~                     ░                                                      ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~                                                                            ~");
                self::$console->println("~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~");

                self::$console->println("You are the winner!");
                break;
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
                $previousSunkShips = GameController::getSunkShips(self::$myFleet, false);
                $previousCount = count($previousSunkShips);
                GameController::addHit($position, false);
                $currentSunkShips = GameController::getSunkShips(self::$myFleet, false);
                $currentCount = count($currentSunkShips);

                if ($currentCount > $previousCount) {
                    $justSunkShip = end($currentSunkShips);
                    self::$console->println(sprintf("\nThe computer sunk your %s!", $justSunkShip->getName()));

                    $remainingShips = GameController::getRemainingShips(self::$myFleet, false);
                    if (!empty($remainingShips)) {
                        self::$console->println("\nYour remaining ships:");
                        foreach ($remainingShips as $ship) {
                            self::$console->println("- " . $ship->getName());
                        }
                    }
                }
            }

            if (count(GameController::getSunkShips(self::$myFleet, false)) == count(self::$myFleet)) {
                self::$console->setForegroundColor(Color::DEFAULT_GREY);
                self::$console->println("\n=== GAME OVER ===");
                self::$console->println("You lost!");
                break;
            }

            self::$console->setForegroundColor(Color::YELLOW);
            self::$console->println("\n=== END OF ROUND ===");
            self::$console->setForegroundColor(Color::DEFAULT_GREY);
        }
    }

    public static function parsePosition($input)
    {
        try {
            if (strlen($input) < 2) {
                throw new \Exception("Invalid input format. Please use format like 'A5'");
            }

            $letter = strtoupper(substr($input, 0, 1));
            $number = substr($input, 1);

            if (!in_array($letter, Letter::$letters)) {
                throw new \Exception("Invalid column. Please use letters A-H");
            }

            if (!is_numeric($number) || $number < 1 || $number > 8) {
                throw new \Exception("Invalid row. Please use numbers 1-8");
            }

            return new Position($letter, $number);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
