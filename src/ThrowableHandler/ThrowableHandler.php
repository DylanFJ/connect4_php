<?php

namespace Connect4\ThrowableHandler;

use Connect4\Service\Output\Output;

class ThrowableHandler
{
    private static Output $output;

    public static function init()
    {
        self::$output = new Output();
        \set_error_handler([self::class, 'handleError']);
        \set_exception_handler(function (\Throwable $throwable) {
            if ($throwable instanceof \Error) {
                $throwable = new \ErrorException(
                    $throwable->getMessage(),
                    0,
                    $throwable->getCode(),
                    $throwable->getFile(),
                    $throwable->getLine()
                );
            }
            self::handleException($throwable);
        });
    }

    public static function handleError(int $severity, string $message, string $file, int $line)
    {
        if (!(error_reporting() & $severity)) {
            return;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    private static function handleException(\Exception $exception): void
    {
        self::initView($exception);
        self::flush();
    }

    private static function initView(\Exception $exception): void
    {
        $message = $exception->getMessage();
        $textWidth = mb_strlen($message);
        $title = ' An exception has occurred ! ';
        $titleWidth = mb_strlen($title);
        $width = 32;
        $open_close_view = ''; //str_repeat(' ', $width);

        self::$output->addWithStyle($open_close_view, 'white', 'red')
            ->addWithStyle($title, 'default', 'red', true)
            ->addWithStyle(get_class($exception) . ": " . $message, 'default', 'red', true)
            ->addWithStyle($open_close_view, 'default', 'red')
            ->addWithStyle(sprintf("in file : %s", $exception->getFile()),'default','red',true)
            ->addWithStyle(sprintf("on line : %s", $exception->getLine()), 'default', 'red',true)
            ->add("Stack trace:",true)
            ->add($exception->getTraceAsString(),true,true);
    }

    private static function flush(): void
    {
        self::$output->flush();
    }
}
