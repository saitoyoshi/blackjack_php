<?php

namespace BlackJack;

require_once(__DIR__ . '/Deck.php');
require_once(__DIR__ . '/Player.php');
require_once(__DIR__ . '/Computer.php');
require_once(__DIR__ . '/Dealer.php');
require_once(__DIR__ . '/Evaluator.php');
require_once(__DIR__ . '/Rule.php');
require_once(__DIR__ . '/SinglePlayRule.php');
require_once(__DIR__ . '/TwoPlayRule.php');
require_once(__DIR__ . '/ThreePlayRule.php');
// require_once(__DIR__ . '/SevenPlayRule.php');

class Game
{

    public function __construct()
    {
    }
    private int $roundCount = 1;
    private function getRule(string $input): Rule
    {
        $rule = new SinglePlayRule();
        if ($input === '2') {
            $rule = new TwoPlayRule();
        }
        if ($input === '3' || $input === '11') {
            $rule = new ThreePlayRule();
        }

        // if ($input === '11') {
        //     $rule = new SevenPlayRule();
        // }

        return $rule;
    }
    public function play(string $input): void
    {

        $rule = $this->getRule($input);
        $computers = $this->createComputers((int) $input);
        $player = new Player();
        $dealer = new Dealer();

        while (true) {
            $this->mainGameLoop($player, $dealer, $computers, $rule);
        }
    }
    private function mainGameLoop(Player $player, Dealer $dealer, array $computers, Rule $rule): void
    {
        $this->startGame();

        $player->initHand();
        $dealer->initHand();
        if (count($computers) > 0) {
            foreach ($computers as $computer) {
                $computer->initHand();
            }
        }


        $deck = new Deck();

        $this->prepareGame($player, $dealer, $computers, $deck);
        $players = [$player];
        if ($player->doYouSurrender()) {
            $player->changeChipForSurrender();
            $this->tellOneMoreGame($player, $dealer, $computers, $rule);
        }

        if ($player->canSplit() && $player->doYouSplit()) {
            $players[] = $player->doSplit();
        }
        $this->drawFor21($players, $dealer, $computers, $deck, $rule);
        $this->evaluateGame($players, $dealer, $computers, $rule);
        $this->checkPlayerHaveChips($players[0]);

        $computers = $this->getAliveComputers($computers);
        $this->tellOneMoreGame($player, $dealer, $computers, $rule);
    }
    private function checkPlayerHaveChips(Player $player): void
    {
        if ($player->iHaveNoChip()) {
            echo 'チップがなくなりましたので、Game Overです' . PHP_EOL;
            exit;
        }
    }
    private function roundCntUp(): void
    {
        $this->roundCount++;
    }
    private function tellOneMoreGame(Player $player, Dealer $dealer, array $computers, Rule $rule): void
    {
        echo 'もう一度やりますか？' . PHP_EOL;
        echo 'Yes/No : ';
        $input = trim(fgets(STDIN));
        if (!($input === 'n' || $input === 'no' || $input === 'N' || $input === 'No')) {
            $this->roundCntUp();
            $this->mainGameLoop($player, $dealer, $computers, $rule);
        } else {
            $this->gameOver();
        }
    }

    private function createComputers(int $input): array
    {
        $computers = [];
        for ($i = 1; $i < $input; $i++) {
            $computer = new Computer('computer' . (string) $i);
            $computers[] = $computer;
        }
        return $computers;
    }
    private function startGame(): void
    {
        echo 'ブラックジャックを開始します。' . PHP_EOL;
        echo  $this->roundCount . '回目のゲームです。'
            . PHP_EOL;
    }
    private function prepareGame(Player $player, Dealer $dealer, array $computers, Deck $deck): void
    {
        //プレイヤーのベットの指定
        $player->prepareGame($deck);
        if (count($computers) > 0) {
            foreach ($computers as $computer) {
                $computer->prepareGame($deck);
            }
        }
        //ディーラー
        $dealer->prepareGame($deck);
    }
    private function playersAllBurst(array $players, array $computers): bool
    {
        foreach ($players as $player) {
            if (!$player->isBurst()) {
                return false;
            }
        }
        foreach ($computers as $computer) {
            if (!$computer->isBurst()) {
                return false;
            }
        }
        return true;
    }

    private function drawFor21(array $players, Dealer $dealer, array $computers, Deck $deck, Rule $rule): void
    {
        foreach ($players as $player) {
            $player->drawCardsWhileYes($deck);
        }
        if (count($computers) > 0) {
            foreach ($computers as $computer) {
                $computer->drawCardsWhileYes($deck);
            }
        }
        //ディラー以外全員バースト

        //ここでGameクラスのメソッドを呼ぶのではなくPlayerのほうでできそう
        if ($this->playersAllBurst($players, $computers)) {
            echo 'ディーラー以外バーストしたので、ディーラーの勝ちです。' . PHP_EOL;
            foreach ($players as $player) {
                $player->changeChipForLose();
            }
            $this->checkPlayerHaveChips($players[0]);
            $this->getAliveComputers($computers);
            $this->tellOneMoreGame($players[0], $dealer, $computers, $rule);
        }

        $dealer->dealerOpenSecondCard();
        $dealer->drawCardsTo17($deck);
    }
    private function getAliveComputers(array $computers): array
    {

        foreach ($computers as $computer) {
            if ($computer->iHaveNoChip()) {
                echo $computer->getName() . 'のチップがなくなりました' . $computer->getName() .  'は席を立ちました' . PHP_EOL;
                unset($computers[array_search($computer, $computers)]);
            }
        }
        return $computers;
    }
    private function evaluateGame(array $players, Dealer $dealer, array $computers, Rule $rule): void
    {
        $evaluator = new Evaluator($players, $dealer, $computers);
        $evaluator->evaluateGame($rule);
    }
    private function gameOver(): void
    {

        echo 'ブラックジャックを終了します。' . PHP_EOL;
        exit;
    }
}

const INITIAL_PLAYER_NAME = 'あなた';
const INITIAL_PLAYER_CHIP = 30;
const INITIAL_COMPUTERS_CHIP = 30;

function getInputForPlayType(): string
{
    do {
        echo '  1:(一人プレイ)' . PHP_EOL;
        echo '  2:(二人プレイ)' . PHP_EOL;
        echo '  3:(三人プレイ)' . PHP_EOL;
        echo '  11:(11人プレイ)' . PHP_EOL;
        echo '何人で遊びますか？ :  ';
        $input = trim(fgets(STDIN));
        // if ($input == "\ca") {
        //     exit;
        // }
    } while (!($input === '' || $input === '1' || $input === '2' || $input === '3' || $input === '11'));


    return $input;
}

$input = getInputForPlayType();

$game = new Game();

$game->play($input);
