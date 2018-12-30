$(function(){
	var msg = '确认要兑换吗？';
	var error_msg = '您必须把股票全部卖出兑现后才可以兑换其他货币！';

	var config, market, index, currency, interval, myinfo, hasShare ;
	var myshares = {};
	var query_heads = {};
	var shares_query = {};
	var platform_shares = {};
	var platform_sid_map = {};

	var s_token = $("#_token").val();

	var old_currency = $("#bind_to").val();
	var asset = $("#coins").text();

	var get_exchange_rate = function(old_c,new_c){
		var url = currency[new_c]['rate_query_url'] + Math.round(new Date().getTime()/1000) + 'list=fx_s' + old_c + new_c;
		//console.log(url);

		var ajax_req = $.ajax({
			type: "get",
			url: url,
			cache:"false",
			dataType: "script"
		});

		ajax_req.done(function(){
			eval("var respond" + " = " + "hq_str_fx_s" + old_c + new_c);
			//console.log(respond);	
			var data = respond.split(',');
			
			$("#exchange_rate").text(data[8]);
		});

		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
			/**
			$("#error-dialog").html(xhr.responseText);	
			**/
		});	
		return ajax_req;
	}

	var update_my_shares_price = function(){
		var asset = 0;
		var old_asset = 0;
		var value_down_class = 'us-value-down';
		var value_up_class = 'us-value-up';	
		$("#asset-in-stock").removeClass('value-down value-up us-value-up us-value-down');		
		$.each(shares_query, function(platform, query){
			var url = query;
			//console.log(url);
			var respond, data;
			var ajax_req = $.ajax({
				type: "get",
				url: url,
				cache:"false",
				dataType: "script"
			});	

			ajax_req.done(function(){
				$.each(platform_shares[platform], function(index, fullcode){
					$("#div_" + fullcode).find('span').removeClass('value-down value-up us-value-up us-value-down');

					var newprice_part = $("#current_price_" + fullcode);
					eval("respond" + " = " + "hq_str_" + fullcode.toLowerCase());
					//console.log(respond);
					data = respond.split(',');
					//console.log(data);
					var sid = platform_sid_map[platform][fullcode];
					//console.log(sid);
					//console.log(myshares);
					var themarket = myshares[sid]['atStockMarket'];

					console.log(themarket);

					var newprice = 0;
					var buyingprice = parseFloat($("#buying_price_" + fullcode).text()).toFixed(2);
					var amount = parseInt($("#amount_" + fullcode).text());

					console.log(buyingprice + ' ' + amount);
					if(themarket == 'sh' || themarket == 'sz'){
						newprice = parseFloat(data[3]).toFixed(2);
						value_down_class = 'value-down';
						value_up_class = 'value-up';
					}
					else if(themarket == 'ny' || themarket == 'nas' || themarket == 'us')
						newprice = parseFloat(data[1]).toFixed(2);
					else if (themarket == 'hk')
						newprice = parseFloat(data[6]).toFixed(2);
					
					if(newprice){
						$("#current_price_" + fullcode).text(newprice);
						if(newprice > buyingprice)
							newprice_part.addClass(value_up_class);
						else if (newprice < buyingprice)
							newprice_part.addClass(value_down_class);

						asset += (newprice * amount);
						old_asset += (buyingprice * amount);
						//console.log('old asset: ' + old_asset);
						//console.log('new asset: ' + asset);						
					}
				});
				if(asset && asset < old_asset)
					$("#asset-in-stock").addClass(value_down_class);
				else if(asset && asset > old_asset)
					$("#asset-in-stock").addClass(value_up_class);				

				$("#asset-in-stock").text(asset);

			});

			ajax_req.fail(function(xhr, status, error){
				console.log(error.message);
				/**
				$("#error-dialog").html(xhr.responseText);	
				**/
			});
								
		});
		//console.log('final old asset: ' + old_asset);
		//console.log('final new asset: ' + asset);

		
	}

	var exchange = function(){
		//var url = 'http://stock.yucheung.com/exchange/' +  $("#bind_to").val();
		var url = config.weburl + '/exchange/' + $("#bind_to").val();
		 
		console.log(url);

		var ajax_req = $.ajax({
			type: "get",
			url: url,
			cache: "false",
			dataType: "json"
		});

		ajax_req.done(function(data){
			if(typeof data['newcoins'] !== undefined && data['newcoins']){
				var currency_name = currency[$("#bind_to").val()]['name'];
				$("#coins").text(data['newcoins']);	

				$("#currency_name").text(currency_name);
			}
		});

		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
			/**
			$("#error-dialog").html(xhr.responseText);	
			**/
		});	
		return ajax_req;		
	}

	var showerror = function(msg){
		$("#confirm-dialog").addClass('activo');
		$("#confirm-dialog p").text(msg);
		$("#confirm-dialog").attr('title', '错误');
		$("#confirm-dialog").dialog({
			modal:true,
			width:400,
			position: ['center', 'center'],
			resizable: false,
			dialogClass: "alert",
			 buttons: {
		        "确认": function() {
		            $(this).dialog("close");
		            $("#confirm-dialog p").text('');
					$("#exchange_rate").text('');
					$("#asset_after_exchange").text('');
					$(".asset_after_exchange").hide();		            
		            $("#exchange").hide();		           
		        }		  
			 }
		});			
	}

	var showconfirm = function(msg){
		$("#confirm-dialog").addClass('activo');
		$("#confirm-dialog p").text(msg);
		$("#confirm-dialog").dialog({
			modal:true,
			width:400,
			position: ['center', 'center'],
			resizable: false,
			dialogClass: "alert",
			 buttons: {
		        "确认": function(){
		        	$(this).dialog("close");
		        	$("#confirm-dialog p").text('');
					$("#exchange_rate").text('');
					$("#asset_after_exchange").text('');
					$(".asset_after_exchange").hide();		            
		            $("#exchange").hide();	
		            exchange();		        	
		        },

		        "取消": function() {
		            $(this).dialog("close");
		            $("#confirm-dialog p").text('');
					$("#exchange_rate").text('');
					$("#asset_after_exchange").text('');
					$(".asset_after_exchange").hide();		            
		            $("#exchange").hide();		           
		        }		  
			 }
		});	
	}


	/*****/

	$("#exchange").hide();

	$.when(get_config(), get_myinfo(), get_queryByPlatform()).done(function(c_data, i_data, q_data){
		config = c_data[0]['config'];
		market = c_data[0]['market'];
		index = c_data[0]['index'];		
		currency = c_data[0]['currency'];
		myinfo = i_data[0]['personal'];
		query_heads = q_data[0];		

		if(typeof i_data[0]['shares'] !== undefined && !$.isEmptyObject(i_data[0]['shares'])){
			hasShare = true;			

			$.each(i_data[0]['shares'], function(sid, share){
				myshares[sid] = share;
				if(!Array.isArray(platform_shares[share.platform]))
					platform_shares[share.platform] = [];
				platform_shares[share.platform].push(share.fullcode);

				if(typeof platform_sid_map[share.platform] !== 'object')
					platform_sid_map[share.platform] = {};
				platform_sid_map[share.platform][share.fullcode] = share.sid;
			});			
		}
		else{
			hasShare = false;
		}
		//var shares_query = {};
		$.each(platform_shares,function(p, f){
			shares_query[p] = query_heads[p] + f.join().toLowerCase(); 
		});

		//console.log(myinfo);
		//console.log(myshares);
		console.log(shares_query);
		if(!$.isEmptyObject(shares_query))
			update_my_shares_price();
	});

	if(!$.isEmptyObject(shares_query))
		interval = setInterval(update_my_shares_price,10000);

	$("#bind_to").on('change', function(){
		var new_currency = $("#bind_to").val();
		$("#exchange_rate").text('');
		$(".asset_after_exchange").hide();
		if(new_currency != old_currency){
			$.when(get_exchange_rate(old_currency, new_currency)).done(function(){
				var exchange_rate = $("#exchange_rate").text();
				if (exchange_rate){
					var asset_after_exchange = round(parseFloat(exchange_rate) * parseInt(asset), 2);
					$("#exchange").show();
					$(".asset_after_exchange").show();
					$("#asset_after_exchange").text(asset_after_exchange);
				}
			});
			
		}
		else
			$("#exchange").hide();
	});

	$("#exchange").on('click', function(){
		if(hasShare)
			showerror(error_msg);
		else	
			showconfirm(msg);
	});
});	