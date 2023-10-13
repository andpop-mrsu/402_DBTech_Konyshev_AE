#!/usr/bin/env php

<?php

$GitHubPath = __DIR__ . '/../vendor/autoload.php';
$PackagistPath = __DIR__ . '/../../../autoload.php';

require file_exists($GitHubPath) ? $GitHubPath : $PackagistPath;

use function ortemx\GuessNumber\Controller\startGame;

startGame();
