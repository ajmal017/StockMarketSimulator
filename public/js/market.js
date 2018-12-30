var outside = this;

var http_hd = 'http:\/\/image.sinajs.cn\/newchart';
var http_usa = 'http:\/\/image.sinajs.cn\/newchart\/usstock\/';
var list = {'sh':'上证指数', 'sz':'深证指数', 'dow':'道琼指数', 'nas':'纳斯达克指数'};
var url = {'sh':'sh000001.gif', 'sz':'sz399001.gif', 'dow':'min_idx_py\/dji.gif', 'nas':'min_idx_py\/ixic.gif'};

var config; 
var market; 
var index; 
var extra;

var type = {'min':'\/min\/', 'dayK':'\/daily\/', 'weekK':'\/weekly\/', 'monthK':'\/monthly\/'};

var type_des = {'min':'指数', 'dayK':'日K线', 'weekK':'周K线', 'monthK':'月K线'};

var market_id = {'sh': 's_sh000001', 'sz' : 's_sz399001', 'dow': 'gb_dji', 'nas': 'gb_ixic', 'ny': 'gb_dji'};

var img_up = "<img src='../images/ch-up-img.png'>";
var img_down =  "<img src='../images/ch-down-img.png'>";

var us_img_up = "<img src='../images/us-up-img.png'>";
var us_img_down =  "<img src='../images/us-down-img.png'>";

var interval, interval2;

