<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\Factory;

use Connect4\Entity\Participant;
use Connect4\Entity\Pawn;

final class PawnFactory
{
    public static function createPawn(Participant $participant): Pawn
    {
        return new Pawn($participant);
    }
}
