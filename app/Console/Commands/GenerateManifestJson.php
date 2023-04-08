<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Models\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateManifestJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:manifest';

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
        $shop = Store::first();
        $config = Config::first();

        $data = [
            'name' => $shop->name,
            'short_name' => $shop->name,
            'description' => $shop->slogan ?? '',
            "display" => "standalone",
            "start_url" => "/",
            "icons" => [
                ["src" => "/icon/icon-96x96.png","sizes" => "96x96","type" => "image/png"],
                ["src" => "/icon/icon-120x120.png","sizes" => "120x120","type" => "image/png"],
                ["src" => "/icon/icon-144x144.png","sizes" => "144x144","type" => "image/png"],
                ["src" => "/icon/icon-152x152.png","sizes" => "152x152","type" => "image/png"],
                ["src" => "/icon/icon-167x167.png","sizes" => "167x167","type" => "image/png"],
                ["src" => "/icon/icon-180x180.png","sizes" => "180x180","type" => "image/png"],
                ["src" => "/icon/icon-192x192.png","sizes" => "192x192","type" => "image/png"]
            ],
            "lang" => "id",
            "background_color" => "#b5cfb4",
            "theme_color" => $config->theme_color,
            "dir" => "ltr",
            "orientation" => "portrait",
            "prefer_related_applications" => false
        ];

        $json = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);

        $filepath = public_path('manifest.json');

        if(File::exists($filepath)) {
            File::delete($filepath);
        }

        File::put($filepath, $json);
    }
}
