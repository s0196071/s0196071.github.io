<?php
function generateLogin() {
    $adjectives = ['Быстрый', 'Смелый', 'Умный', 'Тихий', 'Яркий', 'Скромный', 'Весёлый', 'Храбрый', 'Нежный', 'Лунный'];
    $nouns = ['Лисёнок', 'Ёжик', 'Котик', 'Пёсик', 'Волчонок', 'Зайчик', 'Медвежонок', 'Попугайчик', 'Хомячок', 'Мышонок'];
    return $adjectives[array_rand($adjectives)] . $nouns[array_rand($nouns)] . rand(1, 100);
}

function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}
?>
