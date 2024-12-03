<?php

namespace Battleship;

class Ship
{
    private $name;
    private $size;
    private $color;
    private $positions = array();

    public function __construct($name, $size, $color = null)
    {
        $this->name = $name;
        $this->size = $size;
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getName(): mixed
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSize(): mixed
    {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getColor(): mixed
    {
        return $this->color;
    }

    public function addPosition(string $input): void
    {
        $letter = substr($input, 0, 1);
        $number = substr($input, 1, 1);

        $this->positions[] = new Position($letter, $number);
    }

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function setSize($size): void
    {
        $this->size = $size;
    }
}