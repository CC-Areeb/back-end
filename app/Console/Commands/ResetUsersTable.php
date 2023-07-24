<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetUsersTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-users-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncates the user table and inserts a superadmin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('users')->truncate();
        Artisan::call('db:seed');
        $this->info('Data reset complete!');
    }
}
