<?php

namespace App\Console\Commands;

use App\Country;
use Illuminate\Console\Command;

class ImportCountryImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'country:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import country flags into database';

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
        Country::whereNotNull('filename')->update(['filename' => null]);
        $flags = glob(public_path('img/flags/32/*.png'));
        foreach ($flags as $file) {
            $filename = str_replace('-', ' ', basename($file, '.png'));
            $c = Country::where('countryName', 'like', '%' . $filename . '%')->first();
            if ($c) {
                $c->filename = basename($file);
                $c->save();
                $this->info($c->countryName . ' => ' . basename($file));
            } else {
                $this->error('NO COUNTRY FOR ' . $file);
            }

        }
        // Manual set not catch files
        Country::where('countryName', 'Saint Barthlemy')->update(['filename' => 'Saint-Barthelemy.png']);
        Country::where('countryName', 'Cocos [Keeling] Islands')->update(['filename' => 'Cocos-Keeling-Islands.png']);
        Country::where('countryName', 'Republic of the Congo')->update(['filename' => 'Republic-of-the-Congo.png']);
        Country::where('countryName', 'Faroe Islands')->update(['filename' => 'Faroes.png']);
        Country::where('countryName', 'Guinea-Bissau')->update(['filename' => 'Guinea-Bissau.png']);
        Country::where('countryName', 'Macao')->update(['filename' => 'Macau.png']);
        Country::where('countryName', 'Sint Maarten')->update(['filename' => 'Saint-Martin.png']);
        Country::where('countryName', 'U.S. Virgin Islands')->update(['filename' => 'US-Virgin-Islands.png']);
        Country::where('countryName', 'Samoa')->update(['filename' => 'Samoa.png']);
        $this->info('All done');
    }
}
