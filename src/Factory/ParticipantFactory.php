<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\Factory;

use Connect4\Entity\Participant;

class ParticipantFactory
{
    public static function createParticipants(): array
    {
        return [0, new Participant('P1', 'yellow'), new Participant('P2', 'red')];
    }
}
