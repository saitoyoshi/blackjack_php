<?php

namespace BlackJack;

require_once(__DIR__ . '/Human.php');
require_once(__DIR__ . '/Deck.php');

class Computer extends Player implements Human
{

    public function decidePlayerBet(): void
    {

        $currentChip = $this->getOwnedChip();
        echo $this->getName() . 'の現在のチップ' . $currentChip . PHP_EOL;
        $rand = rand(1, $currentChip);
        $this->bet($rand);
        echo $this->getName() . 'は' . $this->getBettedChip() . 'をベットしました' . PHP_EOL;
        echo $this->getName() . 'の現在のチップ' . $this->getOwnedChip() . PHP_EOL;
    }
    public function drawCardsWhileYes(Deck $deck): void
    {
        while (!$this->isOverNum($this->getHandScore(), 16)) {
            echo $this->getName() . 'の現在の得点は' . $this->getHandScore() . 'です。'
                . PHP_EOL;
            $this->drawCards($deck);
            $this->openCard();
            if ($this->isOver21($this->getHandScore())) {
                $this->tellIhaveBurst();
                break;
            }
        }
    }
    private function isOverNum(int $score, int $num): bool
    {
        if ($score > $num) {
            return true;
        }
        return false;
    }
}
