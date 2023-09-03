<?php

namespace App\Jobs;

use App\Services\TournamentService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Game;
use App\Models\Tournament;
class FinishExpiredGames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }

    public function handle()
    {
        $expiredGames = Game::whereHas('gameTracking', function ($query) {
            $query->where('status', 'ongoing');
        })->get();

        foreach ($expiredGames as $game) {
            $this->finishExpiredGame($game);
        }
    }

    private function finishExpiredGame(Game $game)
    {
        $gameTracking = $game->gameTracking;

        $startTime = Carbon::parse($gameTracking->start_time);

        $durationSeconds = $gameTracking->duration_seconds;

        $currentTime = now();

        if ($currentTime >= $startTime->addSeconds($durationSeconds)) {
            $gameTracking->status = 'completed';
            $gameTracking->save();

            $this->saveGameResults($game);
        }
    }

    private function saveGameResults(Game $game)
    {
        $tournamentService = new TournamentService($game->tournament);

        $tournamentService->simulateGameResult($game);
    }
}
