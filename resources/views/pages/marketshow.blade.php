   @extends('main')
   @section('main')
   	<link rel="stylesheet" href="{{asset('css/flipclock.css')}}">
      <section class="section">
        <div class="container">
            <div class="tile is-ancestor is-vertical">
               	<div class="tile has-text-right">
               		@if($countdown)
            			<div class="clock"></div>            	
            		@endif
            	</div>

            	<div class="tile">
	          		<div class="tile is-6 is-parent">
		                <div class="tile is-child" id="app">	                	
		                    <p class="title is-4" id="index-info"><span id="market_id" hidden>{{ $thismarket }}</span><span id="countdown" hidden>{{ $countdown }}</span><span id="index_id" hidden>{{ $thisindex }}</span><span id="showtype" hidden>{{$showtype}}</span><span id="prefix-for-chart">{{$data['market_prefix']}}</span><span id="type-for-chart">指数</span>: <span class="stock-detail" id="curr_index"></span>  <span class="stock-detail" id="change_in_points"></span>  <span class="stock-detail" id="change_in_percent"></span>  <span class="stock-detail" id="transaction_volume"></span>  <span class="stock-detail" id="transaction_amount"></span></p>

		                    @if(isset($index[$config['default_index']]['status']) && $index[$config['default_index']]['status'])

			                    <nav class="breadcrumb is-centered" aria-label="大盘指数" id="chart-nav">
			                      <ul>
			                      	  <li class="is-active"><a aria-current="page" id="min-chart">分时线</a></li>
			                      	  @if ($thismarket == 'sh' || $thismarket == 'sz')
			                          <li><a id="dayK-chart">日K线</a></li>
			                          <li><a id="weekK-chart">周K线</a></li>
			                          <li><a id="monthK-chart">月K线</a></li>
			                         @endif
			                      </ul>                      
			                    </nav>
			                    <figure class="image is-545*300" id="chart">
			                       	<img src="{{ $data['img_url'] }}">	                        
			                    </figure>
		                    @endif
		                    
		                    @if($showtype)
		                    	
		                    <!--		            
					          <nav class="panel">
								<div class="panel-block">
								    <p class="control has-icons-left">
								      <input class="input is-small" type="text" placeholder="search">
								      <span class="icon is-small is-left">
								        <i class="fa fa-search"></i>
								      </span>
								    </p>
								  </div>
					          </nav>
					          -->
					          	<search-share thislocation="{{$location}}"></search-share>
					        
				          	@endif 

		                </div>                

	          		</div>

	          		<div class="tile is-6 is-parent">
	          			<article class="tile is-child">
	          				<p class="title is-4">{{$data['market_prefix']}}活跃股票</p>
	          				<p class="subtitle has-text-right">更新于<span id="updated_at">{{$data['market_rank']['date']}}</span></p>
	          				<hr>
	          				<nav class="breadcrumb is-centered" aria-label="活跃股票" id="active-nav">
	          					<ul>
	          					<li class="is-active"><a aria-current="page" id="up-active">涨幅榜</a></li>
	          					<li><a id="down-active">跌幅榜</a></li>
	          					</ul>
	          				</nav>
		          			<table class="table">
								<thead>
								    <tr>
								      <th><abbr title="股票代码">Id</abbr></th>
								      <th><abbr title="公司名称">Cname</abbr></th>
								      <th><abbr title="最新价">Nprice</abbr></th>
								      <th><abbr title="涨跌价">Cprice</abbr></th>
								      <th><abbr title="涨跌额">Cperct</abbr></th>
								      <th><abbr title="成交量(百万股)">Tvolume</abbr></th>
								      <th><abbr title="成交额(百万元)">Tamount</abbr></th>
								      					      
								    </tr>
								</thead>
								<tbody id="rank-info">
									@foreach($data['market_rank']['up'][$thismarket] as $d)
										<tr id="{{'tr_' . $d['stock_id']}}">
											@if($showtype)
												<td class="share-trade">
													<span class="icon is-small has-text-success">
	  													<i class="fa fa-money"></i>
													</span>
													@if($thismarket == 'ny' || $thismarket == 'nas')
														<a href="{{'/showtradeform/'  . 'us' . '/' . strtolower($d['stock_id'])}}">{{$d['stock_id']}}</a>
													@else
														<a href="{{'/showtradeform/'  . $thismarket . '/' . strtolower($d['stock_id'])}}">{{$d['stock_id']}}</a>
													@endif
												</td>
											@else
												<td>{{$d['stock_id']}}</td>					
											@endif
											<td><abbr title="{{$d['stock_name']}}">{{str_limit($d['stock_name'], $limit = 10, $end = '...')}}</abbr></td>										
											<td class="{{$value_up_class}}">{{$d['trading_price']}}</td>
											<td class="{{$value_up_class}}">{{$d['change_price']}}</td>
											<td class="{{$value_up_class}}">{{$d['change_percent']}}</td>
											<td>{{$d['volume']}}</td>
											<td>{{$d['amount']}}</td>	
										</tr>	
									@endforeach
								</tbody>
								<tfoot>
								    <tr>
								      <th><abbr title="股票代码">Id</abbr></th>
								      <th><abbr title="公司名称">Cname</abbr></th>
								      <th><abbr title="最新价">Nprice</abbr></th>
								      <th><abbr title="涨跌价">Cprice</abbr></th>
								      <th><abbr title="涨跌额">Cperct</abbr></th>
								      <th><abbr title="成交量(百万股)">Tvolume</abbr></th>
								      <th><abbr title="成交额(百万元)">Tamount</abbr></th>							      					      
								    </tr>
								</tfoot>							
		          			</table>
		          			
	          			</article>
	          		</div>
	          	</div>
	          </div>
          	
        </div>
      </section>
    
   @stop
   <meta name="csrf-token" content="{{ csrf_token() }}">
        
@section('scripts')  	
  <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>
  <script src="{{asset('js/flipclock.min.js')}}"></script>
  <script src="{{asset('js/market.js')}}"></script>  
  <script src="{{asset('js/app.js')}}"></script>
@stop
   