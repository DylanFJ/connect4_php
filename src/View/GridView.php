<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright 2020
*/

declare(strict_types=1);

namespace Connect4\View;

use Connect4\Service\Output\Output;
use Connect4\ValueObject\Grid;

class GridView implements ViewInterface
{
    protected Grid $grid;
    protected string $view;
    private Output $output;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
        $this->init();
    }

    protected function init()
    {
        $this->output = new Output();
        $this->output->add(str_repeat('-', 21), true);
        for ($l = 1; $l < 7; $l++) {
            $this->output->add('|', true);
            for ($c = 1; $c < 8; $c++) {
                $pawn = $this->grid->cell($l, $c)->getContent();
                $color = null === $pawn ? 'default' : $pawn->getColor();
                $this->output->addWithStyle("  ", $color, $color)->add('|');
            }
            $this->output->add('', true)->add(str_repeat('-', 21));
        }
    }

    public function render(): string
    {
        return $this->output->get();
    }
}
