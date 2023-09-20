<?php

namespace ortemx\GuessNumber\Controller;

use function ortemx\GuessNumber\View\showGame;
use function cli\line;

function startGame()
{
    line("Game has started");
    showGame();
}
