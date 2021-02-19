<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\Entity;

use Connect4\ValueObject\Point;

final class Pawn
{
    private Participant $participant;
    private string $color;
    private Point $location;

    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
        $this->color = $participant->getPawnColor();
    }

    public function setLocation(Point $location): void
    {
        $this->location = $location;
    }

    public function getOwner(): Participant
    {
        return $this->participant;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getLocation(): Point
    {
        return $this->location;
    }
}
