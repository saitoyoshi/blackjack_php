<?php

namespace BlackJack;

require_once(__DIR__ . '/Card.php');

class Deck
{
    private const SUIT = [
        'ハート',
        'ダイヤ',
        'スペード',
        'クラブ',
    ];
    private const LETTER = [
        'A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'
    ];
    private array $deck = [];
    // public array $deck = [];
    public function __construct()
    {
        foreach (self::SUIT as $suit) {
            foreach (self::LETTER as $letter) {
                $this->deck[] =  new Card($suit, $letter);
            }
        }
        $this->shuffleDeck();
    }
    private function shuffleDeck(): void
    {
        shuffle($this->deck);
    }
    public function drawCards(): Card
    {
        return array_shift($this->deck);
    }
}

// $deck = new Deck();
// $deck->openDeck();
