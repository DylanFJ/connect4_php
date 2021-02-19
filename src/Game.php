<?php

/*
 * Author: Dylan <dylanfj700@gmail.com>
 * (c) Copyright
*/

declare(strict_types=1);

namespace Connect4;

use Connect4\Entity\Participant;
use Connect4\Entity\Pawn;
use Connect4\Factory\ParticipantFactory;
use Connect4\Service\Output\Output;
use Connect4\ValueObject\Grid;
use Connect4\ValueObject\Point;
use Connect4\ValueObject\Referee;
use Connect4\View\GridView;

final class Game
{
    private Output $output;
    private bool $wonGame = false;

    /* According the rules, we begin with last line of the grid */
    private int $currentLine = 6;
    private Grid $grid;
    private Participant $current_player;
    private ?Participant $winner = null;
    private Participant $firstPlayer;
    private Participant $secondPlayer;
    private int $nb_turns = 0;
    private array $participants = [];
    private Output $history;
    private int $elapsedPlayingTime = 0;

    public function __construct()
    {
        $this->output = new Output;
        $this->history = new Output;
        $this->init();
    }

    private function init()
    {
        $this->grid = new Grid;
        $this->participants = ParticipantFactory::createParticipants();
        $this->firstPlayer = $this->participants[1];
        $this->secondPlayer = $this->participants[2];
        $this->elapsedPlayingTime = time();
        $this->load();
    }

    /*
     * Loop, exit the game if an event is detected
     */
    private function load(): void
    {
        if ($this->wonGame || $this->grid->isFull()) {
            $this->elapsedPlayingTime = time() - $this->elapsedPlayingTime;
            $this->stop();
            return;
        }
        $this->shot();
        $this->load();
    }

    private function shot(): void
    {
        $this->turn($this->firstPlayer);
        $this->turn($this->secondPlayer);
    }

    /*
     * Change the turn
     */
    private function turn(Participant $participant)
    {
        $this->current_player = $participant;
        if (true === $this->grid->isLineFull($this->currentLine)) {

            /* According to rules, we begin with last line of the grid
             * When game starting, $this->current_line is 6
            */
            $this->currentLine--;
            $this->turn($participant);
        } else {
            $availableColumns = [];
            for ($c = 1; $c < 8; $c++) {
                if (null === $this->grid->cell($this->currentLine, $c)->getContent()) {
                    $availableColumns[] = $c;
                }
            }
            $column = $participant->chooseColumn($availableColumns);
            $pawn = new Pawn($participant);
            $pawn->setLocation(new Point($this->currentLine, $column));
            $this->grid->cell($this->currentLine, $column)->fill($pawn);
            $this->nb_turns++;
            $this->output->writeLine($this->current_player . ' plays on case ' . '[' . $this->currentLine . ';' . $column . ']');
            $this->output->write((new GridView($this->grid))->render());
            usleep(1);
            $this->history->add($this->current_player . ' plays on case ' . '[' . $this->currentLine . ';' . $column . ']', true);
            $this->check($column);
        }
    }

    /*
     * Call Referer to check if it's a win
     */
    private function check(int $column): void
    {
        $gridCell = $this->grid->cell($this->currentLine, $column);
        if (Referee::checkAlignment($this->grid, $gridCell)) {
            $this->wonGame = true;
            $this->winner = $this->current_player;
        }
    }

    public function stop(): void
    {
        $this->output->writeLine("Game over !\nElapsed Time: " . $this->elapsedPlayingTime . "s");
        $gridView = (new GridView($this->grid))->render();
        $this->output->writeLine($gridView);
        $winnerText = null !== $this->winner ? 'The winner is ' . $this->winner . '(' . $this->winner->getPawnColor() . ')' : 'No winner';
        $this->output->writeLine($winnerText . "\n");
    }
}
