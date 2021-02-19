<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

namespace Connect4\ValueObject;

interface GridInterface
{
    public function cell(int $line, int $column): GridCell;

    public function empty(): void;

    public function isFull(): bool;

    public function remake(): void;

    public function getItemsFromColumn(int $column, int $startLine = 1, int $stopLine = 6, string $interval = 'closed'): array;

    public function getItemsFromDiagonalBetween(GridCell $start, GridCell $stop, string $interval = 'closed'): array;

    public function getItemsFromLine(int $line, int $startColumn = 1, int $stopColumn = 7, string $interval = 'closed'): array;

    public function isLineFull(int $line): bool;

    public function isLineEmpty(int $line): bool;
}
