<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FinishExpiredGames;

class RunFinishExpiredGames extends Command
{
    protected $signature = 'run:finish-expired-games';
    protected $description = 'Run the FinishExpiredGames job';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Running FinishExpiredGames job...');
        dispatch(new FinishExpiredGames());
        $this->info('FinishExpiredGames job has been dispatched.');
    }
}
