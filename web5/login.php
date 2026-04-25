<?php
session_start();

// Функция для генерации случайного логина
function generateLogin() {
    $adjectives = ['Fast', 'Smart', 'Cool', 'Happy', 'Bright', 'Clever', 'Wise', 'Brave', 'Cool', 'Lucky'];
    $nouns = ['Apple', 'Snow', 'Perfume', 'Goose', 'Cat', 'Sugar', 'Muse', 'Hero', 'Star', 'Ghost'];
    $random = rand(100, 999);
    
    return $adjectives[array_rand($adjectives)] . $nouns[array_rand($nouns)] . $random;
}

// Функция для генерации случайного пароля
function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    if ($_GET['ajax'] == 'login') {
        echo json_encode(['value' => generateLogin()]);
    } elseif ($_GET['ajax'] == 'password') {
        echo json_encode(['value' => generatePassword()]);
    }
    exit();
}

// Если уже авторизован - перенаправляем на главную
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Подключение к БД
$db = new PDO("mysql:host=localhost;dbname=u82389", 'u82389', '3736104', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$error = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // Отладочная информация
    $debug_info .= "Попытка входа: login='$login', password='$password'\n";

    try {
        // Ищем пользователя в БД
        $stmt = $db->prepare("SELECT id, password_hash FROM applications WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user) {
            $debug_info .= "Найден пользователь: ID={$user['id']}\n";
            $debug_info .= "Хэш из БД: {$user['password_hash']}\n";
            $debug_info .= "Длина хэша: " . strlen($user['password_hash']) . " символов\n";

            // Проверяем пароль
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $debug_info .= "Пароль верный, авторизация успешна\n";

                // Перенаправляем после успешного входа
                header('Location: index.php');
                exit();
            } else {
                $debug_info .= "Ошибка: пароль не совпадает\n";
                $error = 'Неверный пароль';
            }
        } else {
            $debug_info .= "Ошибка: пользователь не найден\n";
            $error = 'Пользователь с таким логином не существует';
        }
    } catch (PDOException $e) {
        $error = 'Ошибка базы данных';
        $debug_info .= "Ошибка БД: " . $e->getMessage() . "\n";
    }

    // Логируем отладочную информацию
    error_log($debug_info);
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
        h1 {
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
        <h1>Вход в систему</h1>

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
                <button type="button" id="generatePasswordBtn" class="btn btn-secondary generate-btn">Сгенерировать пароль</button>
            </div>

            <button type="submit" class="btn btn-primary">Войти</button>
        </form>

        <div class="register-link">
            Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>
        </div>
    </div>

    <script>
        // Обработчик кнопки генерации пароля
        document.getElementById('generatePasswordBtn').addEventListener('click', function() {
            fetch('?ajax=password')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка сети');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.value) {
                        const passwordField = document.getElementById('password');
                        passwordField.value = data.value;
                    }
                })
                .catch(error => {
                    console.error('Ошибка при генерации пароля:', error);
                    alert('Не удалось сгенерировать пароль. Попробуйте позже.');
                });
        });
    </script>
</body>
</html>
