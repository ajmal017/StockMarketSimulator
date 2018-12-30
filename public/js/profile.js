	$(function(){

 		var msgs = ['尺寸不能超过5M', '只支持jpg, gif, png格式'];
	 	var size_limit = 5;   //5 MB
	 	var image_types = ["image/gif", "image/jpeg", "image/png"];
	 	var file = $("#avator");

	 	$("#upload-button").hide();
 		
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
			            $(".file-name").text('not selected yet');
			            $("#confirm-dialog p").text('');
			            $("#upload-button").hide();
			        }
				 }
			});	
 		}	 	


 		/******************************************************/


 	 	file.on('change', function(){
	 		if(file.get(0).files.length > 0){
	 			$(".file-name").text(file.get(0).files.item(0).name);
	 			$("#upload-button").show();
	 		}
	 	});

	 	$('#upload').on('click', function(e){	 		
	 		
	 		//$("#jjson").empty();
	 		//$("#jjson").show();

	 		
	 		$( "#confirm-dialog" ).removeClass('inactivo');
	 		var filesize = file.get(0).files.item(0).size; 
	 		var filetype = file.get(0).files.item(0).type;

	 		//var formdata = new FormData();
	 		//formdata.append('file', file.get(0).files.item(0));
	 		//formdata.append('_token', {{ csrf_token() }});

	 		if (filesize > size_limit * 1024 * 1024)
	 			showerror(msgs[0]);
	 		else if (!in_array(filetype, image_types))
	 			showerror(msgs[1]);
	 		else{

				$.ajaxSetup({
            		headers: {
                		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            		}
        		})

        		e.preventDefault();
        		
        		
        		var formData = new FormData();
        		var upload_file = $("#avator")[0].files[0];
        		formData.append('avator', upload_file);
				
        		
        		/*
        		var formData = {
            		avator: file.get(0).files.item(0)            		
       			}
       			*/

       			console.log(formData);

       			var ajax_req = $.ajax({
					type: 'POST',
		            url: '/uploadavator',
		            data: formData,
		            cache: false,
		            contentType: false,
		            processData: false,
		            dataType: 'json'		            
       			});

       			ajax_req.done(function(data){
					//$("#jjson").jJsonViewer(data);
					var iid = data['iid'];
					var name_encode = data['name_encode'];
					
					//$("#current-avator").attr('src', '/avator/' + iid + '/' + name_encode);
					$("#current-avator").css("background-image", "url(" + '/avator/' + iid + '/' + name_encode + ")");
					$(".file-name").text('not selected yet');
					$("#upload").hide();       					
       			});
       			
	 			ajax_req.fail(function(xhr, status, error){
					console.log(error.message);
				}); 			
				
				return ajax_req;	      				
			}					
		 		
	 	});
 	});