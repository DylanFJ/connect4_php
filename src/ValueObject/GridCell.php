<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\ValueObject;

use Connect4\Entity\Pawn;

final class GridCell
{
    private int $line;
    private int $column;
    private ?Pawn $content = null;

    public function __construct(int $line, int $column)
    {
        Grid::checkLine($line);
        Grid::checkColumn($column);
        $this->column = $column;
        $this->line = $line;
    }

    public function fill(?Pawn $pawn): void
    {
        $this->content = $pawn;
    }

    public function getContent(): ?Pawn
    {
        return $this->content;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    public function __toString(): string
    {
        return '[' . $this->line . ';' . $this->column . ']';
    }
}
