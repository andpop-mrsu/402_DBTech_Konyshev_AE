<?php

namespace ortemx\GuessNumber\Controller;

use function ortemx\GuessNumber\View\showGame;
use function ortemx\GuessNumber\View\showWelcomeMessage;
use function ortemx\GuessNumber\Model\guessNumber;
use function cli\line;
use function cli\input;

define("MAX_NUMBER", 100);
define("NUMBER_OF_ATTEMPTS", 10);

function loop()
{
    $attempt_number = 0;
    $guested_number = guessNumber();
    while ($attempt_number < NUMBER_OF_ATTEMPTS) {
        $input_number = input();
        if ($input_number == $guested_number) {
            line("Поздравляем! Вы угадали число");
            return;
        } elseif ($input_number == 0) {
            line("Вы вышли из игры");
            return;
        } elseif ($input_number > $guested_number) {
            line("Загаданное число меньше");
        } else {
            line("Загаданное число больше");
        }
        $attempt_number++;
    }
    line("Попытки исчерпаны");
}
function startGame()
{
    showWelcomeMessage();
    showGame();
    loop();
}
