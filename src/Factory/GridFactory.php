<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\Factory;

use Connect4\ValueObject\Grid;

class GridFactory
{
    public function __invoke(): Grid
    {
        return new Grid();
    }

    public static function buildGrid()
    {
        return new Grid();
    }

    /**
     * @return Grid[]
     */
    public static function buildMoreGrid(int $nb): array
    {
        $grids = [];
        if ($nb < 2) {
            throw new \DomainException('Number of requested grid must be positive and more than 1 !');
        }

        for ($i = 1; $i < $nb + 1; $i++) {
            $grids[] = new Grid();
        }

        return $grids;
    }
}
