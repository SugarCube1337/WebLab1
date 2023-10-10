<?php
session_start(); // Начинаем сессию
date_default_timezone_set('Europe/Moscow');
// Проверяем, есть ли массив с результатами в сессии, и создаем его, если нет
if (!isset($_SESSION['results'])) {
    $_SESSION['results'] = array();
}

function isValidInput($x, $y, $r)
{
    return is_numeric($x) && is_numeric($y) && is_numeric($r);
}

function checkPoint($x, $y, $r)
{
    // Проверка валидности входных данных
    if (!isValidInput($x, $y, $r)) {
        return 'Invalid';
    }
    // Проверка квадрата в левой верхней четверти
    $isInSquare = ($x <= 0 && $x >= -$r && $y >= 0 && $y <= $r);

    // Проверка четверти окружности в правой верхней четверти
    $isInCircle = ($x >= 0 && $y >= 0 && $y <= sqrt($r * $r / 4 - $x * $x));

    // Проверка треугольника в левой нижней четверти
    $isInTriangle = ($x <= 0 && $y <= 0 && $y >= -$x - $r / 2);

    // Если точка попадает хотя бы в одну из областей, возвращаем true
    return $isInSquare || $isInCircle || $isInTriangle;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x']) && isset($_POST['y']) && isset($_POST['r'])) {
    $x = floatval($_POST['x']);
    $y = floatval($_POST['y']);
    $r = floatval($_POST['r']);

    // Проверка валидности входных данных
    $isValid = isValidInput($x, $y, $r);

    // Вычисляем результат
    $result = checkPoint($x, $y, $r);
    $currentTime = date("d.m.Y H:i:s");
    $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

    // Добавляем результат в массив
    array_unshift($_SESSION['results'], array(
        'x' => $x,
        'y' => $y,
        'r' => $r,
        'result' => $result ? 'Попадание' : 'Непопадание',
        'time' => $currentTime,
        'executionTime' => $executionTime
    ));

    // Выводим таблицу результатов
    echo '
    <thead>
        <tr class="table-header">
            <th>X</th>
            <th>Y</th>
            <th>R</th>
            <th>Результат</th>
            <th>Текущее время</th>
            <th>Время выполнения</th>
        </tr>
    </thead>
    <tbody>';

    foreach ($_SESSION['results'] as $resultItem) {
        echo '
        <tr>
            <td>' . $resultItem['x'] . '</td>
            <td>' . $resultItem['y'] . '</td>
            <td>' . $resultItem['r'] . '</td>
            <td>' . $resultItem['result'] . '</td>
            <td>' . $resultItem['time'] . '</td>
            <td>' . round($resultItem['executionTime'] * 1000000) . ' мкс</td>
        </tr>';
    }

    echo '
    </tbody>';
}
?>
