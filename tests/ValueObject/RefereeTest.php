<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4\Test\ValueObject;

use Connect4\Entity\Pawn;
use Connect4\Factory\ParticipantFactory;
use Connect4\Factory\PawnFactory;
use Connect4\ValueObject\Grid;
use Connect4\ValueObject\Referee;
use PHPUnit\Framework\TestCase;

class RefereeTest extends TestCase
{
    private static Grid $grid;
    private static Pawn $pawn;

    public static function setUpBeforeClass(): void
    {
        self::$grid = new Grid;
        $participants = ParticipantFactory::createParticipants();
        $p1 = $participants[1];
        self::$pawn = PawnFactory::createPawn($p1);
        for ($c = 1; $c < 8; $c++) {
            self::$grid->cell(6, $c)->fill(self::$pawn);
        }

        for ($l=6; $l>0; $l--) {
            self::$grid->cell($l,3)->fill(self::$pawn);
        }
    }

    public function testCheckAlignment()
    {
        $this->assertEquals(true, Referee::checkAlignment(self::$grid, self::$grid->cell(6, 3)));
    }

    public function testCheckLine()
    {
        $arrayAfter = self::$grid->getItemsFromLine(6,4,7);

        $c2 = count($arrayAfter);
        $totalOnline = 0;
        if ($c2 > 0) {
            for ($a = 0; $a < $c2; $a++) {
                if (!$arrayAfter[$a] instanceof Pawn || $arrayAfter[$a]->getColor() !== self::$pawn->getColor()) {
                    break;
                }
                $totalOnline++;
            }
        }

        $this->assertEquals(4, $totalOnline);
    }

    public function testCheckColumn()
    {
        $totalOnColumn = 0;
        $arrayAfter = self::$grid->getItemsFromColumn(3,4,6);
        $c2 = count($arrayAfter);

        if ($c2 > 0) {
            for ($a=0; $a<$c2; $a++) {
                if (!$arrayAfter[$a] instanceof Pawn ||  $arrayAfter[$a]->getColor() !== self::$pawn->getColor()) {
                    break;
                }
                $totalOnColumn ++;
            }
        }
        $this->assertEquals(3,$totalOnColumn);
    }
}
