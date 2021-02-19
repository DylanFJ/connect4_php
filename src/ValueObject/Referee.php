<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\ValueObject;

use Connect4\Entity\Pawn;

final class Referee
{
    private static ?GridCell $pawnLocation;
    private static ?Grid $grid;
    private static ?Pawn $currentPawn;

    public static function checkAlignment(Grid $grid, GridCell $pawnLocation): bool
    {
        self::$grid = $grid;
        self::$pawnLocation = $pawnLocation;
        self::$currentPawn = $pawnLocation->getContent();
        return self::checkLine() || self::checkColumn() || self::checkDiagonal();
    }

    private static function checkLine(): bool
    {
        $totalOnline = 0;
        $itemsBefore = [];
        $itemsAfter = [];

        if (self::$pawnLocation->getColumn() !== 1) {
            $itemsBefore = self::$grid->getItemsFromLine(self::$pawnLocation->getLine(), 1, self::$pawnLocation->getColumn(), 'openRight'); // [1-n[
        }

        if (self::$pawnLocation->getColumn() !== 7) {
            $itemsAfter = self::$grid->getItemsFromLine(self::$pawnLocation->getLine(), self::$pawnLocation->getColumn(), 7, 'openLeft'); // ]n-7]
        }

        $c = count($itemsBefore);
        $c2 = count($itemsAfter);

        // we begin with last element of $itemsBefore because we are going to read from right to left

        if ($c > 0) {
            for ($b = $c - 1; $b > -1; $b--) {
                if (!$itemsBefore[$b] instanceof Pawn || $itemsBefore[$b]->getColor() !== self::$currentPawn->getColor()) {
                    break;
                }
                $totalOnline++;
            }
        }

        // However here we read from left to right
        if ($c2 > 0) {
            for ($a = 0; $a < $c2; $a++) {
                if (!$itemsAfter[$a] instanceof Pawn || $itemsAfter[$a]->getColor() !== self::$currentPawn->getColor()) {
                    break;
                }
                $totalOnline++;
            }
        }
        return $totalOnline >= 3;
    }

    private static function checkColumn(): bool
    {
        $totalOnColumn = 0;
        $itemsBefore = [];
        $itemsAfter = [];

        // Line interval [1-n[
        if (self::$pawnLocation->getLine() !== 1) {
            $itemsBefore = self::$grid->getItemsFromColumn(self::$pawnLocation->getColumn(), 1, self::$pawnLocation->getLine(), 'openRight');
        }

        // Line interval ]n-6]
        if (self::$pawnLocation->getLine() !== 6) {
            $itemsAfter = self::$grid->getItemsFromColumn(self::$pawnLocation->getColumn(), self::$pawnLocation->getLine(), 6, 'openLeft');
        }

        $c = count($itemsBefore);
        $c2 = count($itemsAfter);

        if ($c > 0) {
            for ($b = $c - 1; $b > -1; $b--) {
                if (!$itemsBefore[$b] instanceof Pawn || $itemsBefore[$b]->getColor() !== self::$currentPawn->getColor()) {
                    break;
                }
                $totalOnColumn++;
            }
        }

        if ($c2 > 0) {
            for ($a = 0; $a < $c2; $a++) {
                if (!$itemsAfter[$a] instanceof Pawn || $itemsAfter[$a]->getColor() !== self::$currentPawn->getColor()) {
                    break;
                }
                $totalOnColumn++;
            }
        }

        return $totalOnColumn >= 3;
    }

    private static function checkDiagonal(): bool
    {
        $totalOnDiagonal = 0;
        $itemsBeforeCurrentPawn = [];
        $itemsAfterCurrentPawn = [];

        $pC = self::$pawnLocation->getColumn() - 1;
        $pL = self::$pawnLocation->getLine() - 1;
        $nL = self::$pawnLocation->getLine() + 1;
        $nC = self::$pawnLocation->getColumn() + 1;

        for ($i = 0; $i < 3; $i++) {

            if (self::$grid::isLine($pL) && self::$grid::isColumn($pC)) {
                $previousPawn = self::$grid->cell($pL, $pC)->getContent();
                if ($previousPawn instanceof Pawn && $previousPawn->getColor() === self::$currentPawn->getColor()) {
                    $itemsBeforeCurrentPawn[] = $previousPawn;
                }
            }

            if (self::$grid::isLine($nL) && self::$grid::isColumn($nC)) {
                $nextPawn = self::$grid->cell($nL, $nC)->getContent();
                if ($nextPawn instanceof Pawn && $nextPawn->getColor() === self::$currentPawn->getColor()) {
                    $itemsAfterCurrentPawn[] = $nextPawn;
                }
            }
            $pC--;
            $pL--;
            $nL++;
            $nC++;
        }

        $totalOnDiagonal = count($itemsBeforeCurrentPawn) + count($itemsAfterCurrentPawn);

        return $totalOnDiagonal >= 3;
    }
}
