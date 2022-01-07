<?php

require_once(__DIR__ . '/../lib/Player.php');
require_once(__DIR__ . '/../lib/Card.php');
require_once(__DIR__ . '/../lib/Human.php');


use PHPUnit\Framework\TestCase;
use BlackJack\Deck;
use BlackJack\Card;
use BlackJack\Player;
use BlackJack\Human;



class playerTest extends TestCase
{
    // public function testDobleDown()
    // {
    //     $deck = new Deck();
    //     $player = new Player('yoshi',30);
    //     $player->drawCards($deck);
    //     $player->drawCards($deck);
    //     $player->drawCardsWhileYes($deck);
    // }
    public function testAddHand()
    {
        $player = new Player('yoshi', 30);
        // $deck = new Deck();
        // $player->drawCards($deck);
        $card = new Card('ダイヤ', 'A');
        $player->addCard($card);
        $this->assertSame('A', $player->getHand()[0]->getLetter());
        // var_dump($player->getHand());
    }
    public function testSetHand()
    {
        $player = new Player('yoshi', 30);
        $deck = new Deck();
        $player->drawCards($deck);
        var_dump($player->getHand());
        $card = new Card('ダイヤ', 'A');
        $player->setHand($card);
        var_dump($player->getHand());

        $this->assertSame('A', $player->getHand()[0]->getLetter());
        // var_dump($player->getHand());
    }
    public function testBet()
    {
        $player = new Player('yoshi', 30);
        $this->assertSame(30, $player->getOwnedChip());
        $player->bet(15);
        $this->assertSame(15, $player->getOwnedChip());
        $this->assertSame(15, $player->getBettedChip());
    }
    public function testGetChip()
    {
        $player = new Player('yoshi', 30);
        $this->assertSame(30, $player->getOwnedChip());
    }
    public function testChangeForWin()
    {
        $player = new Player('yoshi', 30);
        $this->assertSame(30, $player->getOwnedChip());
        $player->bet(10);
        $this->assertSame(20, $player->getOwnedChip());
        $this->assertSame(10, $player->getBettedChip());
        $player->changeChipForWin();
        $this->assertSame(0, $player->getBettedChip());
        $this->assertSame(40, $player->getOwnedChip());
    }
    public function testChangeForLose()
    {
        $player = new Player('yoshi', 30);
        $this->assertSame(30, $player->getOwnedChip());
        $player->bet(10);
        $this->assertSame(20, $player->getOwnedChip());
        $this->assertSame(10, $player->getBettedChip());
        $player->changeChipForLose();
        $this->assertSame(0, $player->getBettedChip());
        $this->assertSame(20, $player->getOwnedChip());
    }
    public function testChangeForTie()
    {
        $player = new Player('yoshi', 30);
        $player->bet(10);
        $player->changeChipForTie();
        $this->assertSame(30, $player->getOwnedChip());
    }

    public function testGetName()
    {
        $player = new Player('yoshi', 30);
        $this->assertSame('yoshi', $player->getName());
    }
    public function testDrawCards()
    {
        $deck = new Deck();
        $player = new Player('mitti', 30);
        $this->assertSame(0, count($player->getHand()));
        $player->drawCards($deck);
        $this->assertSame(1, count($player->getHand()));
    }
    public function testGetHand()
    {
        $deck = new Deck();
        $player = new Player('mitti', 30);
        $this->assertSame(0, count($player->getHand()));
        $player->drawCards($deck);
        $this->assertSame(1, count($player->getHand()));
    }
    // public function testAceCount()
    // {
    //     $card = new Card('ハート','A');
    //     $deck = new Deck();
    //     $player = new Player();
    //     $player->drawCards($deck);
    //     $player->drawCards($deck);
    // }
}
