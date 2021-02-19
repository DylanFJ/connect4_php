<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4;

use Connect4\Service\Output\Output;
use Connect4\View\ViewInterface;

final class App
{
    private static Output $output;
    private static ViewInterface $finalView;
    private static Game $game;
    const VERSION = '1.0.0';
    const VERSION_ID = '10000';

    public static function start()
    {
        self::$output = new Output();
        self::$output->clear();
        self::$output->writeLine('Game started ...');
        self::$game = new Game();
    }
}
