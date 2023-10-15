<?php

namespace ortemx\GuessNumber\GameView;

use function cli\line;

class GameView
{
    public function showRules($player_name, $settings)
    {
        line(
            "Hello $player_name! Enter a number from 1 to $settings[maxNumber]"
            . " to guess Secret Number or 0 to exit."
            . " You have $settings[attemptCount] attempts. Good luck!"
        );
    }

    public function welcomeMessage()
    {
        line("Welcome to the 'Guess the Number' game. Guess the number generated"
        . " by the computer within a certain range, within a finite number of attempts.");
    }

    public function winningMessage($attempt_number)
    {
        if ($attempt_number == 1) {
            line("WOW! You're a lucky one! You guessed the number on your first attempt!");
        } else {
            line("Congratulations! You have guessed the number.");
        }
        line("Press ENTER");
    }

    public function exitMessage()
    {
        line("You have exited the game. Press ENTER.");
    }

    public function defeatMessage($guested_number)
    {
        line("Attempts exhausted. The number was $guested_number.");
    }

    public function ComparisonMessage($comparison, $attempt_number, $number_of_attempts)
    {
        if ($attempt_number != $number_of_attempts) {
            line("The secret number is $comparison. Attempts left: " . ($number_of_attempts - $attempt_number));
        }
    }

    public function outOfRangeError()
    {
        line("Entered number is out of range. Be attentive");
    }

    public function notANumberError()
    {
        line("Entered value is not a number. Be attentive");
    }

    public function menu()
    {
        line("Choose an option:
        \t1. Start a new game
        \t2. Show saved games
        \t3. Show a list of all the games won by the players
        \t4. Show a list of all the games lost by the players
        \t5. Show top players
        \t6. Replay the outcome of any saved game
        \t0. Exit");
    }

    public function showGames($games, $mode = "ALL")
    {
        print("\033[2J\033[;H");
        if ($mode == "ALL") {
            line("List of all games:");
        } elseif ($mode == "WON") {
            line("List of games won by players");
        } elseif ($mode == "LOSE") {
            line("List of games lost by players");
        }
        line("|  id |                 date | player | max number | secret number | result |");
        foreach ($games as $game) {
            printf(
                "| %3s | %20s | %6s | %10s | %13s | %6s |\n",
                $game['gameId'],
                $game['dateGame'],
                $game['playerName'],
                $game['maxNumber'],
                $game['secretNumber'],
                $game['gameResult']
            );
        }
        line("Press ENTER to return");
    }

    public function showTopPlayers($players)
    {
        print("\033[2J\033[;H");
        line("List of top players");
        line("|   name | wins | losses |");
        foreach ($players as $player) {
            printf("| %6s | %4s | %6s |\n", $player['playerName'], $player['wins'], $player['losses']);
        }
        line("Press ENTER to return");
    }

    public function showReplayOfGame($gameId, $moves)
    {
        print("\033[2J\033[;H");
        line("Replay of game with id $gameId");
        line("| attempt | entered number |   reply |");
        foreach ($moves as $move) {
            printf("| %7s | %14s | %7s |\n", $move['attempt'], $move['enteredNumber'], $move['replay']);
        }
        line("Press ENTER to return");
    }
}
