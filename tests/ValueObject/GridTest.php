<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

namespace Test\ValueObject;

use Connect4\Entity\Pawn;
use Connect4\ValueObject\Exception\InvalidColumnException;
use Connect4\ValueObject\Exception\InvalidIntervalException;
use Connect4\ValueObject\Exception\InvalidLineException;
use Connect4\ValueObject\Exception\NotAlignedCellsException;
use Connect4\ValueObject\Grid;
use Connect4\Factory\ParticipantFactory;
use Connect4\Factory\PawnFactory;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    private static ?Grid $grid;

    public static function setUpBeforeClass(): void
    {
        self::$grid = new Grid;
        $participants = ParticipantFactory::createParticipants();
        $p1 = $participants[1];
        $pawn = PawnFactory::createPawn($p1);
        for ($c = 1; $c < 8; $c++) {
            self::$grid->cell(6, $c)->fill($pawn);
        }

        for ($l = 6; $l > 0; $l--) {
            self::$grid->cell($l, 3)->fill($pawn);
        }

        for ($l = 6, $c = 1, $c2 = 7; $l > 0; $l--) {
            self::$grid->cell($l, $c)->fill($pawn);
            $c++;
            $c2--;
        }
    }

    public static function tearDownAfterClass(): void
    {
        self::$grid = null;
    }

    public function testGridIsNotFull()
    {
        $this->assertFalse(self::$grid->isFull());
    }

    public function testGridIsFull()
    {
        $grid = new Grid;
        $participants = ParticipantFactory::createParticipants();
        $p1 = $participants[1];
        //$p2 = $participants[2];
        for ($i = 1; $i < 7; $i++) {
            for ($j = 1; $j < 8; $j++) {
                $grid->cell($i, $j)->fill(new Pawn($p1));
            }
        }
        $this->assertTrue($grid->isFull());
    }

    public function testGetInvalidColumnException()
    {
        $this->expectException(InvalidColumnException::class);
        self::$grid->cell(1, 8);
    }

    /**/
    public function testValidLine()
    {
        $this->assertEquals(1, self::$grid::checkLine(6));
    }

    public function testGetInvalidLineException()
    {
        $this->expectException(InvalidLineException::class);
        self::$grid->cell(7, 1);
    }

    public function testGetInvalidIntervalExceptionWhenStartEqualStop()
    {
        $this->expectException(InvalidIntervalException::class);
        self::$grid->getItemsFromLine(1, 1, 1);
    }

    public function testGetInvalidIntervalExceptionWhenFirstColumnGreaterThanLastColumn()
    {
        $this->expectException(InvalidIntervalException::class);
        self::$grid->getItemsFromLine(1, 2, 1);
    }

    public function testGetInvalidIntervalExceptionWhenInvalidType()
    {
        $this->expectException(InvalidIntervalException::class);
        self::$grid->getItemsFromLine(1, 1, 3, '');
    }

    public function testGetItemsFromLine()
    {
        $arrayBefore = self::$grid->getItemsFromLine(6, 1, 4, 'openRight');
        $arrayAfter = self::$grid->getItemsFromLine(6, 4, 7, 'openLeft');
        $this->assertEquals(3, count($arrayBefore));
        $this->assertEquals(3, count($arrayAfter));
    }

    public function testGetItemsFromColumn()
    {
        $arrayBefore = self::$grid->getItemsFromColumn(3, 1, 3, 'openRight');
        $arrayAfter = self::$grid->getItemsFromColumn(3, 3, 6, 'openLeft');
        $this->assertEquals(2, count($arrayBefore));
        $this->assertEquals(3, count($arrayAfter));
    }

    public function testGetItemsFromDiagonal()
    {
        $this->expectException(NotAlignedCellsException::class);
        $itemsBefore = self::$grid->getItemsFromDiagonalBetween(
            self::$grid->cell(1, 1), self::$grid->cell(6, 7), 'openRight'
        );
        $this->assertEquals(4, $itemsBefore);
    }
}
