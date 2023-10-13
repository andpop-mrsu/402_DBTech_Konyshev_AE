<?php

namespace ortemx\GuessNumber\Controller;

use ortemx\GuessNumber\GameModel\GameModel;
use function ortemx\GuessNumber\View\showRules;
use function ortemx\GuessNumber\View\showWelcomeMessage;
use function ortemx\GuessNumber\View\showDefeatMessage;
use function ortemx\GuessNumber\View\showExitMessage;
use function ortemx\GuessNumber\View\showWinningMessage;
use function ortemx\GuessNumber\View\showComparisonMessage;
use function ortemx\GuessNumber\View\showAdminPanel;
use function ortemx\GuessNumber\View\askPlayerName;
use function ortemx\GuessNumber\View\showMenu;
use function ortemx\GuessNumber\View\showAllGames;
use function ortemx\GuessNumber\View\showTopPlayers;
use function ortemx\GuessNumber\View\showReplayOfGame;
use function cli\input;

function gameLoop($model)
{
    $settings = $model->getSettings();
    $attempt_count = $settings['attemptCount'];
    $maxNumber = $settings['maxNumber'];

    $attempt_number = 0;
    $guested_number = $model->guessNumber();

    askPlayerName();
    $player_name = input();

    $game_date = date("Y-m-d H:i:s");
    $outcome = null;

    // для данных по конуретной игре
    $attemps = [];
    $entered_numbers = [];
    $answers = [];
    showRules($player_name, $settings);
    while ($attempt_number <= $attempt_count) {
        $entered_number = input();
        $entered_numbers[] = $entered_number;
        $attempt_number++;
        $attemps[] = $attempt_number;
        if ($entered_number == $guested_number) {
            showWinningMessage($attempt_number);
            $outcome = "won";
            $answers[] = "Guess";
            break;
        } elseif ($entered_number == 0) {
            showExitMessage();
            $outcome = "exit";
            $answers[] = "Exit";
            break;
        } elseif ($guested_number < $entered_number) {
            showComparisonMessage("less", $attempt_number, $attempt_count);
            $answers[] = "less";
        } else {
            showComparisonMessage("greater", $attempt_number, $attempt_count);
            $answers[] = "greater";
        }
        if ($attempt_number == $attempt_count) {
            showDefeatMessage($guested_number);
            $outcome = "lose";
            $answers[] = "Defeat";
            break;
        }
    }
    $info = [
        'dateGame' => $game_date,
        'playerName' => $player_name,
        'maxNumber' => $maxNumber,
        'secretNumber' => $guested_number,
        'gameResult' => $outcome,
        'attempts' => $attemps,
        'enteredNumbers' => $entered_numbers,
        'answers' => $answers,
    ];
    $model->saveGameIntoDatabase($info);
}

function administrating()
{
    print("\033[2J\033[;H");
    showAdminPanel();
    while (true) {
        $password = input();
        if ($password == "1234") {
            // Пересоздание базы данных
            // Удаление данных об игре
            // Удаление данных об игроке
            // Изменение настроек игры
        } elseif ($password == "exit") {
            return;
        }
    }
}
function startGame()
{
    $model = new GameModel("gamedb.db");
    while (true) {
        showWelcomeMessage();
        showMenu();
        $choise = input();
        switch ($choise) {
            case 1:
                gameLoop($model);
                break;
            case 2:
                $games = $model->getGames();
                showAllGames($games);
                break;
            case 3:
                $games = $model->getGames("WON");
                showAllGames($games);
                break;
            case 4:
                $games = $model->getGames("LOSE");
                showAllGames($games);
                break;
            case 5:
                $players = $model->getTopPlayers();
                showTopPlayers($players);
                break;
            case 6:
                $gameid = input();
                $moves = $model->getReplayOfGame($gameid);
                showReplayOfGame($gameid, $moves);
                break;
            case "admin":
                administrating();
                break;
            case 0:
                exit();
            default:
                echo ("Invalid choise");
        }
        input();
        print("\033[2J\033[;H");
    }
}
