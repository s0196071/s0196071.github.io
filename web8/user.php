<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'db_connect.php';

$errors = [];
$success = '';

// Получаем текущие данные
$stmt = $db->prepare("SELECT a.login, a.fio, a.phone, a.email, c.comment
                      FROM applications a
                      LEFT JOIN cooperation_requests c ON a.id = c.user_id
                      WHERE a.id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    die('Пользователь не найден');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    // Валидация
    if ($name === '' || !preg_match('/^[а-яёa-z\s\-]+$/iu', $name)) {
        $errors['name'] = 'Допустимы только буквы, пробелы и дефис';
    }
    if ($phone === '' || !preg_match('/^\+?\d{10,15}$/', $phone)) {
        $errors['phone'] = 'Телефон: от 10 до 15 цифр, может начинаться с +';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный email';
    }
    if ($comment === '') {
        $errors['comment'] = 'Введите комментарий';
    }

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // Обновляем основные данные в applications
            $stmt = $db->prepare("UPDATE applications SET fio = ?, phone = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $phone, $email, $_SESSION['user_id']]);

            // Обновляем или создаём запись в cooperation_requests
            $stmt = $db->prepare("SELECT id FROM cooperation_requests WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);

            if ($stmt->fetch()) {
                $stmt = $db->prepare("UPDATE cooperation_requests
                                      SET name = ?, phone = ?, email = ?, comment = ?, agreement = 1, updated_at = NOW()
                                      WHERE user_id = ?");
                $stmt->execute([$name, $phone, $email, $comment, $_SESSION['user_id']]);
            } else {
                $stmt = $db->prepare("INSERT INTO cooperation_requests (user_id, name, phone, email, comment, agreement)
                                      VALUES (?, ?, ?, ?, ?, 1)");
                $stmt->execute([$_SESSION['user_id'], $name, $phone, $email, $comment]);
            }

            $db->commit();
            $success = 'Данные успешно обновлены.';

            // Обновим переменную для отображения в форме
            $user['fio']     = $name;
            $user['phone']   = $phone;
            $user['email']   = $email;
            $user['comment'] = $comment;
        } catch (Exception $e) {
            $db->rollBack();
            $errors['db'] = 'Ошибка сохранения: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container{
            width: 75%;
            padding: 20px;
            margin: 0 auto;
            margin-top: 50px;
            margin-bottom: 50px;
            border-radius: 8px;
        }
        .highlighted {
            background-color: #FFEBEE;
        } 
        a {
            text-decoration: none;
        }
        .btn{
            margin-right: 10px;
        }
        .admin-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="highlighted container mt-5">
        <h2>Профиль пользователя</h2>
        <p>Логин: <strong><?= htmlspecialchars($user['login']) ?></strong> (нельзя изменить)</p>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                       id="name" name="name"
                       value="<?= htmlspecialchars($_POST['name'] ?? $user['fio'] ?? '') ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                       id="phone" name="phone"
                       value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? '') ?>">
                <?php if (isset($errors['phone'])): ?>
                    <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                       id="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? $user['email'] ?? '') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Комментарий</label>
                <textarea class="form-control <?= isset($errors['comment']) ? 'is-invalid' : '' ?>"
                          id="comment" name="comment" rows="3"><?=
                    htmlspecialchars($_POST['comment'] ?? $user['comment'] ?? '')
                ?></textarea>
                <?php if (isset($errors['comment'])): ?>
                    <div class="invalid-feedback"><?= $errors['comment'] ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <a href="myblog.php" class="btn btn-secondary">На главную</a>
        </form>
    </div>
</body>
</html>
