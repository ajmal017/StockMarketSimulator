<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Share;
class SharesTableSeeder extends Seeder
{
	 /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    	
    	$shares = Share::all();

    	if (!empty($shares)){
    		foreach ($shares as $share){
    			$share->status = 1;
    		}	
    	}	
    	$shares->save();
    }
}
