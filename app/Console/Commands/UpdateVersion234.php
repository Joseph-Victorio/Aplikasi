<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;

class UpdateVersion234 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:update-v-234';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $categories = Category::whereNull('updated_at')->get();

        $this->info($categories->count());

        foreach($categories as $category) {
            $category->updated_at = now();
            $category->save();
        }
    }
}
