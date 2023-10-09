<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
          <url>
            <loc>https://www.example.com/foo.html</loc>
            <lastmod>2022-06-04</lastmod>
          </url>
        </urlset>';

        $routes = collect(config('site.maps'));
        foreach ($routes as $name => $params) {
            if (!empty($params)) {
                $x = "under construction";
            } else {
                $x = route($name);
            }
            dump($x);
    // if (!in_array('nova', $value->gatherMiddleware()) &&
    //     !in_array('nova:api', $value->gatherMiddleware()))
    // {
    // dump($value);
    // }
}
        try {
            dump(public_path('/sitemap.xml'));
            $x = Storage::put(public_path('/sitemap.xml'), $content);
            dd($x);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
