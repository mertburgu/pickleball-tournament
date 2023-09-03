<?php

namespace App\Services;
use App\Models\GameTracking;
use App\Models\PlayerResult;
use App\Models\Result;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\Team;
use App\Models\Court;
use App\Models\Game;

class TournamentService
{
    private $tournament;

    public function __construct(Tournament $tournament)
    {
        $this->tournament = $tournament;
    }

    public function createTournament(array $data)
    {
        $validator = validator($data, [
            'name' => 'required|string',
            'game_format' => 'required',
            'score_format' => 'required',
            'tournament_format' => 'required',
            'average_game_time' => 'required',
            'number_of_courts' => 'required',
            'player_limit' => 'required|integer|player_limit_multiple:' . $data['game_format'],
        ]);

        if ($validator->fails()) {
            return false;
        }

        $tournament = $this->tournament->create($data);
        $tournament->load('courts', 'players', 'teams');

        $this->createPlayersAndTeams($tournament);
        $this->createCourts($tournament);
//        $this->assignPlayersToGames($tournament);

        return $tournament;
    }

    private function createPlayersAndTeams(Tournament $tournament)
    {
        $existingPlayerCount = $tournament->players->count();
        $requiredPlayerCount = $tournament->player_limit;

        if ($existingPlayerCount < $requiredPlayerCount) {
            $playersToCreate = $requiredPlayerCount - $existingPlayerCount;

            if ($tournament->game_format == 0) {
                // 1vs1 formatı için her oyuncuyu tekli olarak ekleyin
                for ($i = 1; $i <= $playersToCreate; $i++) {
                    Player::create([
                        'name' => "Player " . ($i + $existingPlayerCount),
                        'tournament_id' => $tournament->id,
                        'order' => $i,
                    ]);
                }
            } elseif ($tournament->game_format == 1) {
                // 2vs2 formatı için her iki oyuncuyu aynı takıma ekleyin
                for ($i = 1; $i <= $playersToCreate; $i += 2) {
                    $team = Team::create([
                        'name' => "Team " . ($i + $existingPlayerCount),
                        'tournament_id' => $tournament->id,
                    ]);

                    $player1 = Player::create([
                        'name' => "Player " . ($i + $existingPlayerCount),
                        'tournament_id' => $tournament->id,
                        'team_id' => $team->id,
                        'order' => $i,
                    ]);

                    $player2 = Player::create([
                        'name' => "Player " . ($i + 1 + $existingPlayerCount),
                        'tournament_id' => $tournament->id,
                        'team_id' => $team->id,
                        'order' => $i + 1,
                    ]);
                }
            }
        }
    }

    private function createCourts(Tournament $tournament)
    {
        $existingCourtCount = $tournament->courts->count();
        $requiredCourtCount = $tournament->number_of_courts;

        if ($existingCourtCount < $requiredCourtCount) {
            $courtsToCreate = $requiredCourtCount - $existingCourtCount;

            for ($i = 1; $i <= $courtsToCreate; $i++) {
                Court::create([
                    'name' => "Court" . ($i + $existingCourtCount),
                    'tournament_id' => $tournament->id,
                ]);
            }
        }
    }

    public function createGamesForTournament(Tournament $tournament)
    {
        $players = $tournament->players->shuffle();
        $gameFormat = $tournament->game_format;

        if ($gameFormat == 0) {
            $gamesPerPlayer = count($players) - 1;
            $this->createGamesFor1vs1($tournament, $players, $gamesPerPlayer);
        } elseif ($gameFormat == 1) {
            $teams = $tournament->teams->shuffle();
            $gamesPerTeam = count($teams) - 1;
            $this->createGamesFor2vs2($tournament, $teams, $gamesPerTeam);
        }
    }

    private function createGamesFor1vs1(Tournament $tournament, $players, $gamesPerPlayer)
    {
        $playerCount = count($players);

        for ($i = 0; $i < $playerCount; $i++) {
            for ($j = $i + 1; $j < $playerCount; $j++) {
                $game = Game::create([
                    'tournament_id' => $tournament->id,
                    'court_id' => null,
                ]);

                $this->assignPlayersToGame($tournament, $game, [$players[$i], $players[$j]]);
            }
        }
    }

