<?php

/**
 * Автор Олег Исаев
 * ВКонтакте: vk.com/id50416641
 * Skype: pandcar97
 */


// Пароль шифрования
$pass = 'ff554';

// Простое шифрование, 2-х символьный, 3-х вариантный
$encode = bel3::encode($pass, json_encode(['foo' => 'Hello World!!!']));

// Сложное, 3-х символьный, 10-ти вариантный
//$encode = bel3::encode($pass, json_encode(['foo' => 'Hello World!!!']), true);

var_dump($encode);

echo '<br>';

// Декодирование
$decode = bel3::decode($pass, $encode);

var_dump($decode);
