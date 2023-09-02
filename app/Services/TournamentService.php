<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\Player;
use App\Models\Team;
use App\Models\Court;
use App\Models\Game;

class TournamentService
{
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

        $tournament = Tournament::create($data);
        $tournament->load('courts', 'players', 'teams');

        $this->createPlayersAndTeams($tournament);
        $this->createCourts($tournament);
        $this->assignPlayersToGames($tournament);

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
                    'name' => "Court". ($i + $existingCourtCount),
                    'tournament_id' => $tournament->id

                ]);
            }
        }
    }

    private function assignPlayersToGames(Tournament $tournament)
    {
        $tournament->load('courts', 'players', 'teams');

        foreach($tournament->courts as $index => $court){
            if ($tournament->game_format == 0) {

                $game = Game::create([
                    'tournament_id' => $tournament->id,
                    'court_id' => $court->id,
                ]);

                $player1 = Player::where('tournament_id', $tournament->id)
                    ->whereDoesntHave('games')
                    ->first();

                $game->players()->attach([$player1->id]);

                $player2 = Player::where('tournament_id', $tournament->id)
                    ->whereDoesntHave('games')
                    ->first();

                $game->players()->attach([$player2->id]);

            } elseif ($tournament->game_format == 1) {

                $teams = Team::where('tournament_id', $tournament->id)
                    ->whereHas('players', function ($query) {
                        $query->whereDoesntHave('games');
                    })
                    ->inRandomOrder()
                    ->take(2)
                    ->get();


                if ($teams->count() == 2) {
                    $game = Game::create([
                        'tournament_id' => $tournament->id,
                        'court_id' => $court->id,
                    ]);

                    $playersToAttach = [];

                    foreach ($teams as $team) {
                        foreach ($team->players as $player) {
                            if (!$player->games->where('court_id', $court->id)->count()) {
                                $playersToAttach[] = $player->id;
                            }
                        }
                    }

                    if (count($playersToAttach) == 4) {
                        $game->players()->attach($playersToAttach);
                    }
                }
            }
        }
    }

    public function getTournamentsWithStats()
    {
        $tournaments = Tournament::get();

        foreach ($tournaments as $tournament) {
            $gameIds = Game::where('tournament_id', $tournament->id)->pluck('id');

            $matchesInProgress = Game::whereIn('id', $gameIds)
                ->whereDoesntHave('result')
                ->count();

            $tournament->matchesInProgress = $matchesInProgress;

            $completedMatches = Game::whereIn('id', $gameIds)
                ->whereHas('result')
                ->count();

            $tournament->completedMatches = $completedMatches;

            $totalPlayers = $tournament->players->count();
            if ($tournament->tournament_format == 0) { // Round Robin
                if ($tournament->game_format == 0) { // 1vs1
                    $matchesPerPlayer = $totalPlayers - 1;
                } elseif ($tournament->game_format == 1) { // 2vs2
                    $matchesPerPlayer = ($totalPlayers / 2) - 1;
                }
            }
            $totalMatches = ($totalPlayers * $matchesPerPlayer) / 2;
            $remainingMatches = $totalMatches - $completedMatches - $matchesInProgress;
            $tournament->remainingMatches = $remainingMatches;
        }

        return $tournaments;
    }
}
