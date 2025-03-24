<?php

namespace Neospaer\NOD;

class Game
{
    private int $num1;
    private int $num2;
    private int $correctGCD;

    public function __construct()
    {
        $this->num1 = rand(10, 100);
        $this->num2 = rand(10, 100);
        $this->correctGCD = $this->gcd($this->num1, $this->num2);
    }

    public function getNumbers(): array
    {
        return [$this->num1, $this->num2];
    }

    public function getCorrectGCD(): int
    {
        return $this->correctGCD;
    }

    public function checkAnswer(int $answer): bool
    {
        return $answer === $this->correctGCD;
    }

    private function gcd(int $a, int $b): int
    {
        return $b == 0 ? $a : $this->gcd($b, $a % $b);
    }
}
