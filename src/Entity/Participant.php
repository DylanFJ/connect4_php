<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\Entity;

use Connect4\ValueObject\GridCell;

final class Participant
{
    private string $name;
    private $latestPawnLocation;
    private string $pawnColor;

    public function __construct(string $name, string $pawnColor)
    {
        if ($name === '' || $pawnColor === '') {
            throw new \UnexpectedValueException('Name must be not empty !');
        }
        $this->name = $name;
        $this->pawnColor = $pawnColor;
    }

    public function chooseColumn(array $availableColumns): int
    {
        return $availableColumns[rand(0, count($availableColumns) - 1)];
    }

    public function getLatestPawnLocation(): GridCell
    {
        return $this->latestPawnLocation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPawnColor(): string
    {
        return $this->pawnColor;
    }

    public function __toString()
    {
        return 'Player ' . $this->name;
    }
}
