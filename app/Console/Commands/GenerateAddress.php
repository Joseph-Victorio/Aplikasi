<?php

namespace App\Console\Commands;

use App\Models\Address;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'address:generate';

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
        DB::connection('rajaongkir')->table('subdistricts')->orderBy('id')->chunk(100, function ($addresses) {
            foreach ($addresses as $address) {
                Address::create([
                    'subdistrict_name' => $address->subdistrict_name,
                    'subdistrict_id' => $address->subdistrict_id,
                    'city' => $address->city,
                    'city_id' => $address->city_id,
                    'type' => $address->type,
                    'province' => $address->province,
                ]);
            }
        });
    }
}
