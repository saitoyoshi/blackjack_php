<?php

namespace BlackJack;

class TwoPlayRule implements Rule
{
    public function evaluateGame(array $players, Dealer $dealer, array $computers): void
    {
        foreach ($players as $player) {
            $name = $player->getName();
            $score = $player->getHandScore();
            echo  $name . 'の得点は' . $score . ' ' . $player->getCurrentStatusStr() . PHP_EOL;
        }
        $computer = $computers[0];
        $computerName = $computer->getName();
        $computerScore = $computer->getHandScore();
        echo  $computerName . 'の得点は' . $computerScore . ' ' . $computer->getCurrentStatusStr() . PHP_EOL;

        $dealerScore = $dealer->getHandScore();
        $dealerName = $dealer->getName();
        echo  $dealerName . 'の得点は' . $dealerScore . ' ' . $dealer->getCurrentStatusStr() . PHP_EOL;

        $players[] = $computer;
        foreach ($players as $player) {
            $name = $player->getName();
            $score = $player->getHandScore();
            if ($score > $dealerScore) {
                echo $name . 'の勝ちです!' . PHP_EOL;
                $player->changeChipForWin();
            } elseif ($score < $dealerScore || $score === 0) {
                echo $name . 'の負けです!' . PHP_EOL;
                $player->changeChipForLose();
            } else {
                echo $name . 'は' . $dealerName . 'と引き分けです。' . PHP_EOL;
                $player->changeChipForTie();
            }
            echo PHP_EOL;
        }
        //消えゆく別れたハンドからチップを回収
        if (count($players) === 3) {
            $players[0]->addChip($players[1]->getOwnedChip());
        }
    }
}
