<?php

namespace ortemx\GuessNumber\Controller;

use ortemx\GuessNumber\GameModel\GameModel;
use ortemx\GuessNumber\GameView\GameView;
use ortemx\GuessNumber\Logs\Logs;

use function cli\input;
use function cli\prompt;

function gameLoop($model, $view)
{
    $settings = $model->getSettings();
    $attempt_count = $settings['attemptCount'];
    $maxNumber = $settings['maxNumber'];

    $attempt_number = 0;
    $guested_number = $model->guessNumber();

    $player_name = prompt("Enter your name");

    $logs = new Logs();
    $logs->player_name = $player_name;
    $logs->max_number = $maxNumber;
    $logs->secret_number = $guested_number;

    $view->showRules($player_name, $settings);
    while (++$attempt_number <= $attempt_count) {
        $entered_number = input();
        $logs->entered_numbers[] = $entered_number;
        $logs->attempts[] = $attempt_number;

        if (filter_var($entered_number, FILTER_VALIDATE_INT) === false) {
            $view->notANumberError();
            $logs->answers[] = "NaN";
            if ($attempt_number == $attempt_count) {
                $view->defeatMessage($guested_number);
                $logs->outcome = "lose";
            }
            continue;
        }
        if ($entered_number > $maxNumber) {
            $view->outOfRangeError();
            $logs->answers[] = "OoRange";
            if ($attempt_number == $attempt_count) {
                $view->defeatMessage($guested_number);
                $logs->outcome = "lose";
            }
            continue;
        }
        if ($entered_number == $guested_number) {
            $view->winningMessage($attempt_number);
            $logs->outcome = "won";
            $logs->answers[] = "Guess";
            break;
        } elseif ($entered_number == 0) {
            $view->exitMessage();
            $logs->outcome = "exit";
            $logs->answers[] = "Exit";
            break;
        } elseif ($guested_number < $entered_number) {
            $view->ComparisonMessage("less", $attempt_number, $attempt_count);
            $logs->answers[] = "less";
        } else {
            $view->ComparisonMessage("greater", $attempt_number, $attempt_count);
            $logs->answers[] = "greater";
        }
        if ($attempt_number == $attempt_count) {
            $view->defeatMessage($guested_number);
            $logs->outcome = "lose";
            $logs->answers[] = "Defeat";
            break;
        }
    }

    $model->saveGameIntoDatabase($logs);
}

function startGame()
{
    $model = new GameModel("gamedb.db");
    $view = new GameView();
    while (true) {
        $view->welcomeMessage();
        $view->menu();
        $choise = input();
        switch ($choise) {
            case 1:
                gameLoop($model, $view);
                break;
            case 2:
                $games = $model->getGames();
                count($games) ? $view->showGames($games) : print("There are no saved games");
                break;
            case 3:
                $games = $model->getGames("WON");
                count($games) ? $view->showGames($games, "WON") : print("There are no games");
                break;
            case 4:
                $games = $model->getGames("LOSE");
                count($games) ? $view->showGames($games, "LOSE") : print("There are no games");
                break;
            case 5:
                $players = $model->getTopPlayers();
                count($players) ? $view->showTopPlayers($players) : print("There are no players");
                break;
            case 6:
                $gameid = prompt("Enter game id");
                $moves = $model->getReplayOfGame($gameid);
                $view->showReplayOfGame($gameid, $moves);
                break;
            case 0:
                exit();
            default:
                print("Invalid choise");
        }
        input();
        print("\033[2J\033[;H");
    }
}
