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
                throw new \Exception("Position $input is already part of the ship.");
            }
        }

        // Determine if the new position is contiguous and in the correct direction
        if (!empty($this->positions)) {
            $isValidPlacement = false;
            
            foreach ($this->positions as $existingPosition) {
                $isHorizontal = $existingPosition->getRow() === $row;
                $isVertical = $existingPosition->getColumn() === $column;
                
                $isContiguous = (
                    ($isHorizontal && abs(ord($existingPosition->getColumn()) - ord($column)) === 1) ||
                    ($isVertical && abs($existingPosition->getRow() - $row) === 1)
                );
                
                if ($isContiguous) {
                    $isValidPlacement = true;
                    break;
                }
            }

            if (!$isValidPlacement) {
                throw new \Exception("Position $input is not contiguous with existing positions.");
            }

            // Ensure consistent direction if more than 2 positions are placed
            if (count($this->positions) > 1) {
                $isHorizontalPlacement = $this->positions[0]->getRow() === $this->positions[1]->getRow();
                $isVerticalPlacement = $this->positions[0]->getColumn() === $this->positions[1]->getColumn();
                
                $newIsHorizontal = $this->positions[0]->getRow() === $row;
                $newIsVertical = $this->positions[0]->getColumn() === $column;

                if (($isHorizontalPlacement && !$newIsHorizontal) || ($isVerticalPlacement && !$newIsVertical)) {
                    throw new \Exception("Position $input does not belong to the same row or column as the rest of the ship.");
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