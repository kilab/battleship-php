<?php

use Battleship\Color;
use Battleship\GameController;
use Battleship\Position;
use Battleship\Ship;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;


/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $ship;
    private $validationResult;
    private $console;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->console = new Console();
    }

    /**
     * @Given I have a :arg1 ship with :arg2 positions
     * @param $size
     * @param $positions
     */
    public function iHaveAShipWithPositions(int $size, int $positions)
    {
        $this->ship = new Ship("test", $size, Color::RED);

        for ($i = 0; $i < $positions; $i++) {
            $this->ship->addPosition(new Position('A', $i));
        }
    }

    /**
     * @When I check if the ship is valid
     */
    public function iCheckIfTheShipIsValid()
    {
        $this->validationResult = GameController::isShipValid($this->ship);
    }

    /**
     * @Then the result should be true
     */
    public function theResultShouldBeTrue()
    {
        Assert::assertTrue($this->validationResult);
    }

    /**
     * @Then the result should be false
     */
    public function theResultShouldBeFalse()
    {
        Assert::assertFalse($this->validationResult);
    }

    /**
     * @Then I should see the message in yellow color
     */
    public function iShouldSeeTheMessageInYellowColor()
    {
        ob_start();
        $this->console->setForegroundColor(Color::YELLOW);
        $output = ob_get_clean();
        Assert::assertStringContainsString(Color::YELLOW, $output);
    }

    /**
     * @Then I should see the error message in red color
     */
    public function iShouldSeeTheErrorMessageInRedColor()
    {
        ob_start();
        $this->console->setForegroundColor(Color::RED);
        $output = ob_get_clean();
        Assert::assertStringContainsString(Color::RED, $output);
    }
}
