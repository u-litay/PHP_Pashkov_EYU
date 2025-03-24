<?php

namespace Neospaer\NOD\Database;

use PDO;

function getDBConnection()
{
    $dbPath = __DIR__ . '/../db/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);
        chmod($dbPath, 0666);
    }
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE TABLE IF NOT EXISTS players (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)");
    $pdo->exec("CREATE TABLE IF NOT EXISTS games (
        id INTEGER PRIMARY KEY AUTOINCREMENT, 
        player_id INTEGER, 
        number1 INTEGER, 
        number2 INTEGER, 
        gcd INTEGER, 
        player_answer INTEGER NULL, 
        result TEXT, 
        played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (player_id) REFERENCES players(id)
    )");
    return $pdo;
}

function saveGame($playerName, $num1, $num2, $gcd, $answer, $result)
{
    try {
        error_log("Saving game: playerName=$playerName, num1=$num1, num2=$num2, gcd=$gcd, answer=$answer, result=$result");
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM players WHERE name = ?");
        $stmt->execute([$playerName]);
        $player = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$player) {
            $stmt = $pdo->prepare("INSERT INTO players (name) VALUES (?)");
            $stmt->execute([$playerName]);
            $playerId = $pdo->lastInsertId();
        } else {
            $playerId = $player['id'];
        }

        $stmt = $pdo->prepare("INSERT INTO games (player_id, number1, number2, gcd, player_answer, result) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$playerId, $num1, $num2, $gcd, $answer, $result]);
        return $pdo->lastInsertId();
    } catch (\Exception $e) {
        error_log("Error saving game: " . $e->getMessage());
        throw new \Exception("Ошибка сохранения игры: " . $e->getMessage());
    }
}

function updateGame($gameId, $playerAnswer, $result)
{
    try {
        error_log("Updating game $gameId: playerAnswer=$playerAnswer, result=$result");
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("UPDATE games SET player_answer = ?, result = ? WHERE id = ?");
        $stmt->execute([$playerAnswer, $result, $gameId]);
    } catch (\Exception $e) {
        error_log("Error updating game: " . $e->getMessage());
        throw new \Exception("Ошибка обновления игры: " . $e->getMessage());
    }
}

function getPlayersWithGames()
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query(
            "SELECT players.name, 
                    DATETIME(games.played_at, '+3 hours') AS played_at, 
                    games.number1, games.number2, games.gcd, games.player_answer, games.result 
             FROM games 
             JOIN players ON games.player_id = players.id 
             ORDER BY games.played_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
        throw new \Exception("Ошибка получения игр: " . $e->getMessage());
    }
}

function getGameById($id)
{
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare(
            "SELECT players.name, 
                    games.number1, games.number2, games.gcd, games.player_answer, games.result 
             FROM games 
             JOIN players ON games.player_id = players.id 
             WHERE games.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
        throw new \Exception("Ошибка получения игры по ID: " . $e->getMessage());
    }
}

function clearDatabase()
{
    try {
        $pdo = getDBConnection();
        $pdo->exec("DELETE FROM games");
        $pdo->exec("DELETE FROM players");
    } catch (\Exception $e) {
        throw new \Exception("Ошибка очистки базы данных: " . $e->getMessage());
    }
}
