$(function(){
	var temp;
	var row_data = ['buyingprice', 'sellingprice', 'amount'];
	var row_data_map = {
		'buyingprice': 'buying_price',
		'sellingprice': 'selling_price',
		'amount': 'amount'
	}
	var owner;

	var reset_all_rows = function(){		
		$('input:text').each(function() {
			var input = $(this);
			input.val($(this).data('prev'));			
		});			

	}

	var reset_rest_rows = function(key){

		$('input:text').each(function() {
			var k = get_key($(this).attr('id'));
			if (k != key){
				var input = $(this);
				input.val($(this).data('prev'));			
			}
		});	

	}



	var set_prev_data = function(){				
		//储存所有输入框的prev值,检查level值
		$('input:text').each(function() {
			var input = $(this);
			input.data("prev", input.val());
			/*	
			if ($(this).attr('name') == 'level' && parseInt($(this).val()) < 1){
				var key = get_key($(this).attr('id'));
				$("#ban-" + key).hide();
			}			
			*/
		});			
	}	

	var get_key = function(id){
		var temp = id.split('-');
		return temp[1];		
	}

	var get_owner = function(){
		var temp = $("span[name='owner']").attr('id').split('-');
		return temp[1];
	}

	var check_row_change = function(key){
		if($("#buyingprice-" + key).val() == $("#buyingprice-" + key).data('prev') && $("#sellingprice-" + key).val() == $("#sellingprice-" + key).data('prev') && $("#amount-" + key).val() == $("#amount-" + key).data('prev'))
			return false;
		else
			return true;
	}

	var showmsg = function(msg, key, success){
		if (!success)
			$("#confirm-dialog").attr('title', '错误');
		else
			$("#confirm-dialog").attr('title', '成功');

		$("#confirm-dialog").addClass('activo');
		$("#confirm-dialog p").text(msg);
		if (success == 1){
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
			            $("#edit-" + key).hide();
			            //process_ban_btn(key);
			            set_prev_data();
			            //process_ban_btn(key);
			        }
				 }
			});	
		}
		else{
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
			            //$("#edit-" + key).hide();
			            reset_all_rows();
			        }
				 }
			});				
		}
 	}	 	

	/*************************************/

	set_prev_data();

	$("a[name='edit']").each(function(){
		$(this).hide();
	});
	
	owner = get_owner();

	$("[name='buying_price'], [name='selling_price'], [name='amount']").on("blur",function(){
		var key = get_key($(this).attr('id'));
		var sharename = $("#sharename-" + key).text();
		var edit_btn = $("#edit-" + key);
		var prev_val = $(this).data('prev');

		if($(this).attr('id') == 'amount-' + key && (parseInt($(this).val()) > parseInt(prev_val) || parseInt($(this).val()) < 0)){
			//console.log('in amount part!');
			showmsg(sharename + '的拥股量必须在0和原有的数量之间!', key, 0);
			return;
		}

		if (prev_val != $(this).val()){
			edit_btn.show();
		}
		else{
			if (!check_row_change(key))
				edit_btn.hide();
		}		
	});


	$("[name='edit']").on('click', function(e){	
		var key = get_key($(this).attr('id'));
		var formData = {};

		var amount = parseInt($("#amount-" + key).val());

		if(amount <= 0){
			showmsg(sharename + '的拥股量在编辑模式下必须大于0', key, 0);
			return;
		}

		formData['owner'] = owner;
		$.each(row_data, function(i, v){
			var id = v + '-' + key;
			//console.log(id);
			formData[row_data_map[v]] = $("#" + id).val();
			//test += v + ':' + id + ':' +  $("#" + id).val() + ' ';
		});

		var url = '/admin/editshare/' + key;
		$.ajaxSetup({
    		headers: {
        		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    		}
		});

		console.log(formData);
		//return;
		e.preventDefault();

		var ajax_req = $.ajax({
			type: 'POST',
	        url: url,
	        data: formData,
	        cache: false,
	        dataType: 'json'		            
		});

		ajax_req.done(function(data){
			console.log(data);
			//$("#jjson").jJsonViewer(data);
			if (typeof data['msg'] != undefined && data['msg'] ){
				var m = data['msg'];
				var sharename = $("#sharename-" + key).text();
				if (m == 'update success'){
					showmsg(sharename + '更新完毕', key, 1);
				}

				else if (m == 'validation failed'){
					showmsg(sharename + '更新失败, 有输入不符', key, 1);
					return;
				}

				$("#sellingprice-" + key).val('0');
			}

		});

		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
		}); 			
	
		return ajax_req;						
	});


	$("[name='forcesell']").on('click', function(e){
		var key = get_key($(this).attr('id'));
		var formData = {};

		var amount = parseInt($("#amount-" + key).val());		
		var prev_amount = $("#amount-" + key).data('prev');

		if(amount < 0 || amount >= prev_amount){
			showmsg(sharename + '的拥股量在强卖模式下必须在0和原有的数量之间!', key, 0);
			return;
		}

		var new_selling_price = $("#sellingprice-" + key).val();

		formData['owner'] = owner;
		$.each(row_data, function(i, v){
			var id = v + '-' + key;
			//console.log(id);
			formData[row_data_map[v]] = $("#" + id).val();
			//test += v + ':' + id + ':' +  $("#" + id).val() + ' ';
		});

		var url = '/admin/forcesell/' + key;
		$.ajaxSetup({
    		headers: {
        		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    		}
		});

		console.log(formData);
		//return;
		e.preventDefault();

		var ajax_req = $.ajax({
			type: 'POST',
	        url: url,
	        data: formData,
	        cache: false,
	        dataType: 'json'		            
		});

		ajax_req.done(function(data){
			console.log(data);
			//$("#jjson").jJsonViewer(data);
			if (typeof data['msg'] != undefined && data['msg'] ){
				var m = data['msg'];
				var sharename = $("#sharename-" + key).text();
				if (m == 'update success'){
					var sellamount = parseInt(data['sellamount']);
					var selling_price = parseFloat(data['selling_price']);
					var gain = parseFloat(data['gain']);

					if(sellamount >= prev_amount)
						var sellamount_msg = '清仓出货, ';
					else
						var sellamount_msg = '一共出货' + sellamount + ', ';
					var msg = sharename + '强卖完成, 详情：以价格' +  selling_price + sellamount_msg + '本次强卖让' + $("span[name='owner']").text() + ' 一共获利现金' + gain;
					showmsg(msg, key, 1);
				}

				else if (m == 'validation failed'){
					var errors = data[errors];
					showmsg(sharename + '强卖失败, 有输入不符: ' + errors, key, 1);
					return;
				}


				$("#sellingprice-" + key).val(new_selling_price);
			}

		});

		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
		}); 			
	
		return ajax_req;

	});		
});	