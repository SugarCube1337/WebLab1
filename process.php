<?php
session_start(); // Начинаем сессию
date_default_timezone_set('Europe/Moscow');
// Проверяем, есть ли массив с результатами в сессии, и создаем его, если нет
if (!isset($_SESSION['results'])) {
    $_SESSION['results'] = array();
}

function isValidInput($x, $y, $r)
{
    if (!is_numeric($x) || !is_numeric($y) || !is_numeric($r)) {
        return 'Invalid';
    }

    if (!in_array($r, array(1, 1.5, 2, 2.5, 3))) {
        return 'Invalid';
    }

    if ($y < -5 || $y > 5) {
        return 'Invalid';
    }
    if (!in_array($x, array(-3, -2, -1, 0, 1, 2, 3, 4, 5))) {
        return 'Invalid';
    }

    return true;
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

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Метод не разрешен
    exit();
}

// Проверяем длину URI
if (strlen($_SERVER['REQUEST_URI']) > 2000) {
    http_response_code(414); // URI слишком длинный
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x']) && isset($_POST['y']) && isset($_POST['r'])) {
    $x = $_POST['x'];
    $y = ($_POST['y']);
    $r = ($_POST['r']);

    // Проверка валидности входных данных
    $isValid = isValidInput($x, $y, $r);

    if ($isValid !== true) {
        $result = 'Invalid';///400
    } else {
        $result = checkPoint($x, $y, $r) ? 'Попадание' : 'Непопадание';
    }

    $currentTime = date("d.m.Y H:i:s");
    $executionTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];

    // Добавляем результат в массив
    array_unshift($_SESSION['results'], array(
        'x' => $x,
        'y' => $y,
        'r' => $r,
        'result' => $result,
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