$(function(){

	var get_stock_index = function(){
		var mid = $("#market_id").text();
		if (mid === null)		
			mid = 'sh';
		//console.log(mid);
		var data;
		var thisindex = $("#index_id").text();
		//var url = "http://hq.sinajs.cn/list=" + market_id[mid];
		var url = index[thisindex]['index_query_url'];
		//console.log('url: ' + url);
		var ajax_req = $.ajax({
			type: "get",
			url: url,
			cache:"false",
			dataType: "script"
		});
		
		ajax_req.done(function(){
			data = url.split("=");
			eval("var respond" + " = " + "hq_str_" + data[1]);
			
			//var temp = respond.split('=');
			//var temp2 = temp[1].split('"');
			//alert(temp2[0] + ' | ' + temp2[1] + ' | ' + temp2[3]);

			$("#index-info .value-up,.value-down,.us-value-up,.us-value-down").removeClass('value-up value-down us-value-up us-value-down');
			$("#index-info img").remove();

			var old_index = $("#curr_index").text();
			old_index = Number(parseFloat(old_index)).toFixed(2);
			var old_cip = $("#change_in_points").text();
			old_cip = Number(parseFloat(old_cip)).toFixed(2);
			var old_ciper = $("#change_in_percent").text();
			old_ciper = Number(parseFloat(old_ciper)).toFixed(2);

			var old_vol = parseFloat($("#transaction_volume").text());
			//old_vol = Number(old_vol).toFixed(2);

			var old_amount = parseFloat($("#transaction_amount").text());
			//old_amount = Number(old_amount).toFixed(2);			

			data = respond.split(',');
			
			if (mid == 'sh' || mid == 'sz'){
				data[1] = Number(parseFloat(data[1])).toFixed(2);
				data[2] = Number(parseFloat(data[2])).toFixed(2);
				data[3] = Number(parseFloat(data[3])).toFixed(2);
				data[4] = Number(data[4] / 10000).toFixed(2) + 'M';
				data[5] = Number(data[5] / 10000).toFixed(2) + 'M';

				if (data[1] < old_index)
					$("#curr_index").after(img_down);
				else if(data[1] > old_index)
					$("#curr_index").after(img_up);

				if (data[2] < old_cip)
					$("#change_in_points").after(img_down);
				else if(data[2] > old_cip)
					$("#change_in_points").after(img_up);

				if (data[3] < old_ciper)
					$("#change_in_percent").after(img_down);
				else if(data[3] > old_ciper)
					$("#change_in_percent").after(img_up);

				if (parseFloat(data[4]) < old_vol)
					$("#transaction_volume").after(img_down);
				else if(parseFloat(data[4]) > old_vol)
					$("#transaction_volume").after(img_up);

				if (parseFloat(data[5]) < old_amount)
					$("#transaction_amount").after(img_down);
				else if(parseFloat(data[5]) > old_amount)
					$("#transaction_amount").after(img_up);

				if (data[2] < 0){
					data[2] = Math.abs(data[2]);
					data[3] = Math.abs(data[3]);
					$("#curr_index").addClass('value-down');
					$("#change_in_points").addClass('value-down');
					$("#change_in_percent").addClass('value-down');
					
				}
				else if (data[2] > 0){					
					$("#curr_index").addClass('value-up');
					$("#change_in_points").addClass('value-up');
					$("#change_in_percent").addClass('value-up');
					
				}

				data[3] += '%';

				$("#curr_index").text(data[1]);
				$("#change_in_points").text(data[2]);
				$("#change_in_percent").text(data[3]);
				$("#transaction_volume").text(data[4]);
				$("#transaction_amount").text(data[5]);


			}
			else if (mid == 'ny' || mid == 'nas'){
				data[1] = Number(parseFloat(data[1])).toFixed(2);
				data[4] = Number(parseFloat(data[4])).toFixed(2);
				data[2] = Number(parseFloat(data[2])).toFixed(2);
				data[10] = Number(data[10] / 1000000).toFixed(2) + 'M';
				data[11] = Number(data[11] / 1000000).toFixed(2) + 'M';
				if (data[1] < old_index)
					$("#curr_index").after(us_img_down);
				else if(data[1] > old_index)
					$("#curr_index").after(us_img_up);

				if (data[4] < old_cip)
					$("#change_in_points").after(us_img_down);
				else if(data[4] > old_cip)
					$("#change_in_points").after(us_img_up);

				if (data[2] < old_ciper)
					$("#change_in_percent").after(us_img_down);
				else if(data[2] > old_ciper)
					$("#change_in_percent").after(us_img_up);

				if (parseFloat(data[10]) < old_vol)
					$("#transaction_volume").after(us_img_down);
				else if(parseFloat(data[10]) > old_vol)
					$("#transaction_volume").after(us_img_up);

				if (parseFloat(data[11]) < old_amount)
					$("#transaction_amount").after(us_img_down);
				else if(parseFloat(data[11]) > old_amount)
					$("#transaction_amount").after(us_img_up);

				if (data[2] < 0){
					data[2] = Math.abs(data[2]);
					data[4] = Math.abs(data[4]);
					$("#curr_index").addClass('us-value-down');
					$("#change_in_points").addClass('us-value-down');
					$("#change_in_percent").addClass('us-value-down');					
				}
				else if (data[2] > 0){					
					$("#curr_index").addClass('us-value-up');
					$("#change_in_points").addClass('us-value-up');
					$("#change_in_percent").addClass('us-value-up');
					
				}

				data[2] += '%';

				$("#curr_index").text(data[1]);
				$("#change_in_points").text(data[4]);
				$("#change_in_percent").text(data[2]);
				$("#transaction_volume").text(data[10]);
				$("#transaction_amount").text(data[11]);									
			}
			else if (mid == 'hk'){
				data[6] = Number(parseFloat(data[3])).toFixed(2);
				data[7] = Number(parseFloat(data[7])).toFixed(2);
				data[8] = Number(parseFloat(data[8])).toFixed(2);
				//data[10] = Number(data[10] / 1000000).toFixed(2) + 'M';
				//data[11] = Number(data[11] / 1000000).toFixed(2) + 'M';
				if (data[6] < old_index)
					$("#curr_index").after(us_img_down);
				else if(data[6] > old_index)
					$("#curr_index").after(us_img_up);

				if (data[7] < old_cip)
					$("#change_in_points").after(us_img_down);
				else if(data[7] > old_cip)
					$("#change_in_points").after(us_img_up);

				if (data[8] < old_ciper)
					$("#change_in_percent").after(us_img_down);
				else if(data[8] > old_ciper)
					$("#change_in_percent").after(us_img_up);
				/*
				if (parseFloat(data[10]) < old_vol)
					$("#transaction_volume").after(us_img_down);
				else if(parseFloat(data[10]) > old_vol)
					$("#transaction_volume").after(us_img_up);

				if (parseFloat(data[11]) < old_amount)
					$("#transaction_amount").after(us_img_down);
				else if(parseFloat(data[11]) > old_amount)
					$("#transaction_amount").after(us_img_up);
				*/
				if (data[8] < 0){
					data[8] = Math.abs(data[8]);
					data[7] = Math.abs(data[7]);
					$("#curr_index").addClass('us-value-down');
					$("#change_in_points").addClass('us-value-down');
					$("#change_in_percent").addClass('us-value-down');					
				}
				else if (data[8] > 0){					
					$("#curr_index").addClass('us-value-up');
					$("#change_in_points").addClass('us-value-up');
					$("#change_in_percent").addClass('us-value-up');
					
				}

				data[8] += '%';
				//console.log('change in points: ' + data[7]  +  "  |  change in percent: " + data[8]);
				$("#curr_index").text(data[6]);
				$("#change_in_points").text(data[7]);
				$("#change_in_percent").text(data[8]);
				//$("#transaction_volume").text(data[10]);
				//$("#transaction_amount").text(data[11]);				


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

	var update_price = function(){
		var mid = $("#market_id").text();
		var showtype = $("#showtype").text();
		var id_str = '';
		var id = [];
		var sids = [];
		var temp;	

		$('#rank-info tr').each(function(){	
			var td_first = $(this).find('td:first');
			if(showtype){
				sids[sids.length] = $(this).find('td:first a').text();
			}
			else	
				sids[sids.length] = td_first.html();

			//console.log(sids[sids.length]);
			if(mid != 'ny' && mid != 'nas'){
				id[id.length] = extra['fullcode_prefix'][mid] + sids[sids.length - 1];
			}
			else{
				//console.log('i am in us market');
				temp = extra['fullcode_prefix'][mid] + sids[sids.length - 1].toLowerCase();
				temp = temp.replace(/\./g, "$$");
				id[id.length] = temp; 
			}

			/*
			if (mid == 'sh' || mid == 'sz')
				id[id.length] = mid + sids[sids.length - 1];
			else if (mid == 'ny' || mid == 'nas'){
				temp = 'gb_' + sids[sids.length - 1].toLowerCase();
				temp = temp.replace(/\./g, "$$");
				id[id.length] = temp;
				
			}
			else if (mid == 'hk'){

			}
			*/
		});

		if (id.length > 0){	
			id_str = id.join(',');

			//var url = "http://hq.sinajs.cn/list=" + id_str;
			var url = market[mid]['query_url_head'] + id_str;
			//var url = 
			console.log("url:" + url);

			var ajax_req = $.ajax({
				type: "get",
				url: url,
				cache:"false",
				dataType: "script"
			});	

			ajax_req.done(function(){	
				
				var respond, data, sid, cur_price, old_price, prev_price, change_in_price, prev_change_in_price, change_in_percent, prev_change_in_percent, volume, prev_volume, amount, prev_amount, tr_part;

				var value_down_class = 'us-value-down';
				var value_up_class = 'us-value-up';

				var img_down_class = 'us-img-down';
				var img_up_class = 'us-img-up';

				$.each(id, function(index, item){
					//is_ch = is_us = 0;
					eval("respond" + " = " + "hq_str_" + item);
					sid = sids[index];
					tr_part = $("#tr_" + sid);

					prev_price = parseFloat(tr_part.find('td:eq(2)').html());
					prev_change_in_price = parseFloat(tr_part.find('td:eq(3)').html());
					prev_change_in_percent = parseFloat(tr_part.find('td:eq(4)').html());
					prev_volume = parseFloat(tr_part.find('td:eq(5)').html());
					prev_amount = parseFloat(tr_part.find('td:eq(6)').html());

					//remove value-down value-up img-up img-down class
					tr_part.find('td').removeClass('value-down value-up img-up img-down us-value-up us-value-down us-img-up us-img-down');


					data = respond.split(',');

					var postdata = outside.preProcessShareFromQuery(mid, data);
					cur_price = round(postdata.current_price, 2);
					old_price = round(postdata.closing_price, 2);

					change_in_price = round(cur_price - old_price, 2);

					change_in_percent = round(change_in_price / old_price * 100, 2);

					volume = round(postdata.trade_volume / 1000000, 2);
					

					if (mid == 'sh' || mid == 'sz'){						
						amount = round(postdata.trade_amount / 1000000, 2);
						tr_part.find('td:eq(6)').html(amount);
					    value_down_class = 'value-down';
						value_up_class = 'value-up';

						img_down_class = 'img-down';
						img_up_class = 'img-up';	
					}
					/*	
					if (mid == 'sh' || mid == 'sz'){
						
						cur_price = parseFloat(data[3]); //当下价格
						cur_price = round(cur_price, 2);
						old_price = parseFloat(data[2]); //昨日收盘价
						old_price = round(old_price, 2);

						change_in_price = round(cur_price - old_price, 2);

						change_in_percent = round(change_in_price / old_price * 100, 2);

						volume = round(parseInt(data[8]) / 1000000, 2);
						amount = round(parseInt(data[9]) / 1000000, 2);

					    value_down_class = 'value-down';
						value_up_class = 'value-up';

						img_down_class = 'img-down';
						img_up_class = 'img-up';						
						
					}
					else if (mid == 'ny' || mid == 'nas'){
						
						cur_price = parseFloat(data[1]); //当下价格
						cur_price = round(cur_price, 2);
						old_price = parseFloat(data[26]); //昨日收盘价
						old_price = round(old_price, 2);
						change_in_price = round(cur_price - old_price, 2);
						change_in_percent = round(change_in_price / old_price * 100, 2);
						
						volume = round(parseInt(data[10]) / 1000000, 2);
						//amount = round(parseInt(data[11]) / 1000000, 2);

					}
					*/	
					tr_part.find('td:eq(2)').html(cur_price);
					tr_part.find('td:eq(3)').html(change_in_price);
					tr_part.find('td:eq(4)').html(change_in_percent);
					tr_part.find('td:eq(5)').html(volume);
					/*
					if (mid == 'sh'|| mid == 'sz')
						tr_part.find('td:eq(6)').html(amount);
					*/
					if (cur_price < old_price){					
						tr_part.find('td:eq(2)').addClass(value_down_class);
						tr_part.find('td:eq(3)').addClass(value_down_class);
						tr_part.find('td:eq(4)').addClass(value_down_class);					

					}
					else if (cur_price > old_price){
						tr_part.find('td:eq(2)').addClass(value_up_class);
						tr_part.find('td:eq(3)').addClass(value_up_class);
						tr_part.find('td:eq(4)').addClass(value_up_class);
					}

					if (prev_price && cur_price < prev_price){
						tr_part.find('td:eq(2)').addClass(img_down_class);
					}
					else if (prev_price && cur_price > prev_price){
						tr_part.find('td:eq(2)').addClass(img_up_class);
					}

					if (prev_change_in_price && change_in_price < prev_change_in_price){
						tr_part.find('td:eq(3)').addClass(img_down_class);
					}
					else if (prev_change_in_price&& change_in_price > prev_change_in_price){
						tr_part.find('td:eq(3)').addClass(img_up_class);
					}

					if (prev_change_in_percent && change_in_percent < prev_change_in_percent){
						tr_part.find('td:eq(4)').addClass(img_down_class);
					}
					else if (prev_change_in_percent && change_in_percent > prev_change_in_percent){
						tr_part.find('td:eq(4)').addClass(img_up_class);
					}				

					if (prev_volume && volume < prev_volume){
						tr_part.find('td:eq(5)').addClass(img_down_class);
					}
					else if (prev_volume && volume > prev_volume){
						tr_part.find('td:eq(5)').addClass(img_up_class);
					}									

					if (prev_amount && amount < prev_amount && (market == 'sh'|| market == 'sz')){
						tr_part.find('td:eq(6)').addClass(img_down_class);
					}
					else if (prev_amount && amount > prev_amount && (market == 'sh'|| market == 'sz')){
						tr_part.find('td:eq(6)').addClass(img_up_class);
					}					

				});
			});

			ajax_req.fail(function(xhr, status, error){
				console.log(error.message);
			}); 			
			
			return ajax_req;			
		}
	}	

	var get_rank = function(type){
		var showtype = $("#showtype").text();

		if (typeof type === "undefined" || type === null) { 
			type = "up";
		}

		var mid = $("#market_id").text();

		//var url = "http://stock.yucheung.com/marketinfo/" + mid + '/' + type;
		var url = config.weburl + '/marketinfo/' + mid + '/' + type;
		
		console.log(url);

		var ajax_req = $.ajax({
			type: "get",
			url: url,
			cache:"false",
			dataType: "json"
		});		
		ajax_req.done(function(data){
			
			//console.log('url:' + url);
			var t_body = $("#rank-info");
			//var t_url = 'http://stock.yucheung.com/showtradeform/';
			var t_url = config.weburl + '/showtradeform/';
			//var t_url = $(location).attr('hostname') + '/showtradeform/';

			if (type == 'up'){
				var color = ' class="us-value-up" ';
				if (mid == 'sh' || mid == 'sz')
					var color = ' class="value-up" ';
			}
			else{
				var color = ' class="us-value-down" ';
				if (mid == 'sh' || mid == 'sz')
					var color = ' class="value-down" ';
			}

			

			$.each(data['data'], function(i, v){
				var e = '<tr id="tr_' + v['stock_id'] + '">';
				//console.log(v['stock_name']);
				if (!showtype)
					e += '<td>' + v['stock_id'] + '</td>'; 
				else{
					if(mid == 'ny' || mid == 'nas')
						e += '<td class="share-trade"><span class="icon is-small has-text-success"><i class="fa fa-money"></i></span><a href="' + t_url + 'us' + '/' + v['stock_id'] + '">' + v['stock_id'] + '</a></td>';
					else
						e += '<td class="share-trade"><span class="icon is-small has-text-success"><i class="fa fa-money"></i></span><a href="' + t_url +  mid + '/' + v['stock_id'] + '">' + v['stock_id'] + '</a></td>';
				}

				e += '<td><abbr title="' + v['stock_name'] + '">' + v['stock_name_limit'] + '</abbr></td>' + '<td' + color + '>' + v['trading_price'] + '</td>' + '<td ' + color + '>' + v['change_price'] + '</td>' + '<td ' + color + '>' + v['change_percent'] + '</td>' + '<td>' + v['volume'] + '</td>' + '<td>' + v['amount'] + '</td></tr>';	
				t_body.append(e);
		

			});	
			$("#updated_at").text(data['date']);
		});
		
		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
		});	

		return ajax_req;
	}	
	/*********************************************************************/
	var countdown = $('#countdown').text();
	$.when(get_config()).done(function(c_data){
		config = c_data['config'];
		market = c_data['market'];
		index = c_data['index'];
		extra = c_data['extra'];		
		return get_stock_index();
	}).then(function(){
		interval = setInterval(update_price,10000);

		interval2 = setInterval(function(){
				get_stock_index();
			},10000);	
	});

	if(parseInt(countdown) > 0){
		//console.log('i am in countdown part!');
		var clock = $('.clock').FlipClock(countdown, {
				countdown: true,
				language: 'chinese',
				callbacks: {	
					stop: function(){
						$('.clock').hide();
					}
				}		
		});	
	}
	//interval = setInterval(update_price,10000);


	/********************************************************************/

	$("#chart-nav a").on('click', function(){
		var mid = $("#market_id").text();
		var temp = $(this).attr('id').split('-');
		var id = temp[0];
		var prev_a_tag = $("#chart-nav a[aria-current='page']");
		var chart = $("#chart img");

		if (id){
			$('#type-for-chart').text(type_des[id]);
			if (mid == 'sh' || mid == 'sz')
				chart.attr('src', http_hd + type[id] + 'n\/' + url[mid] + '?' + (new Date()).getTime());

			prev_a_tag.removeAttr('aria-current');
			prev_a_tag.parent().removeClass('is-active');
			$(this).attr('aria-current', 'page');
			$(this).parent().addClass('is-active');	
		}

		else
			$('#type-for-chart').text('错误');

	});

	$("#active-nav a").on('click', function(){
		//stop update_price per seconds

		clearInterval(interval);

		var temp = $(this).attr('id').split('-');
		var id = temp[0];
		var prev_a_tag = $("#active-nav a[aria-current='page']");
		
		$("#rank-info tr").remove();
		if (id){
			prev_a_tag.removeAttr('aria-current');
			prev_a_tag.parent().removeClass('is-active');
			$(this).attr('aria-current', 'page');
			$(this).parent().addClass('is-active');	
			//get_rank(id);


			$.when(get_rank(id)).then(function(){
				interval = setInterval(update_price,10000);
			});	
		}

	});

	
});	