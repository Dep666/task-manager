<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateUserCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-user-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует уникальные коды для всех пользователей, у которых их нет';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNull('user_code')->get();
        
        if ($users->isEmpty()) {
            $this->info('Нет пользователей без кодов.');
            return 0;
        }
        
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();
        
        foreach ($users as $user) {
            $user->user_code = User::generateUserCode();
            $user->save();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Сгенерированы коды для ' . count($users) . ' пользователей.');
        
        return 0;
    }
}
