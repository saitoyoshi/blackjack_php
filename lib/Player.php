<?php

namespace BlackJack;

require_once(__DIR__ . '/Human.php');
require_once(__DIR__ . '/Deck.php');
require_once(__DIR__ . '/Card.php');

class Player implements Human
{
    private array $hand = [];
    private int $bettedChip = 0;
    public function __construct(private string $name = 'あなた', private int $ownedChip = 30)
    {
    }
    public function initChip(): void
    {
        $this->ownedChip = 30;
    }
    public function getCurrentStatusStr(): string
    {
        $hands = $this->getHand();
        if ($hands[array_key_last($hands)] === 'Burst') {
            return '[ バースト ]';
        }

        $currentStatusStr = '[ ';
        foreach ($hands as $hand) {
            $currentStatusStr .= $hand->getMark() . $hand->getLetter() . '  ';
        }
        $currentStatusStr .= ']';
        return $currentStatusStr;
    }
    public function bet(int $chip): void
    {
        $this->subtractChip($chip);
        $this->addBet($chip);
    }
    public function getOwnedChip(): int
    {
        return $this->ownedChip;
    }
    public function getBettedChip(): int
    {
        return $this->bettedChip;
    }
    public function addBet(int $chip): void
    {
        $this->bettedChip += $chip;
    }
    public function addChip(int $chip): void
    {
        $this->ownedChip += $chip;
    }
    public function initHand(): void
    {
        $this->hand = [];
    }
    public function changeChipForWin(): void
    {
        //配当1倍
        $this->ownedChip += $this->getBettedChip() * 2;
        echo $this->getBettedChip() * 2 . 'の配当を得ました' . PHP_EOL;

        $this->bettedChip = 0;
    }
    public function changeChipForSurrender(): void
    {
        //配当1倍
        $this->ownedChip += $this->getBettedChip() * 5 / 10;
        echo ($this->getBettedChip() * 5 / 10) . 'が戻ってきました' . PHP_EOL;

        $this->bettedChip = 0;
    }
    public function changeChipForTie(): void
    {
        //配当0.5倍
        $this->ownedChip += $this->getBettedChip();
        echo $this->getBettedChip() . 'が戻ってきました' . PHP_EOL;
        $this->bettedChip = 0;
    }
    public function changeChipForLose(): void
    {
        echo $this->getBettedChip() . 'を失いました' . PHP_EOL;
        $this->bettedChip = 0;
    }
    public function subtractChip(int $chip): void
    {
        $this->ownedChip -=  $chip;
    }
    public function drawCards(Deck $deck): void
    {
        $this->hand[] = $deck->drawCards();
    }
    public function setHand(Card $card): void
    {
        $this->hand = [$card];
    }
    public function addCard(Card $card): void
    {
        $this->hand[] = $card;
    }
    public function getHand(): array
    {
        return $this->hand;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getHandScore(): int
    {
        $hands = $this->getHand();
        if ($hands[array_key_last($hands)] === 'Burst') {
            return 0;
        }
        //Aの枚数をカウントする
        $aceCount = $this->aceCount($hands);
        $sum = 0;
        foreach ($hands as $hand) {
            $sum += $hand->getScore();
        }
        while ($sum > 21 && $aceCount > 0) {
            $sum -= 10;
            $aceCount--;
        }
        return $sum;
    }
    public function isBurst(): bool
    {
        $hands = $this->getHand();
        return $hands[array_key_last($hands)] === 'Burst';
    }
    private function dispCurrentStatus(): void
    {
        echo $this->getName() . 'の現在の得点は' . $this->getHandScore() . $this->getCurrentStatusStr()
            . PHP_EOL;
    }
    public function drawCardsWhileYes(Deck $deck): void
    {
        $isFirstTurn = true;
        while (!$this->isOver21($this->getHandScore())) {
            $this->dispCurrentStatus();

            $canDoubleDown = ($this->getBettedChip() <= $this->getOwnedChip() && $isFirstTurn);
            if ($canDoubleDown) {
                echo 'カードを引きますか？（Y/N/D:DobleDown）' . PHP_EOL;
            } else {
                echo 'カードを引きますか？（Y/N/）' . PHP_EOL;
            }
            $input = trim(fgets(STDIN));
            if ($input === 'N' || $input === 'n') {
                break;
            } elseif ($canDoubleDown && ($input === 'D' || $input === 'd')) {
                $this->doubleDown($deck);
                break;
            }
            $this->drawCards($deck);
            $this->openCard();
            $isFirstTurn = false;
            if ($this->isOver21($this->getHandScore())) {
                $this->tellIhaveBurst();
                break;
            }
        }
    }
    public function iHaveNoChip(): bool
    {
        $iHaveNoChip = false;
        if ($this->getOwnedChip() === 0) {
            $iHaveNoChip = true;
        }
        return $iHaveNoChip;
    }
    public function prepareGame(Deck $deck): void
    {
        $this->decidePlayerBet();
        for ($i = 0; $i < 2; $i++) {
            $this->drawCards($deck);
            $this->openCard();
        }
    }
    protected function decidePlayerBet(): void
    {
        do {
            $currentChip = $this->getOwnedChip();
            echo $this->getName() . 'の現在のチップ' . $currentChip . PHP_EOL;
            echo 'いくらベットしますか？  : ';
            $input = (int) trim(fgets(STDIN));
        } while (!(1 <= $input && $input <= $currentChip));
        $this->bet($input);

        echo $this->getName() . 'の現在のチップ' . $this->getOwnedChip() . PHP_EOL;
    }
    public function doYouSurrender(): bool
    {
        $this->dispCurrentStatus();

        echo 'サレンダーしますか？ Y/N' . PHP_EOL;
        $input = trim(fgets(STDIN));
        if ($input === 'y' || $input === 'Y') {
            return true;
        }

        return false;
    }
    public function canSplit(): bool
    {
        $hands = $this->getHand();
        return $hands[0]->getLetter() === $hands[1]->getLetter();
    }
    public function doYouSplit(): bool
    {
        $this->dispCurrentStatus();

        echo 'スプリットしますか？ Y/N' . PHP_EOL;
        $input = trim(fgets(STDIN));
        if ($input === 'y' || $input === 'Y') {
            return true;
        }

        return false;
    }
    public function doSplit(): Player
    {
        $hands = $this->getHand();
        $playersSecondCard = array_pop($hands);
        $this->setHand($hands[0]);
        $splitedPlayer = new Player($this->getName() . '2', 0);
        $splitedPlayer->setHand($playersSecondCard);
        $playerCurrentBet = $this->getBettedChip();
        echo '追加で' . $playerCurrentBet . 'をベットします' . PHP_EOL;
        $splitedPlayer->addBet($playerCurrentBet);
        $this->subtractChip($playerCurrentBet);
        echo '現在のチップは' . $this->getOwnedChip() . PHP_EOL;

        return $splitedPlayer;
    }
    protected function doubleDown(Deck $deck): void
    {
        echo $this->getName() . 'はダブルダウンをしました' . PHP_EOL;
        echo '追加で' . $this->getBettedChip() . 'をベットしました' . PHP_EOL;
        $this->bet($this->getBettedChip());
        echo '現在のベット額' . $this->getBettedChip() . PHP_EOL;
        echo '現在のチップ額' . $this->getOwnedChip() . PHP_EOL;
        $this->drawCards($deck);
        $this->openCard();
        if ($this->isOver21($this->getHandScore())) {
            $this->tellIhaveBurst();
        }
    }
    protected function tellIhaveBurst(): void
    {
        echo $this->getName() . 'はバーストしました' . PHP_EOL;
        $this->hand[] = 'Burst';
    }
    public function openCard(): void
    {
        $card = $this->getHand()[count($this->getHand()) - 1];
        echo $this->name . 'の引いたカードは' . $card->getSuit() . 'の' . $card->getLetter() . 'です。' . PHP_EOL;
    }
    protected function isOver21(int $score): bool
    {
        if ($score > 21) {
            return true;
        }
        return false;
    }
    private function aceCount(array $hands): int
    {
        $cnt = 0;
        foreach ($hands as $hand) {
            if ($hand->getLetter() === 'A') {
                $cnt++;
            }
        }
        return $cnt;
    }
    public function getAceCount(): int
    {
        return $this->aceCount($this->getHand());
    }
}
