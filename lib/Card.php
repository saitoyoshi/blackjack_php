<?php

namespace BlackJack;

class Card
{
    private const CARD_SCORE = [
        'A' => 11,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        '10' => 10,
        'J' => 10,
        'Q' => 10,
        'K' => 10,
    ];
    public function __construct(private string $suit, private string $letter)
    {
    }

    public function getSuit(): string
    {
        return $this->suit;
    }
    public function getLetter(): string
    {
        return $this->letter;
    }
    public function getMark(): string
    {
        $suit = $this->suit;
        if ($suit === 'ハート') {
            return '♥';
        }
        if ($suit === 'スペード') {
            return '♠';
        }
        if ($suit === 'ダイヤ') {
            return '♦';
        }
        if ($suit === 'クラブ') {
            return '♣';
        }
        return '';
    }
    public function getScore(): int
    {
        return self::CARD_SCORE[$this->letter];
    }
}
