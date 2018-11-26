<?php

namespace App\Console\Commands;

use App\Models\Dota2\Champion;
use Illuminate\Console\Command;

class ImportDotaChampions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dota2:import-champions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dota2 champions into database';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            $champions = collect(\GuzzleHttp\json_decode(file_get_contents('https://s3.eu-central-1.amazonaws.com/esportsconstruct-dota2-matches/heroes.json')));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            exit();
        }
        foreach ($champions['heroes'] as $champion) {
            Champion::create([
                'name' => $champion->localized_name,
                'slug_name' => $champion->name,
                'title' => $champion->localized_name,
                'api_id' => $champion->id,
                'info' => '{}',
                'active' => 0,
                'image' => $champion->name . '.png'
            ]);
        }
        $this->info('Champions imported.');
    }
}
