<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // Главная страница
    public function index()
    {
        return view('game');
    }

    // Получить все игры
    public function getGames()
    {
        $games = Game::with('player')->orderBy('played_at', 'desc')->get();
        return response()->json($games);
    }

    // Начать новую игру
    public function startGame(Request $request)
    {
        $playerName = $request->input('playerName', 'Player');
        $player = Player::firstOrCreate(['name' => $playerName]);

        $num1 = rand(10, 100);
        $num2 = rand(10, 100);
        $gcd = $this->gcd($num1, $num2);

        $game = Game::create([
        'player_id' => $player->id,
        'number1' => $num1,
        'number2' => $num2,
        'gcd' => $gcd,
        'played_at' => now(), // Задаём текущее время с учётом часового пояса
        ]);

        return response()->json([
        'id' => $game->id,
        'playerName' => $player->name,
        'number1' => $game->number1,
        'number2' => $game->number2
        ]);
    }

    // Отправить ответ
    public function submitAnswer(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        if ($game->player_answer !== null) {
            return response()->json(['error' => 'Step already made'], 400);
        }

        $playerAnswer = (int) $request->input('playerAnswer');
        $correctGCD = $game->gcd;
        $result = $playerAnswer === $correctGCD ? 'Верно!' : "Неверно. Правильный ответ: $correctGCD";

        $game->update([
            'player_answer' => $playerAnswer,
            'result' => $result,
        ]);

        return response()->json([
            'num1' => $game->number1,
            'num2' => $game->number2,
            'playerAnswer' => $playerAnswer,
            'correctGCD' => $correctGCD,
            'result' => $result,
        ]);
    }

    // Очистить базу данных
    public function clearDatabase()
    {
        Game::truncate();
        Player::truncate();
        return response()->json(['message' => 'База данных очищена']);
    }

    // Вычисление НОД
    private function gcd($a, $b)
    {
        return $b == 0 ? $a : $this->gcd($b, $a % $b);
    }
}
