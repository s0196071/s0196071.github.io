<?php
session_start();
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    header('HTTP/1.0 401 Unauthorized');
    die("Требуется авторизация");
}

$username = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];

// Проверка из БД
$stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch();

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    die("Неверные учетные данные");
}

// 1. Проверка авторизации
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// 2. Подключение к БД
$db = new PDO("mysql:host=localhost;dbname=u82389", 'u82389', '3736104', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// 3. Обработка удаления
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db->beginTransaction();
        $db->prepare("DELETE FROM application_languages WHERE application_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM applications WHERE id = ?")->execute([$id]);
        $db->commit();
        header("Location: admin.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        $db->rollBack();
        die("Ошибка при удалении: " . $e->getMessage());
    }
}

// 4. Получение данных
$users = $db->query("SELECT * FROM applications ORDER BY id")->fetchAll();
$stats = $db->query("
    SELECT pl.name, COUNT(al.application_id) as user_count
    FROM programming_languages pl
    LEFT JOIN application_languages al ON pl.id = al.language_id
    GROUP BY pl.name
    ORDER BY user_count DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        body {
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #FFEBEE;
            border-radius: 8px;
            padding: 20px;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        } 
        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .alert {
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert.success {
            background-color: rgba(76, 201, 240, 0.2);
            border-left: 4px solid var(--success);
            color: #0a6c83;
        }
        .alert.error {
            background-color: rgba(247, 37, 133, 0.2);
            border-left: 4px solid var(--danger);
            color: #a11a56;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: var(--shadow);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        .admin-table th,
        .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .admin-table th {
            background-color: blue;
            color: white;
        }

        .admin-table tr:hover {
            background-color: #f5f5f5;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: shadow;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: blue;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h2>Админ-панель</h2>
            <a href="admin_logout.php" class="logout-btn">
                <button type="submit" class="btn btn-primary">Выйти</button>
            </a>
        </header>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert success">Пользователь успешно удален!</div>
        <?php endif; ?>

        <h2>Статистика по языкам</h2>
        <div class="stats-grid">
            <?php foreach ($stats as $stat): ?>
                <div class="stat-card">
                    <h3><?= htmlspecialchars($stat['name']) ?></h3>
                    <div class="stat-value"><?= $stat['user_count'] ?></div>
                    <p>пользователей</p>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Список пользователей</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Дата рождения</th>
                    <th>Пол</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['fio']) ?></td>
                        <td><?= htmlspecialchars($user['phone']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['birthdate'] ?></td>
                        <td><?= $user['gender'] == 'male' ? 'Мужской' : 'Женский' ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="action-btn edit-btn"><button type="submit" class="btn btn-success">Редактировать</button></a>
                            <a href="admin.php?delete=<?= $user['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Удалить этого пользователя?')"><button type="submit" class="btn btn-danger">Удалить</button></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
