<?php

namespace BlackJack;

require_once(__DIR__ . '/Human.php');
require_once(__DIR__ . '/Deck.php');
require_once(__DIR__ . '/Card.php');

class Evaluator
{
    public function __construct(private array $players, private Dealer $dealer, private array $computers)
    {
    }
    public function evaluateGame(Rule $rule): void
    {
        $rule->evaluateGame($this->players, $this->dealer, $this->computers);
    }
}
