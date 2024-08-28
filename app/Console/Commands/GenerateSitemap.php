<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // modify this to your own needs
        SitemapGenerator::create(config('app.url'))
            ->getSitemap()
            ->add(Url::create(config('app.url'). '/api/v1/products')->setPriority(1)->addAlternate('/api/v1/products', 'nl'))
            ->add(Url::create(config('app.url'). '/api/v1/categories')->setPriority(0.5)->addAlternate('/api/v1/categories', 'nl'))
            ->writeToFile(base_path('sitemap.xml'));
    }
}
