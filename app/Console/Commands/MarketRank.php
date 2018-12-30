<?php

namespace App\Console\Commands;
//namespace App\Http\Controllers;


use Illuminate\Console\Command;
//use Cache;
//use App\Http\Controllers\MarketController;
use App\Services\MarketRankService;

class MarketRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketrank:update {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update market rank';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    //use MarketRankService;
    private $marketrank_service;

   public function __construct(MarketRankService $marketrank_service)
   // public function __construct()
    {
        parent::__construct();

        $this->marketrank_service = $marketrank_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $update_type = $this->argument('type');

        //dd($update_type);

        if($update_type == 'all'){
            $this->marketrank_service->update_market_all_cache();

        }
        else{
            $this->marketrank_service->update_market_cache($update_type);
        }
        //$this->info('got it!');
        //$full_path = app_path('Services\MarketRankService.php');
        //$this->info($full_path);
        //$this->info($this->marketrank_service->update_market_all_cache());    
        //$market = new MarketController;

        //$this->update_market_all_cache();
        //$this->marketrank_service->update_market_all_cache();
    }



}
