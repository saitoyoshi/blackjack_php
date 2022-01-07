<?php

use PHPUnit\Framework\TestCase;
use BlackJack\Card;

require_once(__DIR__ . '/../lib/Card.php');

class CardTest extends TestCase
{
    public function testGetMark()
    {
        $card = new Card('ハート', '7');
        $this->assertSame('♥', $card->getMark());
        $card = new Card('ダイヤ', '7');
        $this->assertSame('♦', $card->getMark());
        $card = new Card('スペード', '7');
        $this->assertSame('♠', $card->getMark());
        $card = new Card('クラブ', '7');
        $this->assertSame('♣', $card->getMark());
    }
    public function testGetSuit()
    {
        $card = new Card('ハート', '7');
        $this->assertSame('ハート', $card->getSuit());
    }
    public function testGetLetter()
    {
        $card = new Card('ハート', '7');
        $this->assertSame('7', $card->getLetter());
    }
    public function testGetScore()
    {
        $card = new Card('ハート', '7');
        $this->assertSame(7, $card->getScore());
    }
}
