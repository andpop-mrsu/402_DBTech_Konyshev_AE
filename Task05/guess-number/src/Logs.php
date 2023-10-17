<?php

namespace ortemx\GuessNumber\Logs;

class Logs
{
    public $date_game;
    public $player_name;
    public $max_number;
    public $secret_number;
    public $outcome;
    public $attempts;
    public $entered_numbers;
    public $answers;

    public function __construct()
    {
        $this->date_game = date("Y-m-d H:i:s");
        $this->attempts = [];
        $this->entered_numbers = [];
        $this->answers = [];
    }
}
