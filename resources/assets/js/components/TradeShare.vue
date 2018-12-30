<template>
	<div class="container" id="transaction-container">
		<p class="title">{{sharename}}交易平台</p>
		<hr>

	 	<div class="tile is-ancestor is-vertical">
           	<div class="tile has-text-right">
           		<div class="clock"></div>        		
        	</div>

        	<div class="tile">
				<section class="tile is-6 is-parent is-vertical">
			 		<section class="tile box">
			 			<!--<div class="tile">-->
			 				<div class="tile is-vertical">
			 					<div class="tile">
			 						<div class="tile">
			 							<div class="share-index">
			 								<span id="current-price" :class="[classData.priceChange]">{{current_data.current_price}}</span>
			 							</div>
			 							<div :class="[classData.bigArraowImg]">
			 							</div>
			 						</div>

			 						<div class="tile">
				 						<div class="share-change">
				 							<span :class="[classData.TextPos, classData.priceChange]" id="share-change-in-price">{{current_data.change_in_price}}</span>
				 							<span :class="[classData.TextPos, classData.priceChange]" id="share-change-in-percent">{{current_data.change_in_percent}}%</span>
				 						</div>
			 						</div>		 					
			 					</div>
			 					<hr>
			 					<div class="tile">
			 						<div class="share-index-ext">	 												
			 								<div><span class="share-detail-title">涨停:</span> <span :class="[classData.TextPos, classData.shareDetailValue]" id="max-price">{{current_data.max_price}}</span></div>
			 							    <div><span class="share-detail-title">跌停:</span> <span :class="[classData.TextPos, classData.shareDetailValue]" id="min-price">{{current_data.min_price}}</span></div>		 							
			 						</div>
			 					</div>
			 					
			 				</div>

			 			<!--</div>-->
			 			<!--<div class="tile">-->
			 				<div class="tile">		 					
									<table class="stock-price-detail">
										<tr>
											<td>
												<span class="share-detail-title">今开:</span> <span :class="[classData.openingpriceChange]" id="opening-price">{{current_data.opening_price}}</span>
											</td>

											<td>
												<span class="share-detail-title">成交量:</span> <span :class="[classData.shareDetailValue]" id="trading-volume">{{current_data.trade_volume}}</span> <span class="share-detail-value">{{current_data.unit_in_volume}}</span><span class="share-detail-value">{{current_data.unit_in_word}}</span>
											</td>
										</tr>

										<tr>
											<td>
												<span class="share-detail-title">竞买:</span> <span :class="[classData.bidpriceChange]" id="bid-price">{{current_data.bid_price}}</span>
											</td>

											<td>
												<span class="share-detail-title">竞卖:</span> <span :class="[classData.askedpriceChange]" id="asked-price">{{current_data.asked_price}}</span>
											</td>
										</tr>

										<tr>
											<td>
												<span class="share-detail-title">最高:</span> <span :class="[classData.highestpriceChange]" id="highest-price">{{current_data.highest_price}}</span>
											</td>

											<td>
												<span class="share-detail-title">成交额:</span> <span class="share-detail-value" id="trading-amount">{{current_data.trade_amount}}</span> <span class="share-detail-value">{{current_data.unit_in_amount}}</span>
											</td>
										</tr>

										<tr>
											<td>
												<span class="share-detail-title">最低:</span> <span :class="[classData.lowestpriceChange]" id="lowest-price">{{current_data.lowest_price}}</span>
											</td>

											<td>
												<span class="share-detail-title">昨收:</span> <span class="closing-price-value" id="closing-price">{{current_data.closing_price}}</span>
											</td>
										</tr>
									 									 							
									</table>
											 					
			 				</div>
			 			<!--</div>-->
			 		</section>
			 		
			 		<template v-if="hideChart == false">
			 		<section class="tile is-child box">
			 			<!--<section class="tile box">			 						 			-->

			 				 <nav v-if="hasBarChart" class="breadcrumb is-centered" aria-label="大盘指数" id="chart-nav">
			 				 	<ul>
			 				 		<nav-li :thismarket="thismarket" :activeID="activeID" :hasBarChart="hasBarChart" thisname="分时线" thisid="min" @changeChart="changeChart"></nav-li>
			 				 		<nav-li :thismarket="thismarket" :activeID="activeID" :hasBarChart="hasBarChart" thisname="日K线" thisid="daily" @changeChart="changeChart"></nav-li>
			 				 		<nav-li :thismarket="thismarket" :activeID="activeID" :hasBarChart="hasBarChart" thisname="周K线" thisid="weekly" @changeChart="changeChart"></nav-li>
			 				 		<nav-li :thismarket="thismarket" :activeID="activeID" :hasBarChart="hasBarChart" thisname="月K线" thisid="monthly" @changeChart="changeChart"></nav-li>
			 				 		<!--
			 				 		<li :class="[hide, isActive]"><a :aria-current="page" id="min-chart">分时线</a></li>
			 				 		<li :class="[hide, isActive]"><a :aria-current="page" id="dayK-chart">日K线</a></li>
			 				 		<li :class="[hide, isActive]"><a :aria-current="page" id="weekK-chart">周K线</a></li>
			 				 		<li :class="[hide, isActive]"><a :aria-current="page" id="monthK-chart">月K线</a></li>
			 				 		-->
			 				 	</ul>
			 				 </nav>			 			
		                    <figure class="image is-545*300" id="chart">
		                       	<img :src="chart_img">	                        
		                    </figure>			 			
				 			         			
			 			<!--</section>-->
			 		</section>
			 		</template>
			 	</section>
			 	

			 	<div class="tile is-6 is-parent is-vertical">
			 		<section class="tile is-child box">
			 			<table class="stock-trade-detail">
			 				<tr>
			 					<th></th>
			 					<th>买方</th>
			 					<th></th>
			 					<th></th>
			 					<th>卖方</th>
			 					<th></th>
			 				</tr>
			 				<tr>
			 				</tr>
			 				<tr>
			 					<td>
			 						<span class="share-detail-title">买1:</span> 
			 					</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.buyingListPrice]" id="buyinglist-1-price">{{current_data.buying_list_price1}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="buyinglist-1-volume">{{current_data.buying_list_volume1}}</span>
								</td>

								<td>
									<span class="share-detail-title">卖1:</span> 
								</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.sellingListPrice]" id="sellinglist-1-price">{{current_data.selling_list_price1}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="sellinglist-1-volume">{{current_data.selling_list_volume1}}</span>
								</td>

			 				</tr>

			 				<tr>
			 					<td>
			 						<span class="share-detail-title">买2:</span> 
			 					</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.buyingListPrice]" id="buyinglist-2-price">{{current_data.buying_list_price2}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="buyinglist-2-volume">{{current_data.buying_list_volume2}}</span>
								</td>

								<td>
									<span class="share-detail-title">卖2:</span> 
								</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.sellingListPrice]" id="sellinglist-2-price">{{current_data.selling_list_price2}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="sellinglist-2-volume">{{current_data.selling_list_volume2}}</span>
								</td>

			 				</tr>

			 				<tr>
			 					<td>
			 						<span class="share-detail-title">买3:</span> 
			 					</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.buyingListPrice]" id="buyinglist-3-price">{{current_data.buying_list_price3}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="buyinglist-3-volume">{{current_data.buying_list_volume3}}</span>
								</td>

								<td>
									<span class="share-detail-title">卖3:</span> 
								</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.sellingListPrice]" id="sellinglist-3-price">{{current_data.selling_list_price3}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="sellinglist-3-volume">{{current_data.selling_list_volume3}}</span>
								</td>

			 				</tr>

			 				<tr>
			 					<td>
			 						<span class="share-detail-title">买4:</span> 
			 					</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.buyingListPrice]" id="buyinglist-4-price">{{current_data.buying_list_price4}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="buyinglist-4-volume">{{current_data.buying_list_volume4}}</span>
								</td>

								<td>
									<span class="share-detail-title">卖4:</span> 
								</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.sellingListPrice]" id="sellinglist-4-price">{{current_data.selling_list_price4}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="sellinglist-4-volume">{{current_data.selling_list_volume4}}</span>
								</td>

			 				</tr>

			 				<tr>
			 					<td>
			 						<span class="share-detail-title">买5:</span> 
			 					</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.buyingListPrice]" id="buyinglist-5-price">{{current_data.buying_list_price5}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="buyinglist-5-volume">{{current_data.buying_list_volume5}}</span>
								</td>

								<td>
									<span class="share-detail-title">卖5:</span> 
								</td>

								<td>
									<span :class="[classData.shareDetailValue, classData.sellingListPrice]" id="sellinglist-5-price">{{current_data.selling_list_price5}}</span>
								</td>

								<td>
									<span class="share-detail-value" id="sellinglist-5-volume">{{current_data.selling_list_volume5}}</span>
								</td>

			 				</tr>		 		 						 								 						 				
			 			</table>
			 			
			 		</section>
			 		<section class="tile is-child box">

			 			<form id="trading-form">
				 			<div class="field is-grouped">
				 				<div class="control">
				 				  <a class="button is-danger" :disabled="buying_btn_disabled" @click="quick_buy">
				 				  	  <span>快速买入</span>
					                  <span class="icon is-small is-left">
					                    <i class="fa fa-money"></i> 
					                  </span> 
				                  </a>		 				  
				 				</div>
				 				<div class="control">
				 					<div class="field has-addons"> 

						 				<div class="control">
						 				  <a class="button is-danger" :disabled="buying_btn_disabled" @click="buy">
						 				  	  <span>买入</span>
							                  <span class="icon is-small is-left">
							                    <i class="fa fa-money"></i> 
							                  </span> 
						                  </a>		 				  
						 				</div>		 					 
					 					<div class="control has-icons-left">	
					                      <input class="input" name ="buying-amount" placeholder="输入整数..." v-model="buyAmount" required type="number" min="1">
						                  <span class="icon is-small is-left">
						                    <i class="fa fa fa-bolt"></i> 
						                  </span>
						                </div>
				                  </div>               
			              	 	</div>
				 			</div>

				 			<hr>

				 			<div class="field is-grouped">
				 				<div class="control">
				 				  <a class="button is-success" :disabled="selling_btn_disabled" @click="quick_sell">
				 				  	  <span>快速卖出</span>
					                  <span class="icon is-small is-left">
					                    <i class="fa fa-money"></i> 
					                  </span> 
				                  </a>		 				  
				 				</div>
				 				<div class="control">
				 					<div class="field has-addons"> 

						 				<div class="control">
						 				  <a class="button is-success" :disabled="selling_btn_disabled" @click="sell">
						 				  	  <span>卖出</span>
							                  <span class="icon is-small is-left">
							                    <i class="fa fa-money"></i> 
							                  </span> 
						                  </a>		 				  
						 				</div>		 					 
					 					<div class="control has-icons-left">	
					                      <input class="input" name ="selling-amount" placeholder="输入整数..." v-model="sellAmount" required type="number" min="1">
						                  <span class="icon is-small is-left">
						                    <i class="fa fa fa-bolt"></i> 
						                  </span>
						                </div>

				                  </div>               
			              	 	</div>
				 			</div>			 			

				 			<div class="card" id="user-info-area">
				 				<div class="card-content">
									<div class="media">
									      <div class="media-left">
									        <!--<figure class="image">-->
									        	<div class="avatar-medium" :style="{ 'background-image': 'url(' + avator_url + ')' }">
									        	</div>							          
									        <!--</figure>-->
									      </div>
									      <div class="media-content">
									        <p class="title is-4">{{myself.name}}</p>	
							 				<div class="info-element">
							 					<span class="info-title">现金：</span><span class="info-detail">{{myself.coins}}</span> 
							 				</div>
							 				<div class="info-element">
							 					<span class="info-title">货币：</span><span class="info-detail">{{currency[myself.bind_to]['name']}}</span> 
							 				</div>

							 				<div class="info-element">
							 					<span class="info-title">拥股数：</span><span class="info-detail">{{shareCount}}</span> 
							 				</div>
							 				
							 				<hr>
							 				<div class="info-element">
							 					<span class="info-title">拥股量：</span><span class="info-detail">{{shareAmount}}</span> 
							 				</div>
							 				<div class="info-element">
							 					<span class="info-title">买入价：</span><span class="info-detail">{{buyingPrice}}</span> 
							 				</div>
							 				<div class="info-element">
							 					<span class="info-title">卖出价：</span><span class="info-detail">{{sellingPrice}}</span> 
							 				</div>							 											 											        						        
									      </div>
									</div>
									<!--
									<div class="content">		 				
						 				<div class="info-element">
						 					<span class="info-title">现金：</span><span class="info-detail">{{myself.coins}}</span> 
						 				</div>
						 				<div class="info-element">
						 					<span class="info-title">货币：</span><span class="info-detail">{{currency[myself.bind_to]['name']}}</span> 
						 				</div>
					 				</div>
					 				-->
					 			</div>		 				
				 			</div>
			 			</form>
			 		</section>
			 	</div>
		 	</div>	
	 	</div>
	 	

		<div id="confirm-dialog" :title="msg_title" class="inactivo">
			<p>{{post_trade_msg}}</p>
		</div>	 	

	</div>




