<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AddBlogPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:blog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add few dummy blog posts in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('posts')->truncate();
        Artisan::call('db:seed', ['--class' => 'PostSeeder']);
        $this->info('Blogs have been posted!');
    }
}
