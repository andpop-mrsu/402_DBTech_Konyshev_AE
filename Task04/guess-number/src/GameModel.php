<?php

namespace ortemx\GuessNumber\GameModel;

use SQLite3;

class GameModel
{
    private $db_path;
    private $max_number;
    private $attempt_count;
    public function __construct($db_path)
    {
        $this->db_path = $db_path;
        $this->createTables();
        $this->setSettings(10, 10);
        $settings = $this->getSettings();
        $this->max_number = $settings['maxNumber'];
        $this->attempt_count = $settings['attemptCount'];
    }

    public function getDbPath()
    {
        return $this->db_path;
    }

    private function createTables()
    {
        if (!file_exists($this->db_path)) {
            $db = new SQLite3($this->db_path);
            $games_info_table = "CREATE TABLE GamesInfo(
                gameId INTEGER PRIMARY KEY,
                dateGame DATETIME,        
                playerName TEXT,
                maxNumber INTEGER,
                secretNumber INTEGER,
                gameResult TEXT
            )";

            $settings_table = "CREATE TABLE Settings(
                maxNumber INTEGER,
                attemptCount INTEGER
            )";

            $replays_table = "CREATE TABLE Replays(
                gameId INTEGER,
                attempt INTEGER,
                enteredNumber INTEGER,
                replay TEXT)";
            $db->exec($games_info_table);
            $db->exec($settings_table);
            $db->exec($replays_table);
            $db->close();
        }
    }

    private function setSettings($max_number, $attempt_count)
    {
        $db = new SQLite3($this->db_path);
        $insert_settings = "INSERT INTO Settings VALUES($max_number, $attempt_count)";
        $db->exec($insert_settings);
        $db->close();
    }

    public function getSettings()
    {
        $db = new SQLite3($this->db_path);
        $sql = "SELECT * FROM Settings";
        $result = $db->query($sql);
        $settings = $result->fetchArray(SQLITE3_ASSOC);
        $db->close();
        return $settings;
    }

    public function getGames($gameFilter = "ALL")
    {
        $db = new SQLite3($this->db_path);
        $sql = "";
        if ($gameFilter == "ALL") {
            $sql = "SELECT * FROM GamesInfo";
        } elseif ($gameFilter == "WON") {
            $sql = "SELECT * FROM GamesInfo WHERE gameResult = 'won'";
        } elseif ($gameFilter == "LOSE") {
            $sql = "SELECT * FROM GamesInfo WHERE gameResult = 'lose'";
        }
        $result = $db->query($sql);
        $games = [];
        while ($game = $result->fetchArray(SQLITE3_ASSOC)) {
            $games[] = $game;
        }
        $db->close();
        return $games;
    }

    public function getTopPlayers()
    {
        $db = new SQLite3($this->db_path);
        $sql =
        "SELECT 
            playerName, 
            COUNT(CASE WHEN gameResult = 'won' THEN 1 END) AS wins, 
            COUNT(CASE WHEN gameResult = 'lose' THEN 1 END) AS losses 
        FROM GamesInfo 
        GROUP BY playerName 
        ORDER BY wins DESC";
        $result = $db->query($sql);
        $players = [];
        while ($player = $result->fetchArray(SQLITE3_ASSOC)) {
            $players[] = $player;
        }
        $db->close();
        return $players;
    }

    public function getReplayOfGame($gameId)
    {
        $db = new SQLite3($this->db_path);
        $sql = "SELECT * FROM Replays WHERE gameId = " . $gameId;
        $result = $db->query($sql);
        $moves = [];
        while ($move = $result->fetchArray(SQLITE3_ASSOC)) {
            $moves[] = $move;
        }
        $db->close();
        return $moves;
    }

    public function saveGameIntoDatabase($logs)
    {
        $dbPath = 'gamedb.db';
        $db = new SQLite3($dbPath);
        $sql = "INSERT INTO GamesInfo 
        (dateGame, playerName, maxNumber, secretNumber, gameResult) VALUES(
            '" . $logs->date_game . "',
            '" . $logs->player_name . "',
            '" . $logs->max_number . "',
            '" . $logs->secret_number . "',
            '" . $logs->outcome . "'
        )";
        $db->exec($sql);
        $lastId = $db->lastInsertRowID();
        for ($i = 0; $i < count($logs->attempts); $i++) {
            $sql = "INSERT INTO Replays
            (gameId, attempt, enteredNumber, replay) VALUES(
                '" . $lastId . "',
                '" . $logs->attempts[$i] . "',
                '" . $logs->entered_numbers[$i] . "',
                '" . $logs->answers[$i] . "'
            )";
            $db->exec($sql);
        }
        $db->close();
    }

    public function guessNumber()
    {
        return rand(1, $this->max_number);
    }
}
