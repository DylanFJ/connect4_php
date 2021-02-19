<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\ValueObject;

use Connect4\Entity\Pawn;
use Connect4\ValueObject\Exception\InvalidColumnException;
use Connect4\ValueObject\Exception\InvalidIntervalException;
use Connect4\ValueObject\Exception\InvalidLineException;
use Connect4\ValueObject\Exception\NotAlignedCellsException;
use \UnexpectedValueException;

final class Grid implements GridInterface
{
    protected array $disp = [];
    const LINES = 6;
    const COLUMNS = 7;

    public function __construct()
    {
        $this->init();
    }

    private function init(): void
    {
        for ($i = 1; $i < 7; $i++) {
            for ($j = 1; $j < 8; $j++) {
                $this->disp[$i][$j] = (new GridCell($i, $j));
            }
        }
    }

    public function cell(int $line, int $column): GridCell
    {
        $this->checkLine($line);
        $this->checkColumn($column);
        return $this->disp[$line][$column];
    }

    public function cellsExists(GridCell ...$cells): bool
    {
        foreach ($cells as $cell) {
            if (1 > $cell->getLine() || $cell->getLine() > 6 || 1 < $cell->getColumn() || $cell->getColumn() > 7) {
                return false;
            }
        }
        return true;
    }

    public static function checkColumn(int $column): int
    {
        if ($column < 1 || $column > 7) {
            throw new InvalidColumnException('Invalid offset, column is only between [1-7] according to rules !');
        }
        return 1;
    }

    public static function checkLine(int $line): int
    {
        if ($line < 1 || $line > 6) {
            throw new InvalidLineException('Invalid offset, line is only between [1-6] according to rules !');
        }
        return 1;
    }

    public static function isLine(int $line): bool
    {
        return $line >= 1 && $line <= 6;
    }

    public static function isColumn(int $column): bool
    {
        return $column >= 1 && $column <= 7;
    }

    public function isFull(): bool
    {
        $full = true;
        for ($i = 1; $i < 7; $i++) {
            for ($j = 1; $j < 8; $j++) {
                if (!$this->disp[$i][$j]->getContent() instanceof Pawn) {
                    $full = false;
                    break;
                }
            }
            if (false === $full) {
                break;
            }
        }
        return $full;
    }

    /*
     * Rebuild the grid
     */
    public function remake(): void
    {
        $this->init();
    }

    /**
     * @return Pawn[]|null[]
     */
    public function getItemsFromLine(int $line, int $startColumn = 1, int $stopColumn = 7, string $interval = 'closed'): array
    {
        $values = [];
        $this->checkLine($line);
        $this->checkColumn($startColumn);
        $this->checkColumn($stopColumn);
        $this->checkInterval($startColumn, $stopColumn, $interval);

        for ($j = $startColumn; $j < $stopColumn + 1; $j++) {
            $values [] = $this->disp[$line][$j]->getContent();
        }
        return $values;
    }

    /*
     * @return Pawn[]|null[]
     */
    public function getItemsFromColumn(int $column, int $startLine = 1, int $stopLine = 6, string $interval = 'closed'): array
    {
        $values = [];
        $this->checkColumn($column);
        $this->checkLine($startLine);
        $this->checkLine($stopLine);
        $this->checkInterval($startLine, $stopLine, $interval);
        for ($i = $startLine; $i < $stopLine + 1; $i++) {
            $values [] = $this->disp[$i][$column]->getContent();
        }
        return $values;
    }

    /**
     * Cells are diagonally aligned when cell1.x1 - cell2.x2 = cell1.y - cell2.y
     * @return Pawn[]
     */
    public function getItemsFromDiagonalBetween(GridCell $firstCell, GridCell $lastCell, string $interval = 'closed'): array
    {
        $interval = mb_strtolower($interval);

        $values = [];

        $this->cellsExists($firstCell, $lastCell);
        $startLine = $firstCell->getLine();
        $stopLine = $lastCell->getLine();
        if (abs($firstCell->getLine() - $lastCell->getLine()) !==
            abs($firstCell->getColumn() - $lastCell->getColumn())) {
            throw new NotAlignedCellsException('Cells not diagonally aligned !');
        }

        $startColumn = $firstCell->getColumn();
        $stopColumn = $lastCell->getColumn();

        if ($firstCell->getLine() > $lastCell->getLine()) {
            $startLine = $lastCell->getLine();
            $startColumn = $lastCell->getColumn();
            $stopColumn = $firstCell->getColumn();
        }

        if ($startColumn > $stopColumn) {
            $action = '-';
        } else {
            $action = '+';
        }

        $this->checkInterval($startLine, $stopLine, $interval);

        $ajust = function (string $action, &$nb) {
            switch ($action) {
                case '-':
                    $nb--;
                    break;

                case '+':
                    $nb++;
                    break;

                default:
                    throw new UnexpectedValueException();
            }
        };

        for ($l = $startLine, $c = $startColumn; $l < $stopLine; $l++) {
            $ajust($action, $c);
            $values[] = $this->cell($l, $c);
        }

        return $values;
    }

    private function checkInterval(int &$start, int &$stop, string $interval): void
    {
        if ($start === $stop) {
            throw new InvalidIntervalException('Interval start value and stop value must not be equal !');
        }

        if ($start > $stop) {
            throw new InvalidIntervalException('Interval start value must be inferior to stop value !');
        }

        $this->checkIntervalValue($interval);

        switch ($interval) {
            case 'openLeft':
            case 'closeRight':
                $start += 1;
                break;

            case 'openRight':
            case 'closeLeft':
                $stop -= 1;
                break;

            case 'opened':
                if ($stop - $start < 2) {
                    throw new InvalidIntervalException('Difference between start and stop value must be 2 or more !');
                }
                $start += 1;
                $stop -= 1;
                break;
        }
    }

    private function checkIntervalValue(string $value): void
    {
        $intervalValues = ['closed', 'openRight', 'openLeft', 'closeLeft', 'closeRight', 'opened'];
        if (!in_array($value, $intervalValues)) {
            throw new InvalidIntervalException('Invalid interval type !');
        }
    }

    public function empty(): void
    {
        for ($i = 1; $i < 7; $i++) {
            for ($j = 1; $j < 8; $j++) {
                $this->disp[$i][$j] = (new GridCell($i, $j));
            }
        }
    }

    public function isLineEmpty(int $line): bool
    {
        for ($j = 1; $j < 8; $j++) {
            if ($this->disp[$line][$j]->get() instanceof Pawn) {
                return false;
            }
        }
        return true;
    }

    public function isLineFull(int $line): bool
    {
        for ($j = 1; $j < 8; $j++) {
            if (!$this->disp[$line][$j]->getContent() instanceof Pawn) {
                return false;
            }
        }
        return true;
    }
}
