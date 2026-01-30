<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\ReputationService;

class RecalculateReputation extends Command
{
    protected $signature = 'reputation:recalculate';
    protected $description = 'Recalculate reputation scores for all users';

    public function handle(ReputationService $service)
    {
        User::chunk(100, function ($users) use ($service) {
            foreach ($users as $user) {
                $service->calculateAndSave($user);
            }
        });
        $this->info('Reputation recalculated.');
    }
}
