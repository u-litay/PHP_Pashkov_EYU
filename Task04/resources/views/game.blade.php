<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Игра "Наибольший общий делитель"</title>
    <style>
        /* Подключение Google Fonts (Roboto) для более интересного шрифта */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');

        body {
            font-family: 'Roboto', Arial, sans-serif; /* Интересный шрифт Roboto с запасным Arial */
            text-align: center;
            padding: 20px;
            margin: 0;
            background-color: #e6f3f5; /* Светло-голубой фон, гармонирующий с кнопками */
            color: #333;
        }

        h1, h2, h3 {
            color: #2f6f7f; /* Темно-аквамариновый для заголовков, чтобы сочетаться с кнопками */
            margin-bottom: 20px;
            font-weight: 700;
        }

        /* Стили для игровых элементов */
        #start, #game, #result {
            margin: 20px auto;
            max-width: 600px;
        }

        label {
            font-weight: 400;
            margin-right: 10px;
        }

        input[type="text"],
        input[type="number"] {
            padding: 10px;
            border: 2px solid #3f888f;
            border-radius: 5px;
            font-size: 16px;
            width: 200px;
            margin: 10px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #2f6f7f;
            outline: none;
            box-shadow: 0 0 5px rgba(63, 136, 143, 0.5);
        }

        /* Стили для кнопок (сохранены цвета, добавлены тени и градиенты) */
        button {
            padding: 12px 24px;
            background: linear-gradient(45deg, #3f888f, #4fa8b0); /* Градиент для кнопок */
            color: white;
            border: none;
            border-radius: 25px; /* Более округлые края */
            cursor: pointer;
            margin: 5px;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Тень для глубины */
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: linear-gradient(45deg, #2f6f7f, #3f888f); /* Темнее при наведении */
            transform: translateY(-2px); /* Легкое поднятие при наведении */
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
        }

        button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Стили для таблицы */
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background: #3f888f;
            color: white;
            font-weight: 600;
        }

        td {
            font-weight: 300;
            color: #444;
        }

        /* Стили для модального окна */
        .modal {
            display: none; /* Скрыто по умолчанию */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5); /* Полупрозрачный фон */
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            width: 90%;
            max-width: 900px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            position: relative;
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            cursor: pointer;
            color: #2f6f7f;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #3f888f;
        }
    </style>
</head>
<body>
    <h1>Игра "Наибольший общий делитель"</h1>
    <div id="start">
        <label>Введите ваше имя: </label>
        <input type="text" id="playerName" value="Player">
        <button onclick="startGame()">Начать игру</button>
    </div>
    <div id="game" style="display: none;">
        <h2>Привет, <span id="name"></span>! Найди НОД:</h2>
        <p>Числа: <b><span id="num1"></span></b> и <b><span id="num2"></span></b></p>
        <input type="number" id="answer" required>
        <button onclick="submitAnswer()">Проверить</button>
    </div>
    <div id="result" style="display: none;"></div>
    <br>
    <button onclick="showHistory()">Показать историю</button>
    <button onclick="clearDatabase()">Очистить базу данных</button>

    <div id="historyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeHistory()">×</span>
            <h3>История игр</h3>
            <table id="gamesTable">
                <thead>
                    <tr><th>Игрок</th><th>Дата</th><th>Числа</th><th>НОД</th><th>Ответ</th><th>Результат</th></tr>
                </thead>
                <tbody id="gamesBody"></tbody>
            </table>
        </div>
    </div>

    <script>
        let gameId = null;

        async function startGame() {
    const playerName = document.getElementById('playerName').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    console.log('Starting game for player:', playerName);

    try {
        const response = await fetch('/games', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ playerName })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log('POST /games response:', data);
        gameId = data.id;
        console.log('Game ID set to:', gameId);

        const gameDataResponse = await fetch('/games');
        if (!gameDataResponse.ok) {
            throw new Error(`HTTP error! Status: ${gameDataResponse.status}`);
        }

        const gameData = await gameDataResponse.json();
        console.log('GET /games response:', gameData);
        const currentGame = gameData.find(g => g.id === gameId);
        console.log('Found currentGame:', currentGame);

        if (!currentGame) {
            throw new Error(`Game with id ${gameId} not found in response`);
        }

        console.log('Switching UI...');
        document.getElementById('start').style.display = 'none';
        document.getElementById('game').style.display = 'block';
        document.getElementById('name').textContent = currentGame.player.name;
        document.getElementById('num1').textContent = currentGame.number1;
        document.getElementById('num2').textContent = currentGame.number2;
        console.log('UI switched, loading games...');
        loadGames();
    } catch (error) {
        console.error('Error starting game:', error);
        alert('Не удалось начать игру: ' + error.message);
    }
}

        async function submitAnswer() {
    const playerAnswer = parseInt(document.getElementById('answer').value);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content; // Получаем токен

    try {
        const response = await fetch(`/step/${gameId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Добавляем токен
            },
            body: JSON.stringify({ playerAnswer })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.json();
        console.log('Response from /step:', result); // Отладка

        document.getElementById('game').style.display = 'none';
        document.getElementById('result').style.display = 'block';
        document.getElementById('result').innerHTML = `
            <p>Ваш ответ: ${result.playerAnswer}</p>
            <p>Правильный НОД: ${result.correctGCD}</p>
            <p>${result.result}</p>
            <button onclick="resetGame()">Новая игра</button>
        `;
        loadGames();
    } catch (error) {
        console.error('Error submitting answer:', error);
        alert('Не удалось проверить ответ: ' + error.message);
    }
}

        async function loadGames() {
            const response = await fetch('/games');
            const games = await response.json();
            const tbody = document.getElementById('gamesBody');
            tbody.innerHTML = '';
            games.forEach(game => {
                tbody.innerHTML += `
                    <tr>
                        <td>${game.player.name}</td>
                        <td>${new Date(game.played_at).toLocaleString()}</td>
                        <td>${game.number1} и ${game.number2}</td>
                        <td>${game.gcd}</td>
                        <td>${game.player_answer || '-'}</td>
                        <td>${game.result || 'В процессе'}</td>
                    </tr>
                `;
            });
        }

        async function clearDatabase() {
    if (confirm('Вы уверены, что хотите очистить базу данных?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content; // Получаем токен

        try {
            const response = await fetch('/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Указываем тип данных
                    'X-CSRF-TOKEN': csrfToken // Добавляем CSRF-токен
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Clear database response:', result); // Отладка
            loadGames(); // Обновляем историю после очистки
        } catch (error) {
            console.error('Error clearing database:', error);
            alert('Не удалось очистить базу данных: ' + error.message);
        }
    }
}

        function resetGame() {
            document.getElementById('result').style.display = 'none';
            document.getElementById('start').style.display = 'block';
            gameId = null;
        }

        function showHistory() {
            document.getElementById('historyModal').style.display = 'block';
            loadGames();
        }

        function closeHistory() {
            document.getElementById('historyModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('historyModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        loadGames();
    </script>
</body>
</html>