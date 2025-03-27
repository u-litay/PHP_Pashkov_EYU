<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Игра</title>
</head>
<body>
    <h1>Игра "Найди НОД"</h1>
    <p>Найди наибольший общий делитель чисел <b><?= $_SESSION['num1'] ?></b> и <b><?= $_SESSION['num2'] ?></b></p>

    <form method="post">
        <input type="number" name="user_answer" required>
        <button type="submit">Проверить</button>
    </form>

    <?php if (isset($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <br>
    <a href="index.php?page=home">Закончить игру</a>
</body>
</html>
