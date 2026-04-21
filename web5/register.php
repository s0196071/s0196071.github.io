<?php
session_start();

// Подключение к БД
$db = new PDO("mysql:host=localhost;dbname=u82389", 'u82389', '3736104', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    // Валидация
    if (empty($login)) {
        $error = 'Введите логин';
    } elseif (strlen($login) < 4) {
        $error = 'Логин должен быть не менее 4 символов';
    } elseif (empty($password)) {
        $error = 'Введите пароль';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } elseif ($password !== $password_confirm) {
        $error = 'Пароли не совпадают';
    } else {
        // Проверка уникальности логина
        $stmt = $db->prepare("SELECT COUNT(*) FROM applications WHERE login = ?");
        $stmt->execute([$login]);

        if ($stmt->fetchColumn() > 0) {
            $error = 'Этот логин уже занят';
        } else {
            // Хеширование пароля
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            try {
                // Создание аккаунта
                $stmt = $db->prepare("INSERT INTO applications
                    (login, password_hash, contract_agreed)
                    VALUES (?, ?, 0)");

                $stmt->execute([
                    $login,
                    $passwordHash
                ]);

                $success = true;
            } catch (PDOException $e) {
                $error = 'Ошибка регистрации: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Регистрация</title>
    <style>
        body {
display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
    background-color: #FFEBEE;
            padding: 2rem;
            border-radius: 4px;
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        button {
            width: 100%;
            margin-top: 1rem;
        }
        .error {
            border: 2px solid red;
            border-radius: 4px;
            margin: 1rem 0;
            padding: 0.75rem;
        }
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .login-link a {
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Регистрация</h2>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="text-align: center; margin: 1rem 0; color: #B83BAC;">
                Регистрация прошла успешно!
            </div>
            <div class="login-link">
                <a href="login.php">Перейти к входу</a>
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label for="login">Логин:</label>
                    <input type="text" id="login" name="login" required
                           value="<?= isset($login) ? htmlspecialchars($login) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Подтвердите пароль:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    Зарегистрироваться
                </button>
            </form>

            <div class="login-link">
                Уже есть аккаунт? <a href="login.php"> Войти</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
