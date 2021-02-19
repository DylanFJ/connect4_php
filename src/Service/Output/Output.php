<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

namespace Connect4\Service\Output;

class Output
{
    protected array $render = [];
    protected ?string $currentOSFamily = null;
    protected ?string $currentOS = null;

    private static $foregroundColors = [
        'black' => 30,
        'red' => 31,
        'green' => 32,
        'yellow' => 33,
        'blue' => 34,
        'magenta' => 35,
        'cyan' => 36,
        'white' => 37,
        'default' => 39
    ];
    private static $backgroundColors = [
        'black' => 40,
        'red' => 41,
        'green' => 42,
        'yellow' => 43,
        'blue' => 44,
        'magenta' => 45,
        'cyan' => 46,
        'white' => 47,
        'default' => 49,
    ];

    public function __construct(string $mode = 'silent')
    {
        $this->checkOS();
        if (false !== stripos(PHP_OS, 'darwin')) {
            self::$backgroundColors['yellow'] = 103;
        }
    }

    private function checkOS()
    {
        if (DIRECTORY_SEPARATOR === '\\' || PHP_SHLIB_SUFFIX === 'dll' || PATH_SEPARATOR === ';') {
            $this->currentOSFamily = 'Windows';
            $this->currentOS = 'Windows';
        }
    }

    public function write(string $text, bool $clearLine = false): self
    {
        echo $text;
        return $this;
    }

    public function add(string $text, bool $newLine = false, bool $newLineAtEnd = false): self
    {
        if ($newLine) {
            $text = "\n" . $text;
        }

        if ($newLineAtEnd) {
            $text .= "\n";
        }

        $this->render[] = $text;
        return $this;
    }

    public function writeLine(string $text): self
    {
        echo "\n" . $text;
        return $this;
    }

    public function flush()
    {
        $final = '';
        foreach ($this->render as $out) {
            $final .= $out;
        }
        $this->render = [];
        echo $final;
    }

    public function clear()
    {
        $cmd = stripos($this->currentOSFamily, 'windows') ? 'cls' : 'clear';
        system($cmd);
    }

    public function reset()
    {
        $cmd = stripos($this->currentOSFamily, 'windows') ? 'cls' : 'tput reset';
        system($cmd);
    }

    public function get(): string
    {
        $final = '';
        foreach ($this->render as $out) {
            $final .= $out;
        }
        return $final;
    }

    public function addWithStyle(
        string $text,
        string $fg_color = 'default',
        string $bg_color = 'default',
        bool $newLine = false,
        bool $newLineAtEnd = false
    ): self
    {
        if ($newLine) {
            $text = "\n" . $text;
        }

        if ($newLineAtEnd) {
            $text .= "\n";
        }

        $this->render[] = $this->applyColors($text, $fg_color, $bg_color);
        return $this;
    }

    private function applyColors(string $text, string $fgColor, string $bgColor): string
    {
        $l_FgColor = strtolower($fgColor);
        $l_BgColor = strtolower($bgColor);

        if (false === isset(self::$foregroundColors[$l_FgColor])) {
            try {
                throw new \UnexpectedValueException(sprintf("Foreground color not found : %s", $fgColor));
            } catch (\UnexpectedValueException $unexpectedValueException) {
                echo $unexpectedValueException->getMessage();
            }
            $fgColor = 'default';
        }

        if (false === isset(self::$backgroundColors[$l_BgColor])) {
            try {
                throw new \UnexpectedValueException(sprintf("Background color not found : %s", $bgColor));
            } catch (\UnexpectedValueException $unexpectedValueException2) {
                echo $unexpectedValueException->getMessage();
            }
            $bgColor = 'default';
        }

        $text = str_replace("\n",
            sprintf("\033[0;39;49m\n\033[0;39;49m\033[0;%d;%dm",
                self::$foregroundColors[$fgColor],
                self::$backgroundColors[$bgColor]),
            $text
        );

        return sprintf("\033[0;%d;%dm$text\033[0;39;49m",
            self::$foregroundColors[$fgColor],
            self::$backgroundColors[$bgColor]
        );
    }
}
