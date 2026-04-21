<?php
header('Content-Type: text/html; charset=UTF-8');

// Функции для работы с cookies
function getFormData($field) {
    return $_COOKIE["form_$field"] ?? '';
}

function setFormCookie($name, $value, $expire = 0) {
    setcookie("form_$name", $value, $expire, '/');
}

function setErrorCookie($name, $message) {
    setcookie("error_$name", $message, 0, '/');
}

// Обработка POST-запроса (отправка формы)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $allowedLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskel', 'Clojure', 'Prolog', 'Scala', 'Go'];

    // Валидация ФИО
    if (empty($_POST['fio'])) {
        $errors['fio'] = 'Заполните ФИО.';
        setErrorCookie('fio', $errors['fio']);
    } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $_POST['fio'])) {
        $errors['fio'] = 'Допустимы только буквы и пробелы';
        setErrorCookie('fio', $errors['fio']);
    } elseif (strlen($_POST['fio']) > 150) {
        $errors['fio'] = 'Не более 150 символов';
        setErrorCookie('fio', $errors['fio']);
    }
    setFormCookie('fio', $_POST['fio']); // <-- Сохраняем всегда

    // Валидация телефона
    if (empty($_POST['phone'])) {
        $errors['phone'] = 'Заполните телефон.';
        setErrorCookie('phone', $errors['phone']);
    } elseif (!preg_match('/^\+?\d{10,15}$/', $_POST['phone'])) {
        $errors['phone'] = 'От 10 до 15 цифр, можно начинать с +';
        setErrorCookie('phone', $errors['phone']);
    }
    setFormCookie('phone', $_POST['phone']); // <-- Сохраняем всегда

    // Валидация email
    if (empty($_POST['email'])) {
        $errors['email'] = 'Заполните email.';
        setErrorCookie('email', $errors['email']);
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $_POST['email'])) {
        $errors['email'] = 'Некорректный email';
        setErrorCookie('email', $errors['email']);
    }
    setFormCookie('email', $_POST['email']); // <-- Сохраняем всегда

    // Валидация даты рождения
    if (empty($_POST['birthdate'])) {
        $errors['birthdate'] = 'Укажите дату рождения';
        setErrorCookie('birthdate', $errors['birthdate']);
    } else {
        $birthdate = DateTime::createFromFormat('Y-m-d', $_POST['birthdate']);
        $today = new DateTime();
        $minAge = new DateTime('-150 years');
        if (!$birthdate || $birthdate > $today || $birthdate < $minAge) {
            $errors['birthdate'] = 'Некорректная дата';
            setErrorCookie('birthdate', $errors['birthdate']);
        }
    }
    setFormCookie('birthdate', $_POST['birthdate']); // <-- Сохраняем всегда

    // Валидация пола
    if (empty($_POST['gender'])) {
        $errors['gender'] = 'Укажите пол';
        setErrorCookie('gender', $errors['gender']);
    } elseif (!in_array($_POST['gender'], ['male', 'female'])) {
        $errors['gender'] = 'Выберите из списка';
        setErrorCookie('gender', $errors['gender']);
    }
    setFormCookie('gender', $_POST['gender']); // <-- Сохраняем всегда

    // Валидация языков программирования
    if (empty($_POST['languages'])) {
        $errors['languages'] = 'Выберите хотя бы один язык';
        setErrorCookie('languages', $errors['languages']);
    } else {
        foreach ($_POST['languages'] as $lang) {
            if (!in_array($lang, $allowedLanguages)) {
                $errors['languages'] = 'Недопустимый язык';
                setErrorCookie('languages', $errors['languages']);
                break;
            }
        }
        setFormCookie('languages', implode(',', $_POST['languages'])); // <-- Сохраняем всегда
    }

    // Валидация биографии
    if (empty($_POST['bio'])) {
        $errors['bio'] = 'Заполните биографию';
        setErrorCookie('bio', $errors['bio']);
    } elseif (strlen($_POST['bio']) > 5000) {
        $errors['bio'] = 'Не более 5000 символов';
        setErrorCookie('bio', $errors['bio']);
    }
    setFormCookie('bio', $_POST['bio']); // <-- Сохраняем всегда

    // Валидация чекбокса
    if (empty($_POST['contract'])) {
        $errors['contract'] = 'Необходимо согласие';
        setErrorCookie('contract', $errors['contract']);
    } else {
        setFormCookie('contract', '1'); // <-- Сохраняем всегда
    }

    // Если есть ошибки — перенаправляем обратно
    if (!empty($errors)) {
        header('Location: index.php');
        exit();
    }

    // Подключение к БД
    $user = 'u82388';
    $pass = '5768002';
    $dbname = 'u82388';
    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $db->beginTransaction();

        // Сохранение основной информации
        $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, birthdate, gender, bio, contract_agreed)
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['fio'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['birthdate'],
            $_POST['gender'],
            $_POST['bio'],
            isset($_POST['contract']) ? 1 : 0
        ]);
        $applicationId = $db->lastInsertId();

        // Сохранение языков
        $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id)
                              SELECT ?, id FROM programming_languages WHERE name = ?");
        foreach ($_POST['languages'] as $lang) {
            $stmt->execute([$applicationId, $lang]);
        }

        $db->commit();

        // Очищаем cookies с данными формы и ошибками
        foreach ($_COOKIE as $name => $value) {
            if (strpos($name, 'form_') === 0 || strpos($name, 'error_') === 0) {
                setcookie($name, '', time() - 3600, '/');
            }
        }

        header('Location: index.php?success=1&id='.$applicationId);
        exit();
    } catch (PDOException $e) {
        if (isset($db)) {
            $db->rollBack();
        }
        setErrorCookie('db', 'Ошибка сохранения: '.$e->getMessage());
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title> Задание 3 </title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="basecss.css" type="text/css"/>

</head>
<body>
    <div class="highlighted container">
        <form action="process.php" method="POST">
            <h3> Анкета </h3>
            <div class="mb-3">
                <label for="fio" class="form-label">ФИО:</label> 
                <input class="form-control" id="fio" aria-describedby="format" name="fio" type="text" placeholder="Введите ваше ФИО" required>
                <?php if (isset($errors['fio'])): ?>
                    <div class="error"><?= $errors['fio'] ?></div>
                <?php endif; ?>
            </div>  

            <div class="mb-3">
                <label for="phone" class="form-label">Номер телефона:</label>
                <input class="form-control" id="phone" aria-describedby="format" name="phone" type="tel" placeholder="Введите номер телефона" required> 
                <?php if (isset($errors['phone'])): ?>
                    <div class="error"><?= $errors['phone'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input class="form-control" id="email" name="email" type="email" placeholder="Введите ваш email" required>
                <?php if (isset($errors['email'])): ?>
                    <div class="error"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="birthdate">Дата рождения:</label>
                <input name="birthdate" type="date" class="form-control" id="birthdate" required>
                <?php if (isset($errors['birthdate'])): ?>
                    <div class="error"><?= $errors['birthdate'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">Пол:
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="male" value="male" required>
                    <label class="form-check-label" for="male">Мужской</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                    <label class="form-check-label" for="female">Женский</label>  
                </div>
                <?php if (isset($errors['gender'])): ?>
                    <div class="error"><?= $errors['gender'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="languages" class="form-label">Любимый язык программирования:</label>
                <select id="languages" name="languages[]" multiple="multiple" class="form-select" required>
                    <option value="Pascal"> Pascal </option>
                    <option value="C"> C </option>
                    <option value="C++"> C++ </option>
                    <option value="JavaScript"> JavaScript </option>
                    <option value="PHP"> PHP </option>
                    <option value="Python"> Python </option>
                    <option value="Java"> Java </option>
                    <option value="Haskel"> Haskel </option>
                    <option value="Clojure"> Clojure </option>
                    <option value="Prolog"> Prolog </option>
                    <option value="Scala"> Scala </option>
                    <option value="Go">Go</option>
                </select>
                <?php if (isset($errors['languages'])): ?>
                    <div class="error"><?= $errors['languages'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">Биография:
                <div class="form-floating">
                    <textarea class="form-control" id="bio" name="bio" required></textarea>
                    <label for="bio">Напишите о себе...</label>
                    <?php if (isset($errors['bio'])): ?>
                        <div class="error"><?= $errors['bio'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <input type="checkbox" class="form-check-input" name="contract" id="contract">
                <label class="form-check-label" for="contract">С контрактом ознакомлен(-а)</label>
                <?php if (isset($errors['contract'])): ?>
                    <div class="error"><?= $errors['contract'] ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        <?php if(isset($showSuccess) && $showSuccess): ?>
                    <div class="success-mes">
                        Спасибо, данные отправлены.
                    </div>
            <?php endif; ?>

        </form> 
    </div>
</body>
</html>
