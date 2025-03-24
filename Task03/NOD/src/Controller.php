<?php

namespace Neospaer\NOD\Controller;

use Neospaer\NOD\View;
use Neospaer\NOD\Database;

function gcd($a, $b)
{
    return $b == 0 ? $a : gcd($b, $a % $b);
}

function startGame($playerName = "Player")
{
    // Генерируем два случайных числа от 10 до 100
    $num1 = rand(10, 100);
    $num2 = rand(10, 100);

    // Вычисляем НОД
    $correctGCD = gcd($num1, $num2);

    // Показываем игроку числа
    View\displayQuestion($num1, $num2);

    // Получаем ввод от пользователя
    $playerAnswer = (int) trim(fgets(STDIN));

    // Проверяем ответ
    $result = ($playerAnswer === $correctGCD) ? "Верно!" : "Неверно. Правильный ответ: $correctGCD";

    // Выводим результат
    View\displayResult($result);

    // Сохраняем в БД с указанным именем игрока
    Database\saveGame($playerName, $num1, $num2, $correctGCD, $playerAnswer, $result);
}


function startNewGame($playerName)
{
    $num1 = rand(10, 100);
    $num2 = rand(10, 100);
    $correctGCD = gcd($num1, $num2);
    return Database\saveGame($playerName, $num1, $num2, $correctGCD, null, null);
}

function makeStep($gameId, $playerAnswer)
{
    $game = Database\getGameById($gameId);
    if (!$game || $game['player_answer'] !== null) {
        return ['error' => 'Invalid game or step already made'];
    }

    $correctGCD = $game['gcd'];
    $result = ($playerAnswer === $correctGCD) ? 'Верно!' : "Неверно. Правильный ответ: $correctGCD";
    Database\updateGame($gameId, $playerAnswer, $result);

    return [
        'num1' => $game['number1'],
        'num2' => $game['number2'],
        'playerAnswer' => $playerAnswer,
        'correctGCD' => $correctGCD,
        'result' => $result
    ];
}

function getAllGames()
{
    return Database\getPlayersWithGames();
}

function getGameById($id)
{
    return Database\getGameById($id);
}
