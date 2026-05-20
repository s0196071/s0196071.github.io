<?php
require_once 'functions.php';

function processCooperation($data, $isLoggedIn, $userId, $db) {
    $errors = [];

    // Валидация
    $name = trim($data['name'] ?? '');
    if ($name === '') {
        $errors['name'] = 'Введите имя';
    } elseif (!preg_match('/^[а-яёa-z\s\-]+$/iu', $name)) {
        $errors['name'] = 'Допустимы только буквы, пробелы и дефис';
    }

    $email = trim($data['email'] ?? '');
    if ($email === '') {
        $errors['email'] = 'Введите email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email';
    }

    $phone = trim($data['phone'] ?? '');
    if ($phone === '') {
        $errors['phone'] = 'Введите телефон';
    } elseif (!preg_match('/^\+?\d{10,15}$/', $phone)) {
        $errors['phone'] = 'Допустимо от 10 до 15 цифр, может начинаться с +';
    }

    $comment = trim($data['comment'] ?? '');
    if ($comment === '') {
        $errors['comment'] = 'Введите комментарий';
    }

    $agreement = !empty($data['agreement']);
    if (!$agreement) {
        $errors['agreement'] = 'Необходимо согласие на обработку данных';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    // Начало транзакции
    $db->beginTransaction();
    try {
        if (!$isLoggedIn) {
            // Создание нового пользователя
            $login = generateLogin();
            $password = generatePassword();
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $db->prepare("INSERT INTO applications (login, password_hash, fio, phone, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$login, $passwordHash, $name, $phone, $email]);
            $userId = $db->lastInsertId();

            // Создание заявки
            $stmt = $db->prepare("INSERT INTO cooperation_requests (user_id, name, email, phone, comment, agreement) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $name, $email, $phone, $comment, $agreement ? 1 : 0]);

            $db->commit();

            return [
                'success' => true,
                'login' => $login,
                'password' => $password,
                'profile_url' => 'user.php'   // или полный URL
            ];
        } else {
            // Авторизованный пользователь: обновляем/создаём заявку
            $stmt = $db->prepare("SELECT id FROM cooperation_requests WHERE user_id = ?");
            $stmt->execute([$userId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $stmt = $db->prepare("UPDATE cooperation_requests SET name=?, email=?, phone=?, comment=?, agreement=?, updated_at=NOW() WHERE user_id=?");
                $stmt->execute([$name, $email, $phone, $comment, $agreement ? 1 : 0, $userId]);
            } else {
                $stmt = $db->prepare("INSERT INTO cooperation_requests (user_id, name, email, phone, comment, agreement) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $name, $email, $phone, $comment, $agreement ? 1 : 0]);
            }

            // Синхронизация основных данных пользователя
            $stmt = $db->prepare("UPDATE applications SET fio=?, phone=?, email=? WHERE id=?");
            $stmt->execute([$name, $phone, $email, $userId]);

            $db->commit();
            return ['success' => true];
        }
    } catch (Exception $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Ошибка базы данных'];
    }
}
?>
