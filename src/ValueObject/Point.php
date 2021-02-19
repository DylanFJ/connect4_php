<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\ValueObject;

final class Point
{
    private int $x;
    private int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }
}
