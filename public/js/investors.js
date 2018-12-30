$(function(){
	var temp;
	var row_data = ['coins', 'bind_to', 'level'];

	var reset_all_rows = function(){
		
		$('select').each(function() {
			var sel = $(this);
			sel.val($(this).data('prev'));			
		});
		
		$('input:text').each(function() {
			var input = $(this);
			input.val($(this).data('prev'));			
		});			

	}

	var reset_rest_rows = function(key){

		$('select').each(function() {
			var k = get_key($(this).attr('id'));
			if (k != key){
				var sel = $(this);
				sel.val($(this).data('prev'));			
			}
		});
		$('input:text').each(function() {
			var k = get_key($(this).attr('id'));
			if (k != key){
				var input = $(this);
				input.val($(this).data('prev'));			
			}
		});	

	}

	var check_row_change = function(key){
		if($("#coins-" + key).val() == $("#coins-" + key).data('prev') && $("#level-" + key).val() == $("#level-" + key).data('prev') && $("#bind_to-" + key).val() == $("#bind_to-" + key).data('prev'))
			return false;
		else
			return true;
	}

	var get_key = function(id){
		var temp = id.split('-');
		return temp[1];		
	}

	var process_ban_btn = function(key){
		if (parseInt($("#level-" + key).val()) < 1)
			$("#ban-" + key).hide();
		else
			$("#ban-" + key).show();
	}

	var set_prev_data = function(){
		//储存所有下拉菜单的prev值
		$('select').each(function() {
			var sel = $(this);
			sel.data("prev", sel.val());

		});
		
		//储存所有输入框的prev值,检查level值
		$('input:text').each(function() {
			var input = $(this);
			input.data("prev", input.val());	
			if ($(this).attr('name') == 'level' && parseInt($(this).val()) < 1){
				var key = get_key($(this).attr('id'));
				$("#ban-" + key).hide();
			}			

		});			
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
			            process_ban_btn(key);
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
	/********************/

	$(".is-success").hide();


	set_prev_data();


	$("[name='coins'], [name='level']").on("blur",function(){
		var key = get_key($(this).attr('id'));
	
		var edit_btn = $("#edit-" + key);
		var prev_val = $(this).data('prev');

		if (prev_val != $(this).val()){
			edit_btn.show();
		}
		else{
			if (!check_row_change(key))
				edit_btn.hide();
		}
	});

	$("[name='bind_to']").on('change', function(){
		var key = get_key($(this).attr('id'));
		var edit_btn = $("#edit-" + key);
		var prev_val = $(this).data('prev');

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
		//var test='';
		$.each(row_data, function(i, v){
			var id = v + '-' + key;
			//formData.append(v, $("#" + id).val());
			formData[v] = $("#" + id).val();
			//test += v + ':' + id + ':' +  $("#" + id).val() + ' ';
		});
		//console.log(test);
		//console.log(formData);
		var url = '/admin/editinvestor/' + key;
		$.ajaxSetup({
    		headers: {
        		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    		}
		});

		console.log(formData);
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
				var username = $("#username-" + key).text();
				if (m == 'update success'){
					showmsg(username + '更新完毕', key, 1);
				}

				else if (m == 'validation failed')
					showmsg(username + '更新失败, 有输入不符', key, 1);
			}

		});

		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
		}); 			
	
		return ajax_req;				
	});

	$("[name='ban']").on('click', function(e){
		var key = get_key($(this).attr('id'));		

		var url = '/admin/baninvestor/' + key;
		e.preventDefault();
		$("#level-" + key).val(0);
		var ajax_req = $.ajax({
			type: 'get',
	        url: url,
	        cache: false,
	        dataType: 'json'		            
		});

		ajax_req.done(function(data){
			console.log(data);
			//$("#jjson").jJsonViewer(data);
			if (typeof data['msg'] != undefined && data['msg'] ){
				var m = data['msg'];
				var username = $("#username-" + key).text();
				if (m == 'ban success'){					
					showmsg(username + '成功被禁', key, 1);
				}

			}

		});
	});	
});	