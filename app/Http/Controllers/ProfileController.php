<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Tools\CacheDrive;
use App\Tools\MarketTimeZone;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use App\Profile;
use App\Avator;
use Imageupload;
use Session;
use Cache;
use File;
use Response;

class ProfileController extends Controller
{
    use MarketTimeZone;

    protected $local_times, $open_times;    
    protected $config  = array();
    protected $market = array();
    protected $index = array();

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('cache');
        $this->config = Cache::get('global_config');
        $this->market = Cache::get('market');
        $this->index = Cache::get('index');
        $this->currency = Cache::get('currency'); 
        $this->local_times = $this->get_local_time();
        $this->open_times = config('share.MarketOpenTime');               
    }

    public function myprofile(){
    	$myid = Session::get('iid');
    	$profile = Profile::where('belongsTo', $myid)->first();
        $avator="";
        if(Session::get('iid'))        
            $avator = Investor::find($myid)->avator()->first();
        //dd($avator->original_filename);
		
        $open_markets = $this->opened_markets($this->open_times, $this->local_times);
        return view('profile.myprofile')->withMyprofile($profile)->with('LargeAvator', $avator)->withMarket($this->market)->withIndex($this->index)->withConfig($this->config)->with('open_markets', $open_markets)->withTitle(' | 个人资料')->withAvator($avator);
    }

    public function showavator($iid, $name_encode, $size='large'){
    	if ($size == 'small')
    		$size_index = 'square50_';

    	elseif ($size == 'medium')
    		$size_index = 'square100_';

    	elseif ($size == 'large')
    		$size_index = 'square200_';

    	$avator = Avator::where('belongsTo', $iid)->first();

    	$path = $avator->{$size_index . 'path'} . '/' . $avator->{$size_index . 'filename'};
		
        return Image::make($path)->response();
        /*
	    if(!File::exists($path)) abort(404);

	    $file = File::get($path);
	    $type = File::mimeType($avator->original_mime);

	    $response = Response::make($file, 200);
	    $response->header("Content-Type", $type);
	    return $response;  	
        */
    }

    public function uploadavator(Request $request){
        $ret = array();

       // return Response::json($request->all());

        if ($request->hasFile('avator')) {
            //$data = $request->file('avator');
            //解除原来的avator
            $del_avator = Avator::where('belongsTo', Session::get('iid'))->first();
            if ($del_avator)
                $del_avator->delete();

            $avator = Imageupload::upload($request->file('avator'), Session::get('iid'));
            //dd($avator);
            $ret['iid'] = Session::get('iid');
            $ret['name_encode'] = base64_encode($avator->filename);
            //return json_encode($ret);
            return Response::json($ret);
 
        }
        else{
            $ret['no_upload'] = 'upload failed!';
            return Response::json($ret);  

        }
    }	
}
