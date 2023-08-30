<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'name', 'game_format', 'score_format', 'tournament_format',
        'player_limit', 'average_game_time', 'number_of_courts',
    ];

    public function courts()
    {
        return $this->hasMany(Court::class);
    }
    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function createGames(Tournament $tournament)
    {
        $tournament->createPlayers();
        $tournament->createCourts();

        $players = $tournament->players;
        $numberOfCourts = $tournament->number_of_courts;
        $gamesPerPlayer = $tournament->player_limit - 1;

        $playersPerGame = $tournament->game_format === 0 ? 2 : 4;
        $gamesPerRound = $playersPerGame * ($tournament->player_limit / $playersPerGame - 1);

        for ($round = 1; $round <= $gamesPerRound; $round++) {
            for ($gameIndex = 0; $gameIndex < $gamesPerPlayer; $gameIndex++) {
                $gamePlayers = [];

                for ($playerIndex = 0; $playerIndex < $playersPerGame; $playerIndex++) {
                    $currentIndex = ($round + $playerIndex + $gameIndex) % $tournament->player_limit;
                    $gamePlayers[] = $players[$currentIndex]->id;
                }

                $courtId = ($round + $gameIndex) % $numberOfCourts + 1;

                $game = Game::create([
                    'tournament_id' => $tournament->id,
                    'court_id' => $courtId,
                ]);

                $game->players()->sync($gamePlayers);
            }
        }
    }

    public function createCourts(Tournament $tournament)
    {
        $existingCourtCount = $tournament->courts->count();
        $requiredCourtCount = $tournament->number_of_courts;

        if ($existingCourtCount < $requiredCourtCount) {
            $courtsToCreate = $requiredCourtCount - $existingCourtCount;

            for ($i = 1; $i <= $courtsToCreate; $i++) {
                Court::create([
                    'name' => "Court". ($i + $existingCourtCount),
                    'tournament_id' => $tournament->id
                ]);
            }
        }
    }


    public function createPlayers(Tournament $tournament)
    {
        $existingPlayerCount = $tournament->players->count();
        $requiredPlayerCount = $tournament->player_limit;

        if ($existingPlayerCount < $requiredPlayerCount) {
            $playersToCreate = $requiredPlayerCount - $existingPlayerCount;

            for ($i = 1; $i <= $playersToCreate; $i++) {
                Player::create([
                    'name' => "Player". ($i + $existingPlayerCount),
                    'tournament_id' => $tournament->id
                ]);

            }
        }
    }
}
