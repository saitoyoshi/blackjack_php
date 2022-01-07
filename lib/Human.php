<?php

namespace BlackJack;

require_once(__DIR__ . '/Deck.php');

interface Human
{
    public function drawCards(Deck $deck): void;
    public function getHand(): array;
}
