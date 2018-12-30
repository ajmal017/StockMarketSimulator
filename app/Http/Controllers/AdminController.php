<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Tools\CacheDrive;
use App\Tools\ShowTools;
use Carbon\Carbon;
use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;
use App\Event;
use Session;
use Cache;
use Response;
use App\Http\Controllers\EventController;


class AdminController extends Controller
{
	use AuthenticatesUsers;

	use CacheDrive;	
	use ShowTools;

	protected $perpage = 10; 

    public function __construct(){
    	$this->middleware('admin');
    	$this->middleware('cache');
    }	



    public function showadmin(){
    	$configs = $this->getGlobalConfig();
    	$markets = $this->getMarket();
    	$indice = $this->getIndex();
    	$currency = $this->getCurrency();
    	//dd($configs);
    	$avator = Investor::find(Session::get('iid'))->avator()->first();
    	return view('admin.home')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withTitle(' | 管理')->withAvator($avator);
    }

    public function setglobal(Request $request){
    	$data = $request->all();
    	//dd($data);
    	$validator = $this->validator($data);

        if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Sesssion::flash('failure', $errors);
            return redirect('/admin');
        }

        $configs = $this->getGlobalConfig();

        //dd($configs);

        if (!empty($configs)){
        	$add_configs = array();
        	$update_configs = array();
        	//dd($data);
        	foreach($data as $attr => $val){
        		if (!isset($configs[$attr]) && $attr != '_token')
        			$add_configs[$attr] = $val;
        		else if($attr != '_token')
        			$update_configs[$attr] = $val;
        	}
        	if (!empty($update_configs))
        		$this->updateConfig($update_configs); 
        	if (!empty($add_configs))
        		$this->addConfig($add_configs);       	
        }
        else{

        	$this->addConfig($data);	

        	//add default market: sh,sz,ny,nas   default index: sh,sz,dow,nas
        	$this->addDefaultMarketIndex();
        }

