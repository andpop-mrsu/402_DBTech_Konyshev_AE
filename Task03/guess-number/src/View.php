<?php

namespace ortemx\GuessNumber\View;

use function cli\line;

function showGame()
{
    line("Введите число от 1" . " до " . MAX_NUMBER . " или 0 для выхода");
}

function showWelcomeMessage()
{
    $outputLine = "Добро пожаловать в игру \"Угадай число\"."
    . " Отгадайте загаданное компьютером число в определенном диапазоне"
    . " за конечное число попыток. Результат пока не сохраняется в базе данных.";
    line($outputLine);
}
