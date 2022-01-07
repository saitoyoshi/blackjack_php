<?php

namespace BlackJack;

interface Rule
{
    public function evaluateGame(array $players, Dealer $dealer, array $computers): void;
}
