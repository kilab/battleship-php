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

    /**
     * @throws \Exception
     */
    public function addPosition(string $input, array $otherShips = []): void
    {
        // Extract column and row from input
        $column = strtoupper(substr($input, 0, 1));
        $row = (int) substr($input, 1);

        // Validate the new position
        $newPosition = new Position($column, $row);

        // Ensure the ship's position doesn't exceed its size
        if (count($this->positions) >= $this->size) {
            throw new \Exception("Cannot add more positions. Ship already fully positioned.");
        }

        // Check if the new position overlaps with itself
        foreach ($this->positions as $position) {
            if ((string) $position === (string) $newPosition) {
                throw new \Exception("Position $input overlaps with itself.");
            }
        }

        // Determine if the new position is contiguous and in the correct direction
        if (!empty($this->positions)) {
            $lastPosition = $this->positions[count($this->positions) - 1];

            $isHorizontal = $lastPosition->getRow() === $row;
            $isVertical = $lastPosition->getColumn() === $column;

            $isContiguous = (
                ($isHorizontal && abs(ord($lastPosition->getColumn()) - ord($column)) === 1) ||
                ($isVertical && abs($lastPosition->getRow() - $row) === 1)
            );

            if (!$isContiguous) {
                throw new \Exception("Position $input is not contiguous or misaligned with existing positions.");
            }

            // Ensure consistent direction
            if (count($this->positions) > 1) {
                $secondLastPosition = $this->positions[count($this->positions) - 2];
                $previousDirectionHorizontal = $secondLastPosition->getRow() === $lastPosition->getRow();
                $previousDirectionVertical = $secondLastPosition->getColumn() === $lastPosition->getColumn();

                if (($previousDirectionHorizontal && !$isHorizontal) || ($previousDirectionVertical && !$isVertical)) {
                    throw new \Exception("Position $input changes the direction of the ship.");
                }
            }
        }

        // Check for overlaps with other ships
        foreach ($otherShips as $otherShip) {
            foreach ($otherShip->getPositions() as $otherPosition) {
                if ((string) $otherPosition === (string) $newPosition) {
                    throw new \Exception("Position $input overlaps with another ship.");
                }
            }
        }

        // Add the new position if all validations pass
        $this->positions[] = $newPosition;
    }

    public function getPositions(): array
    {
        return $this->positions;
    }

    public function setSize($size): void
    {
        $this->size = $size;
    }

    public function isSunk(array $hits): bool
    {
        $hitCount = 0;
        foreach ($this->positions as $position) {
            foreach ($hits as $hit) {
                if ($position == $hit) {
                    $hitCount++;
                }
            }
        }
        return $hitCount == $this->size;
    }
}