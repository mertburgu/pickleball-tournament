<?php

namespace App\Services;

use App\Models\Game;
use App\Models\MatchResult;

class GameService
{
    public function simulateMatches($tournament)
    {
        $matches = Game::where('tournament_id', $tournament->id)
            ->whereDoesntHave('result')
            ->get();

        foreach ($matches as $match) {
            $matchDuration = $tournament->average_game_time + rand(-5, 5);

            $team1Score = rand(0, 11);
            $team2Score = 11 - $team1Score;

            $winner = ($team1Score > $team2Score) ? $match->team1 : $match->team2;
            $loser = ($team1Score < $team2Score) ? $match->team1 : $match->team2;

            $result = Result::create([
                'game_id' => $match->id,
                'score' => $team1Score . '-' . $team2Score,
                'match_duration_seconds' => $matchDuration,
            ]);

            // Kazanan ve kaybeden oyuncuları kaydedin
            PlayerResult::create([
                'result_id' => $result->id,
                'player_id' => $winner->id,
                'score' => $team1Score,
                'status' => 1, // Kazanan
            ]);

            PlayerResult::create([
                'result_id' => $result->id,
                'player_id' => $loser->id,
                'score' => $team2Score,
                'status' => 0, // Kaybeden
            ]);
        }
    }
}
