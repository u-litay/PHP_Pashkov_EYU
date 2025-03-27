<?php

namespace neospaer\GCD\Controllers;

class GameController {
    private $model;

    public function __construct() {
        require_once __DIR__ . '/../Model.php'; 
        $this->model = new \neospaer\GCD\Model();
    }

    public function handleRequest() {
        if (!isset($_SESSION['player_name'])) {
            header('Location: index.php?page=home');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_answer'])) {
            $num1 = $_SESSION['num1'];
            $num2 = $_SESSION['num2'];
            $userAnswer = (int)$_POST['user_answer'];
            $correctAnswer = $this->model->getGCD($num1, $num2);

            $this->model->saveGameResult($_SESSION['player_name'], $num1, $num2, $userAnswer, $correctAnswer);

            $message = ($userAnswer == $correctAnswer) 
                ? "Правильно! НОД чисел $num1 и $num2 = $correctAnswer." 
                : "Неправильно. НОД чисел $num1 и $num2 = $correctAnswer.";
        }

        $num1 = rand(1, 100);
        $num2 = rand(1, 100);
        $_SESSION['num1'] = $num1;
        $_SESSION['num2'] = $num2;

        require_once __DIR__ . '/../views/game.html.php';
    }
}
