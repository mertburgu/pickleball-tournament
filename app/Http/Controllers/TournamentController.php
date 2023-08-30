<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController
{
    public function createTournament(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'player_limit' => 'required|integer|player_limit_multiple'. $request->input('game_format'),
        ]);

        $tournament = Tournament::create($validatedData);

        $games = Game::whereIn('id', $request->input('game_ids'))->get();
        $tournament->games()->saveMany($games);

        return redirect()->route('tournaments.index')->with('success', 'Turnuva oluşturuldu.');
    }
}