    private function createGamesFor2vs2(Tournament $tournament, $teams, $gamesPerTeam)
    {
        $teamCount = count($teams);

        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = $i + 1; $j < $teamCount; $j++) {
                $game = Game::create([
                    'tournament_id' => $tournament->id,
                    'court_id' => null,
                ]);

                $team1Players = $teams[$i]->players;
                $team2Players = $teams[$j]->players;
                $this->assignPlayersToGame($tournament, $game, [$team1Players[0], $team1Players[1], $team2Players[0], $team2Players[1]]);
            }
        }
    }

    private function assignPlayersToGame(Tournament $tournament, Game $game, $players)
    {
        $playerIds = [];

        foreach ($players as $player) {
            $playerIds[] = $player->id;
        }

        $game->players()->attach($playerIds);
    }

    public function getTournamentsWithStats()
    {
        $tournaments = $this->tournament->get();

        foreach ($tournaments as $tournament) {
            $gameIds = Game::where('tournament_id', $tournament->id)->pluck('id');

            $gamesInProgress = Game::whereIn('id', $gameIds)
                ->whereHas('gameTracking', function ($query) {
                    $query->where('status', 'ongoing');
                })
                ->count();

            $completedGames = Game::whereIn('id', $gameIds)
                ->whereHas('result')
                ->count();

            $totalPlayers = $tournament->players->count();
            $gamesPerPlayer = 0;

            if ($tournament->tournament_format == 0) { // Round Robin
                if ($tournament->game_format == 0) { // 1vs1
                    $gamesPerPlayer = $totalPlayers - 1;
                } elseif ($tournament->game_format == 1) { // 2vs2
                    $gamesPerPlayer = ($totalPlayers / 2) - 1;
                }
            }

            $totalGames = ($totalPlayers * $gamesPerPlayer) / 2;
            $remainingGames = $totalGames - $completedGames - $gamesInProgress;

            $tournament->gamesInProgress = $gamesInProgress;
            $tournament->completedGames = $completedGames;
            $tournament->remainingGames = ($remainingGames < 0) ? 0 : $remainingGames; // Negatif değerleri sıfır yap
        }

        return $tournaments;
    }

    public function simulateGameResult(Game $game, $tie = false)
    {
        $gameTracking = GameTracking::where('game_id', $game->id)->first();
        $gameDurationSeconds = $gameTracking->duration_seconds;

        $scoreFormat = $game->tournament->score_format;
        $score = $this->simulateGameScore($scoreFormat);

        $result = Result::create([
            'game_id' => $game->id,
            'score' => $score,
            'game_duration_seconds' => $gameDurationSeconds,
        ]);

        $players = $game->players->shuffle();

        if ($game->players->count() == 2) {
            $winner = $players->first();
            $loser = $players->last();

            $this->createPlayerResult($result, $winner, 0, 1);
            $this->createPlayerResult($result, $loser, 0, 0);
        }
        elseif ($game->players->count() == 4) {
            $winningTeam = $players->take(2);
            $losingTeam = $players->splice(2);

            foreach ($winningTeam as $winner) {
                $this->createPlayerResult($result, $winner, 0, 1);
            }

            foreach ($losingTeam as $loser) {
                $this->createPlayerResult($result, $loser, 0, 0);
            }
        }

        if ($tie) {
            foreach ($players as $player) {
                $this->createPlayerResult($result, $player, 0, 2);
            }
        }

        $this->createAndStartNewGames($game->tournament, $game);
    }
    private function createPlayerResult(Result $result, Player $player, $score, $status)
    {
        PlayerResult::create([
            'result_id' => $result->id,
            'player_id' => $player->id,
            'score' => 0,
            'status' => $status,
        ]);
    }

    private function simulateGameScore($scoreFormat)
    {
        if ($scoreFormat == 0) {
            return rand(0, 11) . '-' . rand(0, 11);
        } elseif ($scoreFormat == 1) {
            return rand(0, 15) . '-' . rand(0, 15);
        } elseif ($scoreFormat == 2) {
            return rand(0, 21) . '-' . rand(0, 21);
        }

        return '0-0';
    }

    private function createAndStartNewGames(Tournament $tournament, $game)
    {
        $tournament->load('courts', 'players', 'teams');

        $newGame = Game::whereDoesntHave('gameTracking')
            ->where('tournament_id', $tournament->id)
            ->first();

        if ($newGame) {
            $averageGameTimeMinutes = $tournament->average_game_time;
            $randomDurationMinutes = rand(-5, 5);
            $gameDurationMinutes = $averageGameTimeMinutes + $randomDurationMinutes;
            $gameDurationSeconds = $gameDurationMinutes * 60;

            $newGame->gameTracking()->create([
                'status' => 'ongoing',
                'start_time' => now(),
                'duration_seconds' => $gameDurationSeconds,
            ]);

            $newGame->update([
                'court_id' => $game->court_id,
            ]);
        }

        $game->update([
            'court_id' => null,
        ]);
    }
}
