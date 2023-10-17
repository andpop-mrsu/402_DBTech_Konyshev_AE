<?php

namespace ortemx\GuessNumber\GameModel;

use RedBeanPHP\R;

class GameModel
{
    private $db_path;
    private $max_number;
    private $attempt_count;
    public function __construct($db_path)
    {
        $this->db_path = $db_path;
        R::setup('sqlite:' . $this->db_path);
        $this->createTables();
        $this->setSettings(100, 10);
        $settings = $this->getSettings();
        $this->max_number = $settings['max_number'];
        $this->attempt_count = $settings['attempt_count'];
    }

    public function getDbPath()
    {
        return $this->db_path;
    }

    private function createTables()
    {
        if (!file_exists($this->db_path)) {
            if (!R::inspect('gamesinfo')) {
                $gamesInfoTable = R::dispense('gamesinfo');
                $gamesInfoTable->id = 'INTEGER PRIMARY KEY';
                $gamesInfoTable->date_game = 'DATETIME';
                $gamesInfoTable->player_name = 'TEXT';
                $gamesInfoTable->max_number = 'INTEGER';
                $gamesInfoTable->secret_number = 'INTEGER';
                $gamesInfoTable->outcome = 'TEXT';
                R::store($gamesInfoTable);
            }

            if (!R::inspect('setting')) {
                $settingTable = R::dispense('setting');
                R::store($settingTable);
            }

            if (!R::inspect('replay')) {
                $replayTable = R::dispense('replay');
                $replayTable->id = 'INTEGER PRIMARY KEY';
                $replayTable->gameId = 'INTEGER';
                $replayTable->attempt = 'INTEGER';
                $replayTable->enteredNumber = 'INTEGER';
                $replayTable->reply = 'TEXT';
                R::store($replayTable);
            }
        }
    }

    private function setSettings($max_number, $attempt_count)
    {
        $setting = R::findOne('setting');
        if ($setting) {
            $setting->maxNumber = $max_number;
            $setting->attemptCount = $attempt_count;
            R::store($setting);
        }
    }

    public function getSettings()
    {
        $settings = R::findOne('setting');
        return $settings;
    }

    public function getGames($gameFilter = "ALL")
    {
        $games = [];

        if ($gameFilter == "ALL") {
            $games = R::findAll('gamesinfo');
        } elseif ($gameFilter == "WON") {
            $games = R::find('gamesinfo', 'outcome = ?', ['won']);
        } elseif ($gameFilter == "LOSE") {
            $games = R::find('gamesinfo', 'outcome = ?', ['lose']);
        }
        return $games;
    }

    public function getTopPlayers()
    {
        $sql = "
            SELECT 
                player_name, 
                COUNT(CASE WHEN outcome = 'won' THEN 1 END) AS wins, 
                COUNT(CASE WHEN outcome = 'lose' THEN 1 END) AS losses 
            FROM gamesinfo 
            GROUP BY player_name 
            ORDER BY wins DESC
        ";
        $players = R::getAll($sql);
        return $players;
    }

    public function getReplayOfGame($gameId)
    {
        $moves = R::findAll('replay', 'game_id = ?', [$gameId]);
        return $moves;
    }

    public function saveGameIntoDatabase($logs)
    {
        $gameInfo = R::dispense('gamesinfo');
        $gameInfo->date_game = $logs->date_game;
        $gameInfo->player_name = $logs->player_name;
        $gameInfo->max_number = $logs->max_number;
        $gameInfo->secret_number = $logs->secret_number;
        $gameInfo->outcome = $logs->outcome;
        R::store($gameInfo);

        $lastId = $gameInfo->id;

        for ($i = 0; $i < count($logs->entered_numbers); $i++) {
            $replay = R::dispense('replay');
            $replay->gameId = $lastId;
            $replay->attempt = $logs->attempts[$i];
            $replay->entered_number = $logs->entered_numbers[$i];
            $replay->reply = $logs->answers[$i];
            R::store($replay);
        }
    }

    public function guessNumber()
    {
        return rand(1, $this->max_number);
    }
}
