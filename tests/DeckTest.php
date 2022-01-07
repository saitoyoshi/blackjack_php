<?php

use BlackJack\Card;
use PHPUnit\Framework\TestCase;
use BlackJack\Deck;

require_once(__DIR__ . '/../lib/Deck.php');

class DeckTest extends TestCase
{
    public function test(){
        $this->assertSame('','');
    }
}
