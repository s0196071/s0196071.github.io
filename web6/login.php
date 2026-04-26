<?php
session_start();

// Подключение к БД
$db = new PDO("mysql:host=localhost;dbname=u68775", 'u68775', '7631071', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Экстренный сброс пароля (доступен только по специальной ссылке)
if (isset($_GET['emergency_reset'])) {
    $new_hash = password_hash('admin123', PASSWORD_BCRYPT);
    $stmt = $db->prepare("UPDATE admin_users SET password_hash = ? WHERE username = 'admin'");
    $stmt->execute([$new_hash]);
    die("Пароль сброшен. Новый пароль: admin123");
}

$error = '';
$attempts = $_SESSION['login_attempts'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $attempts < 5) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Очистка от невидимых символов
    $password = preg_replace('/[^\x20-\x7E]/', '', $password);

    // Проверка учетных данных
    $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin) {
        // Проверка пароля с подробным логированием
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['login_attempts'] = 0;
            header('Location: admin.php');
            exit();
        } else {
            // Логирование неудачной попытки
            error_log("Failed admin login attempt. Username: $username, IP: {$_SERVER['REMOTE_ADDR']}");
            $error = 'Неверный пароль';
        }
    } else {
        $error = 'Пользователь не найден';
    }

    $_SESSION['login_attempts'] = ++$attempts;
} elseif ($attempts >= 5) {
    $error = 'Слишком много попыток. Попробуйте позже.';
    sleep(5); // Замедление brute-force атак
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Вход в систему</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #FFEBEE;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
        }
        .error {
            border: 2px solid red;
            border-radius: 4px;
            color: red;
            margin: 15px 0;
            padding: 10px;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Вход в систему</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Войти</button>
        </form>

        <div class="register-link">
            Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>
        </div>
    </div>
</body>
</html>
