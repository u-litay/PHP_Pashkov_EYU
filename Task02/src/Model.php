<?php

namespace neospaer\GCD;

use SQLite3;

class Model {
    private $db;

    public function __construct() {
        $this->db = new SQLite3(__DIR__ . '/../db/database.sqlite');
        $this->initializeDatabase();
    }

    private function initializeDatabase() {
        $this->db->exec("CREATE TABLE IF NOT EXISTS games (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT,
            num1 INTEGER,
            num2 INTEGER,
            user_answer INTEGER,
            correct_answer INTEGER,
            result TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
    }

    public function getGCD($a, $b) {
        while ($b) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        return $a;
    }

    public function saveGameResult($playerName, $num1, $num2, $userAnswer, $correctAnswer) {
        // Определяем результат игры
        $result = ($userAnswer == $correctAnswer) ? 'Win' : 'Lose';
    
        // Подготовка SQL запроса для вставки данных в таблицу
        $stmt = $this->db->prepare("INSERT INTO games (player_name, num1, num2, user_answer, correct_answer, result, created_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
        
        $stmt->bindValue(1, $playerName);
        $stmt->bindValue(2, $num1);
        $stmt->bindValue(3, $num2);
        $stmt->bindValue(4, $userAnswer);
        $stmt->bindValue(5, $correctAnswer);
        $stmt->bindValue(6, $result, SQLITE3_TEXT);  // Убедитесь, что используете SQLITE3_TEXT
    
        // Выполнение запроса
        $stmt->execute();
    }
    
    public function getGameResults() {
        $results = [];
        $query = $this->db->query("SELECT * FROM games");
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $results[] = $row;
        }
        return $results;
    }
}