</template>

<script>

//$('#trade-form').parsley();
import Countdown from 'vuejs-countdown'

var outside = this;
var bus = new Vue();

Vue.component('nav-li', {
	props: ['thismarket', 'activeID', 'hasBarChart', 'thisname', 'thisid'],
	data(){
		return{
			hide: '', 
			isActive: '',
			page: ''
		};
	},
	template: '<li :class="[hide, isActive]"><a :aria-current="page" :id="thisid" v-on:click="getChartType">{{thisname}}</a></li>',

	created(){
		var vm = this;
		if(this.activeID == this.thisid)
			this.isActive = 'is-active';

		if(!this.hasBarChart && this.thisid != 'min')
			this.hide = 'hide';
		else{
			this.page = 'page';
		}


		bus.$on('cancelActive', function(id){
			if (id == vm.thisid){
				vm.isActive = '';
			}
		})
	},

	methods:{
		getChartType: function(){
			this.isActive = 'is-active';
			bus.$emit('cancelActive', this.activeID);
			this.$emit('changeChart', this.thisid);
		}
	}
});

export default{

	props: [
		'predata'
	],	

	data(){
		return {
			interval_A: '',
			interval_B: '',
			time_delay: 5000, //changable
			thismarket: '',
			clock: '',
			code: '',
			config: {},
			market: {},
			index: {},
			currency: {},
			fullcode_prefix: {},
			show_share_chart: {},
			barChart: {},
			share_chart_platform: {},
			share_chart_url_head: {},
			trading_with_delay: {},
			this_trading_delay: 0,

			myself:{},
			//title: '',

			csrf_token: '',

			avator_url:"http://bulma.io/images/placeholders/96x96.png",

			sharename: '',
			shareid: '',
			chart_img: '',
			activeID: '',
			hideChart: false,
			hasBarChart: false,
			open_markets: {},
			isOpened: false,
			hasShare: false,
			thisshare: {},

			buying_btn_disabled: true,
			selling_btn_disabled: true,

			buyAmount: 0,
			sellAmount: 0,

			shareAmount: 0,
			buyingPrice: 0,
			sellingPrice: 0,
			shareCount: 0,
			shareCountdown: 0,

			myshares: [],

			left_days: 0,
			left_hours: 0,
			left_minutes: 0,
			left_seconds: 0,

			current_data:{
				opening_price: '',
				closing_price: '',
				current_price: '',
				highest_price: '',
				lowest_price: '',

				change_in_price: '',
				change_in_percent: '',
				max_price: '',
				min_price: '',

				bid_price: '',
				asked_price: '',
				trade_volume: '',
				volume_unit: 1,
				unit_in_word: '',
				unit_in_volume: '',
				trade_amount: '',
				unit_in_amount: '',

				buying_list_volume1: '',
				buying_list_price1: '',
				buying_list_volume2: '',
				buying_list_price2: '',
				buying_list_volume3: '',
				buying_list_price3: '',
				buying_list_volume4: '',
				buying_list_price4: '',
				buying_list_volume5: '',
				buying_list_price5: '',

				selling_list_volume1: '',
				selling_list_price1: '',
				selling_list_volume2: '',
				selling_list_price2: '',
				selling_list_volume3: '',
				selling_list_price3: '',
				selling_list_volume4: '',
				selling_list_price4: '',
				selling_list_volume5: '',
				selling_list_price5: '',												 															

				update_date: '',
				update_time: ''
			},
			
			classData:{
				priceChange: '',
				openingpriceChange: '',
				bidpriceChange: '',
				askedpriceChange: '',
				highestpriceChange: '',
				lowestpriceChange: '',
				bigArraowImg: '',
				TextPos: 'has-text-center',
				shareDetailValue: 'share-detail-value',
				buyingListPrice: '',
				sellingListPrice: ''
				//hide: '',
				//isActive: '',
				//page: ''
			},

			post_trade_msg: '',
			msg_title: '',
			old_data: {}			
		};
	},

	created(){
		this.sharename = this.predata.share.name;
		this.shareid = this.predata.share.sid;

		this.config = this.predata.config;
		//console.log(this.config);
		this.market = this.predata.market;
		this.index = this.predata.index;
		this.currency = this.predata.currency;
		this.fullcode_prefix = this.predata.fullcode_prefix;
		this.thismarket = this.predata.thismarket;
		this.code = this.predata.code;	
		if(typeof this.predata['unit_in_word'][this.thismarket] !== undefined && this.predata['unit_in_word'][this.thismarket]){
			this.current_data.unit_in_word = this.predata['unit_in_word'][this.thismarket];
			this.current_data.volume_unit = parseInt(this.predata['volume_unit'][this.thismarket]);
		}		
		
		this.myself = this.predata.myself;
		this.show_share_chart = this.predata.show_share_chart;
		this.barChart = this.predata.has_bar_chart;
		this.share_chart_platform = this.predata.share_chart_platform;
		this.share_chart_url_head = this.predata.share_chart_url_head;
		if(this.show_share_chart.indexOf(this.thismarket) < 0)
			this.hideChart = true;
		else{
			 if(this.barChart.indexOf(this.thismarket) >= 0){
				this.hasBarChart = true;
			}

			if(this.thismarket == 'us' || this.thismarket == 'nas' || this.thismarket == 'ny'){
				this.activeID = 'min_idx_py';
			}
			
			else{	
				this.activeID = 'min';
			}

			this.chart_img = this.chartImgGet(this.activeID);
			
			console.log(this.chart_img);

		}

		console.log('has bar chart: ' + this.hasBarChart);

		this.open_markets = this.predata.open_markets;
		this.trading_with_delay = this.predata.trading_with_delay;
		if(typeof this.trading_with_delay[this.thismarket] !== undefined && parseInt(this.trading_with_delay[this.thismarket]) > 0){
			this.this_trading_delay = parseInt(this.trading_with_delay[this.thismarket]);			
		}

		this.avator_url = this.predata.avator_url;
		//console.log('current chart url: ' + this.chart_img);
		//this.isOpened();
		if(typeof this.predata.thisshare !== undefined && this.predata.thisshare){
			this.hasShare = true;
			this.thisshare = this.predata.thisshare;
			//console.log(this.thisshare);
			this.shareAmount = this.thisshare.amount;
			this.buyingPrice = this.thisshare.buying_price;
			this.sellingPrice = this.thisshare.selling_price;

			if(parseInt(this.shareAmount) > 0 && this.this_trading_delay > 0){
				this.shareCountdown = parseInt(this.predata.countdown);
			}
		}
		
		this.shareCount = this.predata.myshares_count;
		if(parseInt(this.shareCount) > 0){
			this.myshares = this.predata.myshares;
			//console.log('myshares: ');
			//console.log(this.myshares);
		}

		/*
		//if(this.open_markets.indexOf(this.thismarket) > 0 && this.ifRightCurrency()){
		  if(this.ifRightCurrency()){
			//console.log('disable button!');
			//if(parseFloat(this.myself.coins) >= parseFloat(this.current_data.asked_price))
			//this.buying_btn_disabled = true;

			if(this.ifHasShare())
				this.selling_btn_disabled = false;			
		}
		*/
		//this.csrf_token = this.predata.csrf_token;
		this.csrf_token = $('meta[name="_token"]').attr('content');


		this.updateshare();
		this.interval_A = setInterval(this.updateshare, this.time_delay);
		//this.interval_B = setInterval(this.processBtn, 2000);			

	},

	mounted(){
		if (this.shareCountdown > 0){
			this.buying_btn_disabled = true;		
			this.startcountdown();
		}
	},

	methods: {
		updateshare: function(){
			var vm = this;
			if(this.thismarket != 'nas' && this.thismarket != 'ny')
				var fullcode = this.fullcode_prefix[this.thismarket] + this.code;
			else{
				var fullcode = this.fullcode_prefix[this.thismarket] + this.code.replace(/\./g, "$$");
			}
			var url = this.market[this.thismarket]['query_url_head'] + fullcode;
			var comp = {};

			var ajax_req = $.ajax({
				type: "get",
				url: url,
				cache:"false",
				dataType: "script"
			});

			ajax_req.done(function(){
				$.globalEval("var respond" + " = " + "hq_str_" + fullcode);
				
				var data = respond.split(',');
				var postdata = preProcessShareFromQuery(vm.thismarket, data);

				vm.old_data = $.extend({}, vm.current_data);
				
				vm.current_data.opening_price = postdata.opening_price;
				vm.current_data.closing_price = postdata.closing_price;
				vm.current_data.current_price = postdata.current_price;
				vm.current_data.highest_price = postdata.highest_price;
				vm.current_data.lowest_price = postdata.lowest_price;
				vm.current_data.bid_price = postdata.bid_price;
				vm.current_data.asked_price = postdata.asked_price;

				if(vm.thismarket == 'sh' || vm.thismarket == 'sz'){
					vm.current_data.max_price = round(vm.current_data.opening_price * 1.1, 2);
					vm.current_data.min_price = round(vm.current_data.opening_price * 0.9, 2);
				}

				vm.current_data.change_in_price = (vm.current_data.current_price - vm.current_data.closing_price).toFixed(2);
				vm.current_data.change_in_percent = (vm.current_data.change_in_price / vm.current_data.closing_price * 100).toFixed(2);

				if(postdata.trade_volume){
					comp = vm.getShortNum(postdata.trade_volume / vm.current_data.volume_unit, 2);				
					vm.current_data.trade_volume = comp['val'];
					vm.current_data.unit_in_volume = comp['unit'];
				}

				if(postdata.trade_amount){
					comp = vm.getShortNum(postdata.trade_amount, 2);
					vm.current_data.trade_amount = comp['val'];
					vm.current_data.unit_in_amount = comp['unit'];
				}

				vm.current_data.buying_list_volume1 = postdata.buying_list_volume1;
				vm.current_data.buying_list_price1 = postdata.buying_list_price1;

				vm.current_data.selling_list_volume1 = postdata.selling_list_volume1;
				vm.current_data.selling_list_price1 = postdata.selling_list_price1;

				if(vm.thismarket == 'sh' || vm.thismarket == 'sz'){
					vm.current_data.buying_list_volume2 = postdata.buying_list_volume2;
					vm.current_data.buying_list_price2 = postdata.buying_list_price2;							
					vm.current_data.buying_list_volume3 = postdata.buying_list_volume3
					vm.current_data.buying_list_price3 = postdata.buying_list_price3;
					vm.current_data.buying_list_volume4 = postdata.buying_list_volume4;
					vm.current_data.buying_list_price4 = postdata.buying_list_price4;
					vm.current_data.buying_list_volume5 = postdata.buying_list_volume5;
					vm.current_data.buying_list_price5 = postdata.buying_list_price5;

					vm.current_data.selling_list_volume2 = postdata.selling_list_volume2;
					vm.current_data.selling_list_price2 = postdata.selling_list_price2;
					vm.current_data.selling_list_volume3 = postdata.selling_list_volume3;
					vm.current_data.selling_list_price3 = postdata.selling_list_price3;
					vm.current_data.selling_list_volume4 = postdata.selling_list_volume4;
					vm.current_data.selling_list_price4 = postdata.selling_list_price4;
					vm.current_data.selling_list_volume5 = postdata.selling_list_volume5;
					vm.current_data.selling_list_price5 = postdata.selling_list_price5;	

				}


				vm.current_data.update_date = postdata.update_date;
				vm.current_data.update_time = postdata.update_time;	

				/*
				vm.current_data.opening_price = parseFloat(data[1]);
				vm.current_data.closing_price = parseFloat(data[2]);
				vm.current_data.current_price = parseFloat(data[3]);
				vm.current_data.highest_price = parseFloat(data[4]);
				vm.current_data.lowest_price = parseFloat(data[5]);
				vm.current_data.bid_price = parseFloat(data[6]); 
				vm.current_data.asked_price = parseFloat(data[7]);

				vm.current_data.change_in_price = (vm.current_data.current_price - vm.current_data.closing_price).toFixed(2);
				vm.current_data.change_in_percent = (vm.current_data.change_in_price / 100).toFixed(2);


				comp = vm.getShortNum(parseFloat(data[8]) / vm.current_data.volume_unit, 2);
				vm.current_data.trade_volume = comp['val'];
				vm.current_data.unit_in_volume = comp['unit'];

				comp = vm.getShortNum(parseFloat(data[9]), 2);
				vm.current_data.trade_amount = comp['val'];
				vm.current_data.unit_in_amount = comp['unit'];



				vm.current_data.buying_list_volume1 = parseFloat(data[10]);
				vm.current_data.buying_list_price1 = parseFloat(data[11]);
				vm.current_data.buying_list_volume2 = parseFloat(data[12]);
				vm.current_data.buying_list_price2 = parseFloat(data[13]);							
				vm.current_data.buying_list_volume3 = parseFloat(data[14]);
				vm.current_data.buying_list_price3 = parseFloat(data[15]);
				vm.current_data.buying_list_volume4 = parseFloat(data[16]);
				vm.current_data.buying_list_price4 = parseFloat(data[17]);
				vm.current_data.buying_list_volume5 = parseFloat(data[18]);
				vm.current_data.buying_list_price5 = parseFloat(data[19]);

				vm.current_data.selling_list_volume1 = parseFloat(data[20]);
				vm.current_data.selling_list_price1 = parseFloat(data[21]);
				vm.current_data.selling_list_volume2 = parseFloat(data[22]);
				vm.current_data.selling_list_price2 = parseFloat(data[23]);
				vm.current_data.selling_list_volume3 = parseFloat(data[24]);
				vm.current_data.selling_list_price3 = parseFloat(data[25]);
				vm.current_data.selling_list_volume4 = parseFloat(data[26]);
				vm.current_data.selling_list_price4 = parseFloat(data[27]);
				vm.current_data.selling_list_volume5 = parseFloat(data[28]);
				vm.current_data.selling_list_price5 = parseFloat(data[29]);																

				vm.current_data.update_date = parseFloat(data[30]);
				vm.current_data.update_time = parseFloat(data[31]);	
				*/
				//check if has enough money
				//if(vm.open_markets.indexOf(this.thismarket) > 0 && vm.ifRightCurrency() && parseFloat(vm.myself.coins) >= parseFloat(vm.current_data.asked_price)){
				/*	
				if(vm.ifRightCurrency() && parseFloat(vm.myself.coins) >= parseFloat(vm.current_data.asked_price)){	
					vm.buying_btn_disabled = false;
				}
				else{
					vm.buying_btn_disabled = true;				
				}
				*/
				vm.processBtn();
				vm.processChange(vm.old_data, vm.current_data);	
			});
			
			ajax_req.fail(function(xhr, status, error){
				console.log(error.message);
				/**
				$("#error-dialog").html(xhr.responseText);	
				**/
			}); 			
			
			return ajax_req;					
	
		},

		processBtn: function(){
			this.buying_btn_disabled = true;
			this.selling_btn_disabled = true;
			//var ifOpened = this.ifOpened();
			this.ifOpened();
			var ifOpened = this.isOpened;
			var ifRightCurrency = this.ifRightCurrency();
			//var ifHasShare = this.ifHasShare();
			var ifMatchDelay = this.ifMatchDelay();

			var ifHasEnoughMoney = this.ifHasEnoughMoney();
			var ifExceedMaxNo = this.ifExceedMaxNo();
			var ifHasShare = this.ifHasShare();

			console.log('ifRightCurrency->' + ifRightCurrency + ' ifOpened->' + ifOpened + ' ifMatchDelay->' + ifMatchDelay + ' ifHasEnoughMoney->' + ifHasEnoughMoney + ' ifExceedMaxNo->' + ifExceedMaxNo + ' ifHasShare->' + ifHasShare);
			//if(ifOpened && ifRightCurrency && ifMatchDelay){
			  if(ifRightCurrency){
			  	if(ifHasEnoughMoney && !ifExceedMaxNo){		
					this.buying_btn_disabled = false;
				}
				else{

					this.buying_btn_disabled = true;
				}

				if(ifHasShare && ifMatchDelay)
					this.selling_btn_disabled = false;
				else
					this.selling_btn_disabled = true;
			}
			else{
				this.buying_btn_disabled = true;
				this.selling_btn_disabled = true;
			}
		},

		ifOpened: function(){
			var vm = this;
			//var url = 'http://stock.yucheung.com/getopenmarkets';
			var url = '/getopenmarkets';
			//var ret;
			axios.get(url)
				.then(function(res){
					if (typeof res.data !== undefined && res.data){								
						var data = res.data;
												
						if(data.indexOf(vm.thismarket) < 0)
							//ret = false;
							//vm.btn_disabled = true;
							vm.isOpened = false;
						else
							//ret = true;
							//vm.btn_disabled = false;
							vm.isOpened = true;
					}

				})
				.catch(function(err){
					console.log('errors: ' + err);

			});			
			//return ret;	
		},

		ifRightCurrency: function(){
			var mycurrency = this.myself.bind_to;
			var marketcurrency = this.market[this.thismarket]['allowed_currency'];
			//console.log(mycurrency + ' | ' + marketcurrency);
			if(mycurrency == marketcurrency)
				return true;
			else
				return false;
		},

		ifHasShare: function(){
			return parseInt(this.shareAmount) > 0;			
		},

		ifHasEnoughMoney: function(){
			return parseFloat(this.myself.coins) >= parseFloat(this.current_data.asked_price);
		},

		ifExceedMaxNo: function(){
			//console.log('shareCount->' + this.shareCount + ' max_shares_no->' + this.config.max_shares_no);
			//console.log('myshares: ');
			//console.log(this.myshares);
			return (parseInt(this.shareCount) >= parseInt(this.config.max_shares_no) && this.myshares.indexOf(this.shareid) < 0);
		},

		ifMatchDelay: function(){
			if(parseInt(this.this_trading_delay) <= 0)
				return true;
			else{
				if(this.shareCountdown <= 0)
					return true;
				else
					return false;
			}
		},

		/*
		ifDisableSell: function(){
			if (this.ifOpened() && this.ifRightCurrency() && this.ifHasShare())
				return false;
			else
				return true;
		}
		*/

		getShortNum: function(num, digits){
			var units = ['k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'],
			        decimal;
			var ret = {};        
		    for(var i=units.length-1; i>=0; i--) {
		        decimal = Math.pow(1000, i+1);

		        if(num <= -decimal || num >= decimal) {
		        	ret['val'] = +(num / decimal).toFixed(digits);
		        	ret['unit'] = units[i];
		            //return +(num / decimal).toFixed(digits) + units[i];
		            return ret;
		        }
		    }
		    ret['val'] = num;
		    ret['unit'] = '';
		    return ret;			
		},

		processChange: function(old, current){
			if(!old.current_price)
				return

			var bigUpArrow = 'big-us-up';
			var bigDownArrow = 'big-us-down';
			var valueUp = 'us-value-up';
			var valueDown = 'us-value-down';
			var buyingprice = 'us-buyinglist-price';
			var sellingprice = 'us-sellinglist-price';

			if(this.thismarket == 'sh' || this.thismarket == 'sz'){
				bigUpArrow = 'big-ch-up';
				bigDownArrow = 'big-ch-down';
				valueUp = 'value-up';
				valueDown = 'value-down';
				buyingprice = 'buyinglist-price';
				sellingprice = 'sellinglist-price';
			}

			if(current.current_price > current.closing_price){
				this.classData.priceChange = valueUp;
			}

			else if (current.current_price < current.closing_price){
				this.classData.priceChange = valueDown;
			}

			else{
				this.classData.priceChange = '';
			}

			if(current.opening_price > current.closing_price){
				this.classData.openingpriceChange = valueUp;
			}

			else if (current.opening_price < current.closing_price){
				this.classData.openingpriceChange = valueDown;
			}

			else{
				this.classData.openingpriceChange = '';
			}

			if(current.bid_price > current.closing_price){
				this.classData.bidpriceChange = valueUp;
			}

			else if (current.bid_price < current.closing_price){
				this.classData.bidpriceChange = valueDown;
			}

			else{
				this.classData.bidpriceChange = '';
			}			

			if(current.asked_price > current.closing_price){
				this.classData.askedpriceChange = valueUp;
			}

			else if (current.asked_price < current.closing_price){
				this.classData.askedpriceChange = valueDown;
			}

			else{
				this.classData.askedpriceChange = '';
			}


			if(current.highest_price > current.closing_price){
				this.classData.highestpriceChange = valueUp;
			}

			else if (current.highest_price < current.closing_price){
				this.classData.highestpriceChange = valueDown;
			}

			else{
				this.classData.highestpriceChange = '';
			}


			if(current.lowest_price > current.closing_price){
				this.classData.lowestpriceChange = valueUp;
			}

			else if (current.lowest_price < current.closing_price){
				this.classData.lowestpriceChange = valueDown;
			}

			else{
				this.classData.lowestpriceChange = '';
			}


			if(current.current_price > old.current_price)
				this.classData.bigArraowImg = bigUpArrow;
			else if (current.current_price < old.current_price)
				this.classData.bigArraowImg = bigDownArrow;
			else
				this.classData.bigArraowImg = '';

			this.classData.buyingListPrice = buyingprice;
			this.classData.sellingListPrice = sellingprice;
		
		},

		changeChart: function(newActiveId){
			this.activeID = newActiveId;
			this.chart_img = this.chartImgGet(newActiveId);
			//console.log(this.chart_img);
		},

		chartImgGet: function(type){		
			var code = this.code;

			//用sina的chart
			if(this.share_chart_platform.sina.indexOf(this.thismarket) >= 0){
				var mid_part = '';
				if(this.thismarket == 'sh' || this.thismarket == 'sz'){
					mid_part = 'n/';
					code  = this.fullcode_prefix[this.thismarket] + code;
				}

				if(this.thismarket == 'us' || this.thismarket == 'nas' || this.thismarket == 'ny'){
					code = code.toLowerCase();
				}	
				return this.share_chart_url_head[this.thismarket] + type  + '/' + mid_part + code + '.gif?' + (new Date()).getTime();
			}
		},

		quick_buy: function(){			
			if(parseInt(this.current_data.selling_list_volume1) == 0 || parseInt(this.myself.coins) == 0){
				//this.buying_btn_disabled = true;
				var title = '错误';
				var msg = '发生错误：竞卖量为0或者现金为0，无法购买';
				this.showconfirm(title,msg);				
				return;
			}

			var max_allowed_amount = Math.floor(parseFloat(this.myself.coins) / parseFloat(this.current_data.asked_price));
			var actual_amount, actual_spend;
			actual_amount = actual_spend = 0;
		
			//console.log(parseFloat(this.myself.coins));
			//console.log(this.current_data.asked_price);
			//console.log(max_allowed_amount);

			if(max_allowed_amount >= parseInt(this.current_data.selling_list_volume1))
				actual_amount = parseInt(this.current_data.selling_list_volume1);
			else
				actual_amount = max_allowed_amount;

			actual_spend = (parseFloat(this.current_data.asked_price) * actual_amount).toFixed(2);

			console.log(this.shareid + ' ' + this.myself.iid + ' ' + actual_amount + ' ' + this.current_data.asked_price);
			//return;

			this.ajax_buy(this.shareid, this.myself.iid, actual_amount, this.current_data.asked_price);
			
		},

		buy: function(){
			if(parseInt(this.current_data.selling_list_volume1) == 0 || parseInt(this.myself.coins) == 0){
				//this.buying_btn_disabled = true;
				var title = '错误';
				var msg = '发生错误：竞卖量为0或者现金为0，无法购买';
				this.showconfirm(title,msg);						
				return;
			}

			var actual_amount, actual_spend;
			actual_amount = actual_spend = 0;
			if(this.buyAmount && this.buyAmount >= parseInt(this.current_data.selling_list_volume1))
				actual_amount = parseInt(this.current_data.selling_list_volume1);
			else if (this.buyAmount && this.buyAmount < parseInt(this.current_data.selling_list_volume1))
				actual_amount = this.buyAmount;
			else{
				//this.buying_btn_disabled = true;
				var title = '错误';
				var msg = '发生错误：请输入大于0的购买量';
				this.showconfirm(title,msg);					
				return;
			}

			actual_spend = (parseFloat(this.current_data.asked_price) * actual_amount).toFixed(2);
			console.log(this.shareid + ' ' + this.myself.iid + ' ' + actual_amount + ' ' + this.current_data.asked_price);
			this.ajax_buy(this.shareid, this.myself.iid, actual_amount, this.current_data.asked_price);
		},


		quick_sell: function(){
			if(parseInt(this.current_data.buying_list_volume1) == 0 || parseInt(this.thisshare.amount) == 0){
				//this.sellinging_btn_disabled = true;
				var title = '错误';
				var msg = '发生错误：竞买量为0或者拥有股量为0，无法卖出';
				this.showconfirm(title,msg);						
				return;
			}

			var max_allowed_amount = parseInt(this.thisshare.amount);
			var actual_amount, actual_gain;
			actual_amount = actual_gain = 0;
			if(max_allowed_amount >= parseInt(this.current_data.buying_list_volume1))
				actual_amount = parseInt(this.current_data.buying_list_volume1);
			else(max_allowed_amount < parseInt(this.current_data.buying_list_volume1))
				actual_amount = max_allowed_amount;

			actual_gain = (parseFloat(this.current_data.bid_price) * actual_amount).toFixed(2);


			this.ajax_sell(this.shareid, this.myself.iid, actual_amount, this.current_data.bid_price);
		},

		sell: function(){
			if(parseInt(this.current_data.buying_list_volume1) == 0 || parseInt(this.thisshare.amount) == 0){
				//this.sellinging_btn_disabled = true;
				var title = '错误';
				var msg = '发生错误：竞买量为0或者拥有股量为0，无法卖出';
				this.showconfirm(title,msg);						
				return;
			}

			var actual_amount, actual_gain;
			actual_amount = actual_gain = 0;

			if(this.sellAmount && this.sellAmount >= parseInt(this.current_data.buying_list_volume1))
				actual_amount = parseInt(this.current_data.buying_list_volume1);
			else if (this.sellAmount && this.sellAmount < parseInt(this.current_data.buying_list_volume1))
				actual_amount = this.sellAmount;
			else{
				//this.buying_btn_disabled = true;
				var title = '错误';
				var msg = '发生错误：请输入大于0的卖出量';
				this.showconfirm(title,msg);					
				return;
			}			
			actual_gain = (parseFloat(this.current_data.bid_price) * actual_amount).toFixed(2);
			this.ajax_sell(this.shareid, this.myself.iid, actual_amount, this.current_data.bid_price);			
		},

		ajax_buy: function(sid, iid, amount, price){
			var vm = this;
			//var baseURL = 'http://stock.yucheung.com/';
			var baseURL = '/';

			console.log('sid: ' + sid + '  iid: ' + iid + '  amount: ' + amount + '  price: ' + price);
			//return;
			console.log('X-CSRF-TOKEN: ' + this.csrf_token);
			var buy_axios = axios.create({
				baseURL: baseURL,
				headers: {
                	'X-CSRF-TOKEN': this.csrf_token
            	},
            	responseType: 'json'
			});
			
			buy_axios.post('tradeshare/buy', {
            		sid: sid,
            		iid: iid,
            		thismarket: this.thismarket,
            		amount: amount,
            		price: price   				
			}).then(function(res){
					if (typeof res.data !== undefined && res.data){								
						//var data = res.data;
						var data = res.data;
						console.log(data);
						//return;
						var msg, title;
						msg = title = '';
						//return;
						if(!data.errors){							
							vm.myself.coins = data.data.left;
							var spend = data.data.spend;
							var amount = data.data.amount;
							var price = data.data.price;
							var totalamount = data.data.totalamount;
							var sharecount = parseInt(data.data.sharecount);
							title = '购买';
							msg += '购买股票' + vm.sharename +  '成功, 购买价格为' + vm.currency[vm.myself.bind_to]['name'] + ' ' + price + '，共购买' + amount + '股，累计' + totalamount + '股，一共花费' + vm.currency[vm.myself.bind_to]['name'] + ' ' +  spend + '!';

							//return;
							vm.shareAmount = totalamount;
							vm.buyingPrice = price;
							vm.shareCount = parseInt(vm.shareCount) + sharecount;
							vm.processBtn();
							/*
							if(parseInt(vm.shareAmount) > 0){
								vm.hasShare = true;
								vm.selling_btn_disabled = false;
							}

							if(parseFloat(vm.myself.coins) < parseFloat(vm.current_data.asked_price)){
								vm.buying_btn_disabled = true;
							}
							*/
							vm.buyAmount = 0;
							vm.sellAmount= 0;							
							
						}
						else{
							title = '错误';
							msg += '发生错误：' + data.errors;
						}
						//sellAmount = buyAmount = 0;
						if(parseInt(vm.shareAmount) > 0 && vm.this_trading_delay > 0){
							vm.shareCountdown = parseInt(vm.this_trading_delay) * 3600;
							vm.startcountdown();
						}
						vm.processBtn();
						vm.showconfirm(title,msg);
					}

				})
				.catch(function(err){
					console.log('errors: ' + err);

			});				

		},

		ajax_sell: function(sid, iid, amount, price){
			var vm = this;
			//var baseURL = 'http://stock.yucheung.com/';
			var baseURL = '/';
			//var x_csrf_token = $('meta[name="_token"]').attr('content');
			var sell_axios = axios.create({
				baseURL: baseURL,
				headers: {
                	'X-CSRF-TOKEN': this.csrf_token
            	},
            	responseType: 'json'
			});

			sell_axios.post('tradeshare/sell', {
            		sid: sid,
            		iid: iid,
            		thismarket: this.thismarket,
            		amount: amount,
            		price: price
			}).then(function(res){
					if (typeof res.data !== undefined && res.data){								
						var data = res.data;
						console.log(data);

						var msg, title;
						msg = title = '';

						if(!data.errors){							
							vm.myself.coins = data.data.left;
							var gain = data.data.gain;
							var net_gain = data.data.net_gain;
							var amount = data.data.amount;
							var totalamount = data.data.totalamount;
							var price = data.data.price;
							var sharecount = parseInt(data.data.sharecount);
							var gain_msg = '';

							vm.shareAmount = totalamount;
							vm.sellingPrice = price;
							vm.shareCount = parseInt(vm.shareCount) + sharecount;

							vm.buyAmount = 0;
							vm.sellAmount= 0;	
							/*
							if(parseInt(vm.shareAmount) <= 0){
								vm.hasShare = false;
								vm.selling_btn_disabled = true;
							}
							*/
							vm.processBtn();	

							if(parseFloat(net_gain) < 0)
								gain_msg += '净亏' + vm.currency[vm.myself.bind_to]['name'] + ' ' + Math.abs(parseFloat(net_gain)) + '.';
							else if (parseFloat(net_gain) > 0)
								gain_msg += '净赚' + vm.currency[vm.myself.bind_to]['name'] + ' ' + Math.abs(parseFloat(net_gain)) + '.';

							title = '卖出';
							msg += '卖出股票' + vm.sharename +  '成功, 卖出价格为' + vm.currency[vm.myself.bind_to]['name'] + ' ' + price + '，共卖出' + amount + '股，还剩' + totalamount + '股，本次交易获利' + vm.currency[vm.myself.bind_to]['name'] + ' ' +  gain + '! ' + gain_msg;
							
						}
						else{
							title = '错误';
							msg += '发生错误：' + data.errors;
						}
						//sellAmount = buyAmount = 0;
						vm.showconfirm(title,msg);
					}

				})
				.catch(function(err){
					console.log('errors: ' + err);

			});				
		},

		showconfirm: function(title,msg){
			this.post_trade_msg = msg;
			this.msg_title = title;
			$("#confirm-dialog").addClass('activo');
			$("#confirm-dialog").dialog({
				modal:true,
				width:400,
				position: ['center', 'center'],
				resizable: false,
				dialogClass: "alert",
				buttons: {
		        	"确认": function(){
		        		$(this).dialog("close");
		        		this.post_trade_msg = '';
		        		this.msg_title = '';
		        	}	
				}	
			});				
		},

		startcountdown: function(){
			this.clock = $('.clock').FlipClock(this.shareCountdown, {
					countdown: true,
					language: 'chinese',
					callbacks: {	
						stop: function(){
							this.shareCountdown = 0;
							this.processBtn();
							$('.clock').hide();
						}
					}		
			});			
		}
	}
};	

</script>