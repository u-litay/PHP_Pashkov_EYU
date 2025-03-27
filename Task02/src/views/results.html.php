<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты игр</title>
</head>
<body>
    <h1>История игр</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Игрок</th>
                <th>Число 1</th>
                <th>Число 2</th>
                <th>Ваш ответ</th>
                <th>Правильный ответ</th>
                <th>Результат</th>
                <th>Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?= htmlspecialchars($result['player_name']) ?></td>
                    <td><?= htmlspecialchars($result['num1']) ?></td>
                    <td><?= htmlspecialchars($result['num2']) ?></td>
                    <td><?= htmlspecialchars($result['user_answer']) ?></td>
                    <td><?= htmlspecialchars($result['correct_answer']) ?></td>
                    <td><?= htmlspecialchars($result['result']) ?></td>
                    <td><?= htmlspecialchars($result['created_at']) ?></td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <a href="index.php?page=home">Вернуться на главную</a>
</body>
</html>
