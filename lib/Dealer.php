<?php

namespace BlackJack;

require_once(__DIR__ . '/Human.php');
require_once(__DIR__ . '/Deck.php');

class Dealer extends Player implements Human
{

    private string $name = 'ディーラー';
    public function __construct()
    {
        parent::__construct('ディーラー', 0);
    }
    public function drawCardsTo17(Deck $deck): void
    {
        echo $this->name . 'の現在の得点は' . $this->getHandScore() . 'です。' . PHP_EOL;
        while (!$this->isOverNum($this->getHandScore(), 17)) {
            $this->drawCards($deck);
            $this->openCard();

            if ($this->isOverNum($this->getHandScore(), 21)) {
                $this->tellIhaveBurst();
                break;
            }
        }
    }
    public function prepareGame(Deck $deck): void
    {
        $this->drawCards($deck);
        $this->openCard();
        $this->drawCards($deck);
        echo $this->getName() . 'の引いた2枚目のカードはわかりません。';
        $this->dealerFirstStatus();
    }
    public function dealerFirstStatus(): void
    {
        $hands = $this->getHand();
        $openHand = $hands[0]->getMark() . $hands[0]->getLetter();
        echo '[ ' . $openHand .  '  ? ]' . PHP_EOL;
    }
    public function dealerOpenSecondCard(): void
    {
        echo $this->name . 'の引いた2枚めのカードは' . $this->getHand()[1]->getSuit()
            . 'の' . $this->getHand()[1]->getLetter() . 'です' . PHP_EOL;
    }
    private function isOverNum(int $score, int $num): bool
    {
        if ($score > $num) {
            return true;
        }
        return false;
    }
}
