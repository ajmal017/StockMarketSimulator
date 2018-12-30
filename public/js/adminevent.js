$(function(){

	var config; 
	var market; 
	var index; 

	var get_key = function(id){
		var temp = id.split('-');
		return temp[1];
	}
	/*
	var get_filter = function(id){
		var temp = id.split('-');
		return temp[1];
	}
	*/

	var get_del_events = function(){
		var ret = [];
		var checkboxes = $("tr :checkbox")
		$("#events-body").find(':checkbox').each(function(){
			if(this.checked){
				var eid = get_key($(this).attr('id'));
				ret.push(eid);
			}
		});
		return ret;
	}

	var get_events = function(url){
		var ajax_req = $.ajax({
			type: "get",
			url: url,
			cache:"false",
			dataType: "json"
		});		

		ajax_req.done(function(data){
			console.log(data);
			$("#events-body").empty();
			$("#links").empty();

			$.each(data.events, function(i, v){
				var eid = v.eid;
				var e = '<tr id="tr_' + eid + '">';
				e += '<td width="10%"><input type="checkbox" id="delet-' + eid + '">' + '</td>' + '<td width="10%">' + eid + '</td>' + '<td width="10%">' + v.type_in_words + '</td>' + '<td width="50%">' + v.event + '</td>' + '<td width="20%">' + v.updated_at + '</td></tr>';
				$("#events-body").append(e);
			});
			$("#links").html(data.links);
		});	

		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
		}); 
	}

	var showmsg = function(msg){
		$("#confirm-dialog").addClass('activo');
		$("#confirm-dialog p").text(msg);
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
		        }
			 }
		});	
	}	 	

/************************************************************************************/
	$.when(get_config()).done(function(c_data){
		config = c_data['config'];
		market = c_data['market'];
		index = c_data['index'];		
	});	

	$("#select-all").on('click', function(){
		$("#events-body").find(':checkbox').each(function(){
			this.checked = !this.checked;
		});

	});


	$("#event-select a").on('click', function(){
		var filter = get_key($(this).attr('id'));
		var orderby = $("#orderby").val();		
		var prev_a_tag = $("a[aria-current='page']");
		//var url = 'http://stock.yucheung.com/admin/grabevents/' + filter + '/' + orderby;
		var url = config.weburl + '/admin/grabevents/' + filter + '/' + orderby;

		console.log(filter + ' | ' + orderby + ' | ' + url);
		get_events(url);

		prev_a_tag.removeAttr('aria-current');
		prev_a_tag.parent().removeClass('is-active');
		$(this).attr('aria-current', 'page');
		$(this).parent().addClass('is-active');							
	});

	$("#orderby").on('change', function(){
		
		var filter = get_key($("a[aria-current='page']").attr('id'));
		console.log('filter: ' + filter);
		var orderby = $(this).val();		

		//var url = 'http://stock.yucheung.com/admin/grabevents/' + filter + '/' + orderby;
		var url = config.weburl + '/admin/grabevents/' + filter + '/' + orderby;
		console.log(filter + ' | ' + orderby + ' | ' + url);
		get_events(url);		

	});

	$("#del").on('click', function(e){
		var del_eids = get_del_events();
		if(del_eids.length == 0){
			showmsg('没有勾选删除的事件！');
			return;
		}
		console.log(del_eids);
		//var url = 'http://stock.yucheung.com/admin/delevents';
		var url = config.weburl + '/admin/delevents';

		$.ajaxSetup({
    		headers: {
        		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    		}
		})

		e.preventDefault();
		
		/*
		var formData = new FormData();
		var upload_file = $("#avator")[0].files[0];
		formData.append('avator', upload_file);
		*/

		var data = {
			del_eids: del_eids
		};
		//console.log(data);
		
		

		var ajax_req = $.ajax({
			type: 'POST',
	        url: url,
	        data: data,
	        dataType: 'json'
		});	

		ajax_req.done(function(data){
			console.log(data);
			if(typeof data !== undefined && data){

				var filter = get_key($("a[aria-current='page']").attr('id'));
				//console.log('filter: ' + filter);
				var orderby = $("#orderby").val();		

				//var url = 'http://stock.yucheung.com/admin/grabevents/' + filter + '/' + orderby;
				var e_url = config.weburl + '/admin/grabevents/' + filter + '/' + orderby;
				console.log(filter + ' | ' + orderby + ' | ' + e_url);
				get_events(e_url);				
			}

		});
		
		ajax_req.fail(function(xhr, status, error){
			console.log(error.message);
		}); 			
		
	});
});	