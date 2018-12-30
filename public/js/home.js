//var http_hd = 'http:\/\/image.sinajs.cn\/newchart\/min\/n\/';

//var http_usa = 'http:\/\/image.sinajs.cn\/newchart\/usstock\/';
//var list = {'sh':'上证指数', 'sz':'深证指数', 'dow':'道琼指数', 'nas':'纳斯达克指数'};
//var url = {'sh':'sh000001.gif', 'sz':'sz399001.gif', 'dow':'min_idx_py\/dji.gif', 'nas':'min_idx_py\/ixic.gif'}
//var url = {};

var config; 
var market; 
var index; 

//var market_id = {'sh': 's_sh000001', 'sz' : 's_sz399001', 'dow': 'gb_dji', 'nas': 'gb_ixic'}
//var market_id = {};


var interval, interval_2;

var img_up = "<img src='../images/ch-up-img.png'>";
var img_down =  "<img src='../images/ch-down-img.png'>";

var us_img_up = "<img src='../images/us-up-img.png'>";
var us_img_down =  "<img src='../images/us-down-img.png'>";

$(function(){
	
	var get_last_events = function(){
		var last_updated = btoa($("#last-updated").text());

		//console.log(last_updated);
		if(last_updated){
			//var url = "http://stock.yucheung.com/getlastevents/" + last_updated;
			var url = config.weburl + '/getlastevents/' + last_updated;
			var ajax_req = $.ajax({
				type: "get",
				url: url,
				cache:"false",
				dataType: "json"
			});	
			ajax_req.done(function(data){
				//console.log('receive update time: ');
				//console.log(data);
				if(typeof data.events !== undefined && Array.isArray(data.events)){
					$.each(data.events, function(i, v){
						var e = '<div class="event-element" id="el-' + v.eid + '">' + '<span class="event-time">' + v.updated_at + '</span>' + '<span class="event-event">' + v.event + '</span></div>';
						$("#events").prepend(e);						
					});					
				}
				if(typeof data.last_updated !== undefined)
					$("#last-updated").text(data.last_updated);
			});
			ajax_req.fail(function(xhr, status, error){
				console.log(error.message);
			}); 			
			
			return ajax_req;
		}			
	}

	var get_stock_index = function(mid){
		if (typeof mid === "undefined" || mid === null) { 
			//mid = "sh"; 
			mid = config['default_index'];
		}		
		var data;

		//var url = "http://hq.sinajs.cn/list=" + market_id[mid];
		var url = index[mid]['index_query_url'];
		var ajax_req = $.ajax({
			type: "get",
			url: url,
			cache:"false",
			dataType: "script"
		});
		
		ajax_req.done(function(){
			data = url.split("=");

			eval("var respond" + " = " + "hq_str_" + data[1]);
			//console.log(respond);
			$("#index-info .value-up,.value-down,.us-value-up,.us-value-down").removeClass('value-up value-down us-value-up us-value-down');
			$("#index-info img").remove();

			var old_index = $("#curr_index").text();
			if (old_index)
				old_index = Number(parseFloat(old_index)).toFixed(2);
			else
				old_index = 0;

			var old_cip = $("#change_in_points").text();
			if (old_cip)
				old_cip = Number(parseFloat(old_cip)).toFixed(2);
			else
				old_cip = 0;
			var old_ciper = $("#change_in_percent").text();
			if (old_ciper)
				old_ciper = Number(parseFloat(old_ciper)).toFixed(2);
			else
				old_ciper = 0;

			var old_vol = $("#transaction_volume").text(); 
			//old_vol = parseFloat(old_vol);
			if (old_vol)
				old_vol = parseFloat(old_vol);
			else
				old_vol = 0;
			var old_amount = $("#transaction_amount").text(); 
			//old_amount = Number(old_amount).toFixed(2);
			if (old_amount)
				old_amount = parseFloat(old_amount);
			else
				old_amount = 0;

			data = respond.split(',');

			//console.log(respond);

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
			else if (mid == 'dow' || mid == 'nas'){
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

			else if (mid == 'hsi'){
				console.log('i am in hk index!');
				data[6] = Number(parseFloat(data[6])).toFixed(2);
				data[7] = Number(parseFloat(data[7])).toFixed(2);
				data[8] = Number(parseFloat(data[8])).toFixed(2);
				if (data[9])
					data[9] = Number(data[9] / 1000000).toFixed(2) + 'M';
				if (data[10])
					data[10] = Number(data[10] / 1000000).toFixed(2) + 'M';

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

				if (data[8] < 0){
					data[7] = Math.abs(data[7]);
					data[8] = Math.abs(data[8]);
					$("#curr_index").addClass('us-value-down');
					$("#change_in_points").addClass('us-value-down');
					$("#change_in_percent").addClass('us-value-down');					
				}
				else if (data[8] > 0){					
					$("#curr_index").addClass('us-value-up');
					$("#change_in_points").addClass('us-value-up');
					$("#change_in_percent").addClass('us-value-up');
					
				}

				
				if (data[9] && parseFloat(data[9]) < old_vol)
					$("#transaction_volume").after(us_img_down);
				else if(data[9] && parseFloat(data[9]) > old_vol)
					$("#transaction_volume").after(us_img_up);

				if (data[10] && parseFloat(data[10]) < old_amount)
					$("#transaction_amount").after(us_img_down);
				else if(data[10] && parseFloat(data[10]) > old_amount)
					$("#transaction_amount").after(us_img_up);

				data[8] += '%';

				$("#curr_index").text(data[6]);
				$("#change_in_points").text(data[7]);
				$("#change_in_percent").text(data[8]);
				if (data[9])
					$("#transaction_volume").text(data[9]);
				else
					$("#transaction_volume").text('0M')
				if (data[10])
					$("#transaction_amount").text(data[10]);
				else
					$("#transaction_amount").text('0M');								
			}	
		});					
			
		ajax_req.fail(function(xhr, status, error){
			/**
			$("#error-dialog").html(xhr.responseText);	
			**/
			console.log(error.message);
		}); 			
		
		return ajax_req;		


	}


	/*********************************************************************/

/**
	$.when(get_config()).done(get_stock_index()).then(function(){
				interval = setInterval(function(){
						get_stock_index();
					},10000);
			});	
**/

	$.when(get_config()).done(function(c_data){
		//console.dir(c_data);
		config = c_data['config'];
		market = c_data['market'];
		index = c_data['index'];

		return get_stock_index();		/**
		$("#jjson").jJsonViewer(c_data['config'], {expanded: true});
		$("#jjson").show();
		**/
	}).then(function(){

		interval = setInterval(function(){
				get_stock_index();
			},10000);

		interval_2 = setInterval(function(){
				get_last_events();	
		}, 10000);		
	});	
	$("#chart-nav a").on('click', function(){

		//stop update_price per seconds

		clearInterval(interval);

		var prev_a_tag = $("a[aria-current='page']");
		var title = $("#title-for-chart");
		var temp = $(this).attr('id').split('-');
		var chart = $("#chart img");
		var id = temp[0];
		if (id){
			title.text(index[id]['name']);
			chart.attr('src', index[id]['min_chart_url'] + '?' + (new Date()).getTime());
			/**
			if (id == 'dow' || id == 'nas')
				chart.attr('src', http_usa + url[id] + '?' + (new Date()).getTime());
				chart.attr('src', index[id]['index_query_url'] + '?' + (new Date()).getTime());
			else	
				chart.attr('src', http_hd + url[id] + '?' + (new Date()).getTime());
			**/
			prev_a_tag.removeAttr('aria-current');
			prev_a_tag.parent().removeClass('is-active');
			$(this).attr('aria-current', 'page');
			$(this).parent().addClass('is-active');

			var d = get_stock_index(id);

			$.when(d).then(function(){
				interval = setInterval(function(){
						get_stock_index(id);
					},10000);
			});
		}
		else
			title.text('错误');

	});	


});	