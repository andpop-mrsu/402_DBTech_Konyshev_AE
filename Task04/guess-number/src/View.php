<?php

namespace ortemx\GuessNumber\View;

use function cli\line;

function showRules($player_name, $settings)
{
    line(
        "Hello $player_name! Enter a number from 1 to $settings[maxNumber]"
        . " to guess Secret Number or 0 to exit."
        . " You have $settings[attemptCount] attempts. Good luck!"
    );
}

function showWelcomeMessage()
{
    line("Welcome to the 'Guess the Number' game. Guess the number generated"
    . " by the computer within a certain range, within a finite number of attempts.");
}

function showWinningMessage($attempt_number)
{
    if ($attempt_number == 1) {
        line("WOW! You're a lucky one! You guessed the number on your first attempt!");
    } else {
        line("Congratulations! You have guessed the number.");
    }
}

function showExitMessage()
{
    line("You have exited the game.");
}

function showDefeatMessage($guested_number)
{
    line("Attempts exhausted. The number was $guested_number.");
}

function showComparisonMessage($comparison, $attempt_number, $number_of_attempts)
{
    if ($attempt_number != $number_of_attempts) {
        line("The secret number is $comparison. Attempts left: " . ($number_of_attempts - $attempt_number));
    }
}

function showErrorMessage()
{
    line("Entered number is out of range. Be attentive");
}

function showMenu()
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

function askPlayerName()
{
    line("Enter your name:");
}

function showAdminPanel()
{
    line("Enter the password:");
};

function showAllGames($games)
{
    print("\033[2J\033[;H");
    line("List of all saved games:");
    line("|  id |                 date | player | max number | secret number | result |");
    foreach ($games as $game) {
        printf("| %3s | %20s | %6s | %10s | %13s | %6s |\n", $game['gameId'], $game['dateGame'], $game['playerName'], $game['maxNumber'], $game['secretNumber'], $game['gameResult']);
    }
    line("Press ENTER to return");
};

function showTopPlayers($players)
{
    print("\033[2J\033[;H");
    line("List of top players:");
    line("|   name | wins | losses |");
    foreach ($players as $player) {
        printf("| %6s | %4s | %6s |\n", $player['playerName'], $player['wins'], $player['losses']);
    }
    line("Press ENTER to return");
}

function showReplayOfGame($gameId, $moves)
{
    print("\033[2J\033[;H");
    line("Replay of game with id $gameId:");
    line("| attempt | entered number |   reply |");
    foreach ($moves as $move) {
        printf("| %7s | %14s | %7s |\n", $move['attempt'], $move['enteredNumber'], $move['replay']);
    }
    line("Press ENTER to return");
}