        //dd($add_configs);
        Session::flash('success', '编辑成功');
        return redirect('/admin');
    }

	public function update_admin_cache(){
		$this->update_config_cache();
		$this->update_market_cache();
		$this->update_index_cache();
		$this->update_currency_cache();
		$this->convert_share_config_into_cache();
        Session::flash('success', '缓存更新完毕');
        return redirect('/admin');		
	}  	  

	public function showmarkets(){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		$avator="";
		if(Session::get('iid'))
			$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showmarkets')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withTitle(' | 管理市场')->withAvator($avator);

	}    	

	public function showindice(){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		//dd($indice);
		$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showindice')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withTitle(' | 管理指数')->withAvator($avator);

	}    


	public function showeditmarketform($mid){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		$target = Market::where('market_index', $mid)->first();
		$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showeditmarketform')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withMid($mid)->withId($target->mid)->withTitle(' | 编辑市场')->withAvator($avator);

	}	

	public function showeditindexform($iid){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		$target = Index::where('index_index', $iid)->first();
		$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showeditindexform')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withIid($iid)->withId($target->inid)->withTitle(' | 编辑指数')->withAvator($avator);

	}	

	public function editmarket(Request $request, $id){
		$target = Market::where('mid', $id)->first();
		$data = $request->all();
		if ($target->market_index != $data['market_index']){
			$validator = Validator::make($data, [
				'name' => 'required|min:1|max:255',
				'market_index'	=> 'required|min:2|max:10|unique:markets',
				'query_url_head' => 'required|url',
				'rank_up_url' => 'required',
				'rank_down_url' => 'required',
			]);
		}
		else{
			$validator = Validator::make($data, [
				'name' => 'required|min:1|max:255',
				'query_url_head' => 'required|url',
				'rank_up_url' => 'required',
				'rank_down_url' => 'required',
			]);
		}

       if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Session::flash('failure', $errors);
            return redirect('/admin');
        }
        

        if ($target->market_index !== $request->market_index){
  			$id_changed = 1;
        	$theindex = $target->index()->first();
        	//$theshares = $target->shares()->get();
			
			$theindex->belongsToMarket = $request->market_index;
			$theindex->save();
			$this->update_index_cache(); 

			Share::where('atStockMarket', $target->market_index)->update(['atStockMarket' => $request->market_index]);

        }

        $target->name = $request->name;
        $target->market_index = $request->market_index;
        $target->query_url_head = $request->query_url_head;
        $target->rank_up_url = $request->rank_up_url;
        $target->rank_down_url = $request->rank_down_url;
        $target->status = $request->status;
        $target->allowed_currency = $request->allowed_currency;
        $target->save();
        $this->update_market_cache();      	

        $m_data = [
        	'type' => 'updatemarket',
        	'market' => $target->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		Session::flash('success', '编辑成功');
		//return redirect(route('admin_show_edit_market_form', ['market_index' => $target->market_index]))->withConfig($configs)->withMarket($markets)->withIndex($indice)->withMid($target->market_index)->withId($target->mid)->withTitle(' | 编辑市场');	        
		return redirect(route('admin_show_edit_market_form', ['market_index' => $target->market_index]));	     
	}	

	public function editindex(Request $request, $id){
		$target = Index::where('inid', $id)->first();
		$data = $request->all();

		if ($target->index_index != $data['index_index']){
			$validator = Validator::make($data, [
				'name' => 'required|min:1|max:255',
				'index_index'	=> 'required|min:2|max:10|unique:markets_index',
				'index_query_url' => 'required|url',
				'min_chart_url' => 'required',
				'belongsToMarket' => 'required|min:2|max:10',
			]);
		}
		else{
			$validator = Validator::make($data, [
				'name' => 'required|min:1|max:255',
				'index_query_url' => 'required|url',
				'min_chart_url' => 'required',
				'belongsToMarket' => 'required|min:2|max:10',
			]);
		}

       if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Session::flash('failure', $errors);
            return redirect('/admin');
        }

        

        $target->name = $request->name;
        $target->index_index = $request->index_index;
        $target->index_query_url = $request->index_query_url;
        $target->min_chart_url = $request->min_chart_url;
        $target->belongsToMarket = $request->belongsToMarket;
        $target->status = $request->status;

        $target->save();
        $this->update_index_cache();        

        $m_data = [
        	'type' => 'updateindex',
        	'index' => $target->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		Session::flash('success', '编辑成功');
		//return redirect(route('admin_show_edit_index_form', ['index_index' => $target->index_index]))->withConfig($configs)->withMarket($markets)->withIndex($indice)->withIid($target->index_index)->withId($target->inid)->withTitle(' | 编辑指数');        
		return redirect(route('admin_show_edit_index_form', ['index_index' => $target->index_index]));  
	}

	public function showaddmarketform(){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();	
		$avator = Investor::find(Session::get('iid'))->avator()->first();	
		return view('admin.showaddmarketform')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withTitle(' | 添加市场')->withAvator($avator);

	}

	public function showaddindexform(){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		$avator = Investor::find(Session::get('iid'))->avator()->first();		
		return view('admin.showaddindexform')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withTitle(' | 添加指数')->withAvator($avator);

	}

	public function addmarket(Request $request){
		$data = $request->all();
		$validator = Validator::make($data, [
			'name' => 'required|min:1|max:255',
			'market_index'	=> 'required|min:2|max:10|unique:markets',
			'query_url_head' => 'required|url',
			'rank_up_url' => 'required',
			'rank_down_url' => 'required',
		]);

       if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Session::flash('failure', $errors);
            return redirect('/admin');
        }

        $target = new Market;
        $target->name = $request->name;
        $target->market_index = $request->market_index;
        $target->query_url_head = $request->query_url_head;
        $target->rank_up_url = $request->rank_up_url;
        $target->rank_down_url = $request->rank_down_url;
        $target->allowed_currency = $request->allowed_currency;

        $target->save();
        $this->update_market_cache();

        $m_data = [
        	'type' => 'addmarket',
        	'market' => $target->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		Session::flash('success', '添加成功');
		//return redirect(route('admin_show_markets'))->withConfig($configs)->withMarket($markets)->withIndex($indice)->withTitle(' | 管理市场');        
		return redirect(route('admin_show_markets'));
	}

	public function addindex(Request $request){
		$data = $request->all();
		$validator = Validator::make($data, [
			'name' => 'required|min:1|max:255',
			'index_index'	=> 'required|min:2|max:10|unique:markets_index',
			'index_query_url' => 'required|url',
			'min_chart_url' => 'required',
			'belongsToMarket' => 'required|min:2|max:10',
		]);

       if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Session::flash('failure', $errors);
            return redirect('/admin');
        }

		$target = new Index;
        $target->name = $request->name;
        $target->index_index = $request->index_index;
        $target->index_query_url = $request->index_query_url;
        $target->min_chart_url = $request->min_chart_url;
        $target->belongsToMarket = $request->belongsToMarket;
        
        $target->save();
        $this->update_index_cache(); 

        $m_data = [
        	'type' => 'addindex',
        	'index' => $target->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		Session::flash('success', '添加成功');
		//return redirect(route('admin_show_indice'))->withConfig($configs)->withMarket($markets)->withIndex($indice)->withTitle(' | 管理指数');     		
		return redirect(route('admin_show_indice'));
	}		

	public function showcurrency(){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		$result = $this->paginate($currency, $this->perpage);
		$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showcurrency')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($result)->withTitle(' | 管理货币')->withAvator($avator);		

	}	

	public function showeditcurrencyform($cid){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		$target = Currency::where('currency_index', $cid)->first();
		$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showeditcurrencyform')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withCid($cid)->withId($target->cid)->withTitle(' | 编辑货币')->withAvator($avator);		

	}

	public function showaddcurrencyform(){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		$avator = Investor::find(Session::get('iid'))->avator()->first();		
		return view('admin.showaddcurrencyform')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withTitle(' | 添加货币')->withAvator($avator);	

	}		

	public function addcurrency(Request $request){
		$data = $request->all();

		
		$validator = Validator::make($data, [
			'name' => 'required|min:1|max:255',
			'currency_index'	=> 'required|min:2|max:10|unique:currency',
			'rate_query_url' => 'required|url',
		]);
		


       if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Session::flash('failure', $errors);
            return redirect('/admin');
        }		

		$target = new Currency;
        $target->name = $request->name;
        $target->currency_index = $request->currency_index;
        $target->rate_query_url = $request->rate_query_url;
                
        $target->save();
        $this->update_currency_cache();  
        $m_data = [
        	'type' => 'addcurrency',
        	'currency' => $target->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		Session::flash('success', '添加成功');
		//return redirect(route('admin_show_indice'))->withConfig($configs)->withMarket($markets)->withIndex($indice)->withTitle(' | 管理指数');     		
		return redirect(route('admin_show_currency'));

	}	

	public function editcurrency(Request $request, $id){
		$target = Currency::where('cid', $id)->first();
		$data = $request->all();

		if ($target->currency_index != $data['currency_index']){
			$validator = Validator::make($data, [
				'name' => 'required|min:1|max:255',
				'currency_index'	=> 'required|min:2|max:10|unique:currency',
				'rate_query_url' => 'required|url',
			]);
		}
		else{
			$validator = Validator::make($data, [
				'name' => 'required|min:1|max:255',
				'rate_query_url' => 'required|url',
			]);
		}

       if ($validator->fails()){
            $errors = implode(', ', $validator->errors()->all());
            Session::flash('failure', $errors);
            return redirect('/admin');
        }

        

        $target->name = $request->name;
        $target->currency_index = $request->currency_index;
        $target->rate_query_url = $request->rate_query_url;
        $target->status = $request->status;

        $target->save();
        $this->update_currency_cache();        

        $m_data = [
        	'type' => 'updatecurrency',
        	'currency' => $target->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		Session::flash('success', '编辑成功');
		//return redirect(route('admin_show_edit_index_form', ['index_index' => $target->index_index]))->withConfig($configs)->withMarket($markets)->withIndex($indice)->withIid($target->index_index)->withId($target->inid)->withTitle(' | 编辑指数');        
		return redirect(route('admin_show_edit_currency_form', ['currency_index' => $target->currency_index]));  

	}

	public function showinvestors(){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();

		$investors = Investor::paginate($this->perpage);

		$shares = array();
		foreach($investors as $investor){
			if($investor->shares()->wherePivot('amount', '>' , 0)->exists())
				$shares[$investor->iid] = $investor->shares()->wherePivot('amount', '>' , 0)->get()->keyBy(function($share){
													return $share->sid;
											});
		}

		$avator = Investor::find(Session::get('iid'))->avator()->first();		
		//dd($shares[1][6]->pivot);
		return view('admin.showinvestors')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withInvestors($investors)->withShares($shares)->withTitle(' | 管理玩家')->withAvator($avator);
	}	

	public function editinvestor($iid, Request $request){
		$ret = array();
		$data = $request->all();
		$validator = Validator::make($data, [
			'coins' => 'required|numeric|min:1',
			'level'	=> 'required|numeric|min:0',
			'bind_to' => 'required',
		]);	
		if ($validator->fails()){
			$ret['msg'] = 'validation failed';
			return Response::json($ret);
		}			
		$investor = Investor::find($iid);
		$investor->fill($data);
		$investor->save();

        $m_data = [
        	'type' => 'updateinvestor',
        	'investor' => $investor->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		$ret['msg'] = 'update success';
		return Response::json($ret);		
	}

	public function baninvestor($iid){
		$investor = Investor::find($iid);
		$investor->level = 0;
		$investor->save();

        $m_data = [
        	'type' => 'baninvestor',
        	'investor' => $investor->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		$ret['msg'] = 'ban success';
		return Response::json($ret);
	}

	public function showshares($iid){
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();

		$owner = Investor::find($iid);
		$shares = $owner->shares()->wherePivot('amount', '>' , 0)->get();

		$shares->each(function($share, $index){
			$share->limit_buying_at = date('Y-m-d', Carbon::parse($share->pivot->buying_at)->timestamp);
			$share->limit_selling_at = date('Y-m-d', Carbon::parse($share->pivot->selling_at)->timestamp);
		});
		$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showshares')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withOwner($owner)->withShares($shares)->withTitle(' | 管理玩家股票')->withAvator($avator);
	}

	public function editshare($sid, Request $request){
		$ret = array();
		$data = $request->all();
		//return Response::json($data);
		$validator = Validator::make($data, [
			'owner' => 'required',
			'buying_price' => 'required|numeric|min:1',
			'selling_price'	=> 'required|numeric|min:0',
			'amount' => 'required|numeric|min:1',			
		]);			
		if ($validator->fails()){
			$errors = implode(', ', $validator->errors()->all());
			$ret['msg'] = 'validation failed';
			$ret['errors'] = $errors;
			return Response::json($ret);
		}

		$p_data = [
			'buying_price' => $data['buying_price'],
			'selling_price' => 0,
			'amount' => $data['amount'],
		];
		//return Response::json($data);
		$owner = Investor::find($data['owner']);

		$owner->shares()->updateExistingPivot($sid, $p_data);

		$theshare = $owner->shares()->wherePivot('sid', $sid)->first();

        $m_data = [
        	'type' => 'updateshare',
        	'investor' => $owner->name,
        	'share' => $theshare->name,
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type']));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		$ret['msg'] = 'update success';
		return Response::json($ret);		

	}

	public function forcesell($sid, Request $request){
		$ret = array();
		$data = $request->all();
		$extra = null;
		$validator = Validator::make($data, [
			'owner' => 'required',
			'buying_price' => 'required|numeric|min:1',
			'selling_price'	=> 'required|numeric|min:0',
			'amount' => 'required|numeric|min:0',
		]);			
		if ($validator->fails()){
			$errors = implode(', ', $validator->errors()->all());
			$ret['msg'] = 'validation failed';
			$ret['errors'] = $errors;
			return Response::json($ret);
		}			

		$owner = Investor::find($data['owner']);
		//return Response::json($data);
		$theshare = $owner->shares()->wherePivot('sid', $sid)->first();
		$share = $theshare->pivot;
		//return Response::json($share);
		$prev_selling_price = $share->selling_price;
		$prev_amount = $share->amount;

		if(intval($data['amount']) >= intval($prev_amount)){
			$data['amount'] = 0;
			$extra = 1;
		}

		if(floatval($data['selling_price']) <= 0)
			$data['selling_price'] = $data['buying_price'];



		$gain = ( intval($prev_amount) - intval($data['amount']) ) * floatval($data['selling_price']);

		$owner->coins = round(floatval($owner->coins) + $gain, 2);
		//return Response::json($owner);

		$owner->save();

		$p_data = [
			'buying_price' => $data['buying_price'],
			'selling_price' => $data['selling_price'],
			'amount' => $data['amount'],
		];


		$owner->shares()->updateExistingPivot($sid, $p_data);

        $m_data = [
        	'type' => 'sellshare',
        	'investor' => $owner->name,
        	'share' => $theshare->name,
        	'amount' => intval($prev_amount) - intval($data['amount']),
        	'admin' => Session::get('username'),
        ];
        $e_msg = addslashes($this->show_admin_msg($m_data, $m_data['type'], $extra));
		$event = [
			'type' => 3,
			'event' => $e_msg,
			'bywhom' => Session::get('iid'),
		];
		
		EventController::create_event($event);

		$ret['msg'] = 'update success';
		$ret['sellamount'] = intval($prev_amount) - intval($data['amount']);
		$ret['selling_price'] = $data['selling_price'];
		$ret['gain'] = round($gain, 2);

		return Response::json($ret);	
	}

	public function showevents($filter = null, $orderby = 'desc'){
		$type_map = [
			1 => '玩家交易',
			2 => '玩家更新',
			3 => '后台管理',
		];
		$configs = $this->getGlobalConfig();
		$markets = $this->getMarket();
		$indice = $this->getIndex();
		$currency = $this->getCurrency();
		/*
		if($filter == 'trade')
			$events = Event::orderBy('updated_at', $orderby)->where('type', 1)->paginate(15);
		elseif($filter == 'update')
			$events = Event::orderBy('updated_at', $orderby)->where('type', 2)->paginate(15);
		elseif($filter == 'admin')
			$events = Event::orderBy('updated_at', $orderby)->where('type', 3)->paginate(15);
		else
			$events = Event::orderBy('updated_at', $orderby)->paginate(15);
		*/

		$events = Event::orderBy('updated_at', 'desc')->paginate(15);	
		$avator = Investor::find(Session::get('iid'))->avator()->first();
		return view('admin.showeventsform')->withConfig($configs)->withMarket($markets)->withIndex($indice)->withCurrency($currency)->withEvents($events)->withTypemap($type_map)->withTitle(' | 管理日志/事件')->withAvator($avator);				
	}

	public function ajax_showevents($filter, $orderby){
		$type_map = [
			1 => '玩家交易',
			2 => '玩家更新',
			3 => '后台管理',
		];

		if($filter == 'trade')
			$events = Event::orderBy('updated_at', $orderby)->where('type', 1)->paginate(15);
		elseif($filter == 'update')
			$events = Event::orderBy('updated_at', $orderby)->where('type', 2)->paginate(15);
		elseif($filter == 'admin')
			$events = Event::orderBy('updated_at', $orderby)->where('type', 3)->paginate(15);
		else
			$events = Event::orderBy('updated_at', $orderby)->paginate(15);

		$ret['links'] = $events->links('partials.pagination');
		$events = $events->each(function($event, $k){
			$event->event = stripcslashes($event->event);
		});

		foreach($events as $event){
			$event->type_in_words = $type_map[$event->type];
		}

		$ret['events'] = $events->toArray();		

		return Response::json($ret);					
	}


	public function ajax_delevents(Request $request){
		$ret = array();
		$data = $request->all();

		Event::whereIn('eid', $data['del_eids'])->delete();
		$ret = $data['del_eids'];
		return Response::json($ret);
	}

	protected function validator(array $data){
	    
        return Validator::make($data, [
            'webname' => 'required|string|min:5|max:255',
            'weburl' => 'required|url',
            'init_credits' => 'required|integer|min:10000',
            'shares_rank_expiration' => 'required|integer|min:1',
            'headline_expiration' => 'required|integer|min:1',
        ]);
	
	}

	function paginate($items, $perPage)
	{
	    if(is_array($items)){
	        $items = collect($items);
	    }

	    return new LengthAwarePaginator(
	        $items->forPage(Paginator::resolveCurrentPage() , $perPage),
	        $items->count(), $perPage,
	        Paginator::resolveCurrentPage(),
	        ['path' => Paginator::resolveCurrentPath()]
	    );
	}


}
