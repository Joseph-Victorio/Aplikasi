<?php

namespace App\Console\Commands;

use App\Models\Config;
use Illuminate\Console\Command;

class ChangeFieldConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:change-field';

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
        Config::query()->update([
            'rajaongkir_couriers' => NULL
        ]);
    }
}
