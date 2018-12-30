@extends('main')
@section('main')

<section class="section">
	<div class="container">

		<div class="image">
			@if(!isset($avator) || !$avator)
				<div class="avatar-medium" id="current-avator" style="background-image: url(http://bulma.io/images/placeholders/256x256.png)"></div>	
			@else
				<div class="avatar-medium" id="current-avator" style="background-image: url({{'/avator/' . $avator->belongsTo . '/' . base64_encode($avator->filename) . '/medium'}})"></div>	
			@endif
		</div>	
			
		<div class="tile is-ancestor">
		<!--个人资料看板-->
			<div class="tile is-6">
				<div class="div-box">					
					<p class="title is-4">{{Session::get('username')}}个人财产</p>
					<hr>
					<p><span class="box-title">账号资产：</span><span id="coins">{{$myself->coins}}</span><span id="currency_name">{{$currency[$myself->bind_to]['name']}}</span></p>
					<p><span class="box-title">货币更改：</span>
						<select  name="bind_to" id="bind_to">
							@foreach($currency as $cid => $cdata)
								@if($cid == $myself->bind_to)
									<option value="{{$cid}}" selected>{{$cdata['name']}}</option>
								@else
									<option value="{{$cid}}">{{$cdata['name']}}</option>
								@endif
							@endforeach
						</select>
						<a class="button is-small" id="exchange">兑换</a>
						<span class="exchange_rate" id="exchange_rate"></span>
					</p>

					<p class="asset_after_exchange">
						<span class="box-title">兑换后资产：</span><span id="asset_after_exchange"></span>	
					</p>

					<p><span class="box-title">股市资产：</span>
						<span id="asset-in-stock">
							@if(empty($shares))
								0{{$currency[$myself->bind_to]['name']}}
							@else
								正在查询..
							@endif							
						</span>
				</div>
			</div>

		<!--投资股票看板-->
			<div class="tile is-6">
				<div class="div-box">
					<p class="title is-4">{{Session::get('username')}}股票投资</p>
					<hr>
					@if(empty($shares))
						<p class="subtitle has-text-center">无股票投资记录</p>
					@else
						@foreach($shares as $sid => $sdata)
							<div class="box-row" id="{{'div_' . $sdata['fullcode']}}">
								<span class="box-title"><a href="{{route('showtradeform', ['thismarket' => $sdata['atStockMarket'], 'code' => $sdata['code']])}}"><abbr title="{{$sdata['name']}}">{{str_limit($sdata['name'], $limit = 10, $end = '...')}}</abbr></a></span>
								<span id="{{'buying_price_' . $sdata['fullcode']}}">{{$sdata['buying_price']}}</span>
								<span id="{{'amount_' . $sdata['fullcode']}}">{{$sdata['amount']}}</span>
								<span id="{{'current_price_' . $sdata['fullcode']}}">正在查询...</span>
							</div>
						@endforeach
					@endif
				</div>
				<input type="hidden" name="_token" id="_token" value="{{ Session::token() }}">
			</div>
		</div>	
	</div>
</section>

@stop
<div id="confirm-dialog" title="提示" class="inactivo">
<p>
</p>
</div>

<meta name="_token" content="{{ Session::token() }}">

@section('scripts')
	<!-- <script src="{{asset('js/profile.js')}}"></script>  -->
	<script src="{{asset('js/account.js')}}"></script>
@stop