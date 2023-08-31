<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Player;
use App\Models\Court;
use App\Models\Game;
use Illuminate\Http\Request;

class TournamentController
{
    public function index()
    {
        $tournaments = Tournament::get();
        return view('tournament.index', ['tournaments' => $tournaments]);
    }
    public function create()
    {
        return view('tournament.create');
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string',
            'game_format' => 'required',
            'score_format' => 'required',
            'tournament_format' => 'required',
            'average_game_time' => 'required',
            'number_of_courts' => 'required',
            'player_limit' => 'required|integer|player_limit_multiple:' . $request->input('game_format'),
        ]);

        $tournament = Tournament::create($validator);

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

        return redirect()->route('tournament.index')->with('success', 'Tournament created successfully!');
    }
}
