<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Tournament;
use App\Services\TournamentService;
use Illuminate\Http\Request;
use App\Http\Requests\CreateTournamentRequest;

class TournamentController
{
    protected $tournamentService;

    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    public function index()
    {
        $tournaments = $this->tournamentService->getTournamentsWithStats();

        return view('tournament.index', [
            'tournaments' => $tournaments,
        ]);
    }

    public function show(Tournament $tournament)
    {
        if (!$tournament) {
            return redirect()->route('tournament.index')->with('error', 'Tournament not found.');
        }

        return view('tournament.show', ['tournament' => $tournament]);
    }

    public function create()
    {
        return view('tournament.create');
    }

    public function store(CreateTournamentRequest $request, TournamentService $tournamentService)
    {
        $data = $request->validated();

        $tournament = $tournamentService->createTournament($data);

        if ($tournament) {
            return redirect()->route('tournament.index')->with('success', 'Tournament ' . $tournament->name . ' created successfully!');
        } else {
            return redirect()->back()->with('error', 'Tournament creation failed. Please check your input and try again.');
        }
    }
    public function edit(Tournament $tournament)
    {
        if (!$tournament) {
            return redirect()->route('tournament.index')->with('error', 'Tournament not found.');
        }

        return view('tournament.edit', ['tournament' => $tournament]);
    }

    public function update(Request $request, Tournament $tournament)
    {
        $validator = $request->validate([
            'name' => 'required|string',
//            'game_format' => 'required',
            'score_format' => 'required',
            'tournament_format' => 'required',
            'average_game_time' => 'required',
//            'number_of_courts' => 'required',
//            'player_limit' => 'required|integer|player_limit_multiple:' . $request->input('game_format'),
        ]);

        if ($tournament->update($validator)) {
            return redirect()->route('tournament.show', $tournament->id)->with('success', 'Tournament ' . $tournament->name . ' updated successfully!');
        } else {
            return redirect()->route('tournament.edit', $tournament->id)->with('error', 'Tournament ' . $tournament->name . ' update failed. Please check your input and try again.');
        }
    }

    public function destroy(Tournament $tournament)
    {
        if($tournament->delete()){
            return redirect()->route('tournament.index')->with('success', 'Tournament '. $tournament->name . ' deleted successfully!');

        }
        return redirect()->route('tournament.index')->with('error', 'Tournament ' . $tournament->name . ' deletion failed. Please try again.');
    }

    public function start(Tournament $tournament)
    {
        if (!$tournament) {
            return redirect()->route('tournament.index')->with('error', 'Tournament not found.');
        }

        if ($tournament->started) {
            return redirect()->route('tournament.show', $tournament->id)->with('error', 'Tournament ' . $tournament->name . '  has already started.');
        }

        $tournament->update(['started' => true]);

        return redirect()->route('tournament.show', $tournament->id)->with('success', 'Tournament ' . $tournament->name . ' has started.');
    }

    public function getGameList(Tournament $tournament)
    {
        if (!$tournament) {
            return response()->json(['error' => 'Tournament not found'], 404);
        }

        $games = Game::with('gameTracking')->where('tournament_id', $tournament->id)->get();

        $gameData = [];

        foreach ($games as $game) {
            $gameStatus = $game->gameTracking->status ?? 'Not Started'; // Varsa gameTracking durumunu kullan, yoksa 'Not Started' varsayılanını kullan
            $remainingTime = $game->gameTracking->duration_seconds ?? 0; // Varsa gameTracking süresini kullan, yoksa 0 varsayılanını kullan

            $gameData[] = [
                'id' => $game->id,
                'status' => $gameStatus,
                'remaining_time' => $remainingTime,
            ];
        }

        return response()->json(['games' => $gameData]);
    }

    public function startGames(Tournament $tournament)
    {
        $games = Game::whereDoesntHave('gameTracking')->where('tournament_id', $tournament->id)->get();
        $tournament->update(['started' => true]);
        foreach ($games as $game) {
            $averageGameTimeMinutes = $tournament->average_game_time;
            $randomDurationMinutes = rand(-5, 5);
            $gameDurationMinutes = $averageGameTimeMinutes + $randomDurationMinutes;
            $gameDurationSeconds = $gameDurationMinutes * 60;

            $gameTracking = $game->gameTracking()->create([
                'status' => 'ongoing',
                'start_time' => now(),
                'duration_seconds' => $gameDurationSeconds,
            ]);
        }


        return redirect()->route('tournament.show', $tournament->id)->with('success', 'Tournament games have started.');
    }
}
