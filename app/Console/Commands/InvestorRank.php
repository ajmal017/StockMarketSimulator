<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvestorRankService;


class InvestorRank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    //type: rank|new_price|active_shares   market: china|america|hk
    protected $signature = 'investorrank:update {type} {market?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update investor rank';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(InvestorRankService $investorrank_service)
    {
        parent::__construct();
        $this->investorrank_service = $investorrank_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $update_type = $this->argument('type');
        $market = $this->argument('market');
        //$overwrite = $this->argument('overwrite');

        if($update_type == 'active_shares'){
            //dd($overwrite);
            if($market == true)
                $this->investorrank_service->update_all_active_shares(true);
            elseif ($market == false)
                $this->investorrank_service->update_all_active_shares(false);
        }
        elseif ($update_type == 'new_price'){
            if(isset($market)){
                if($market == 'us')
                    $this->investorrank_service->make_american_new_price();
                elseif ($market == 'china')
                   $this->investorrank_service->make_chinese_new_price(); 
                elseif ($market == 'hk')
                    $this->investorrank_service->make_hk_new_price();
            }
            else{
                 $this->investorrank_service->make_chinese_new_price(); 
                 $this->investorrank_service->make_american_new_price();
                 $this->investorrank_service->make_hk_new_price();  
            }
        }
        elseif ($update_type == 'rank'){
            if(isset($market)){
                if($market == 'us')
                    $this->investorrank_service->make_american_rank();
                elseif ($market == 'china')
                   $this->investorrank_service->make_chinese_rank(); 
                elseif ($market == 'hk')
                    $this->investorrank_service->make_hk_rank();                
            }
            else{
                 $this->investorrank_service->make_chinese_rank(); 
                 $this->investorrank_service->make_american_rank();
                 $this->investorrank_service->make_hk_rank();                  
            }
        }
        elseif ($update_type == 'show_active_shares'){
            dd($this->investorrank_service->get_active_shares());
        }
        elseif($update_type == 'show_rank_cache'){
            $result = array();
            $result['active_shares'] = $this->investorrank_service->get_active_shares();
            $result['investor_rank'] = $this->investorrank_service->get_ranks();
            $result['active_shares_no'] = $this->investorrank_service->get_active_shares_no();
            $result['active_shares_count'] = $this->investorrank_service->get_active_shares_count();
            $result['make_rank_date'] = $this->investorrank_service->get_make_rank_date();
            $result['make_price_date'] = $this->investorrank_service->get_make_price_date();
            $result['make_rank_offset'] = $this->investorrank_service->get_make_rank_offset();

            dd($result);
        }
    }
}
