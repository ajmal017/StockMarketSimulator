@extends('admin')
@section('main')
      <section class="section">
        <div class="container">    
        	 <h2 class="title">{{$currency[$cid]['name']}}编辑</h2>
        	 @if(!empty($currency))
        	 <form id="edit-form" action="{{route('editcurrency', ['id' => $id])}}" method="post" enctype="multipart/form-data"> 
	           <div class="field">
	             <label class="label">货币名</label>
	               <div class="control has-icons-left">
	                  @if(isset($currency[$cid]['name']))
	                     <input class="input" type="text" name ="name" placeholder="Pick a stock name..." value="{{$currency[$cid]['name']}}" required minlength="1">
	                  @else
	                      <input class="input" type="text" name ="name" placeholder="Pick a stock name..." required minlength="1">
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-money"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label danger-label">货币代码（重要数据）</label>
	               <div class="control has-icons-left">
	                  @if(isset($currency[$cid]['currency_index']))
	                     <input class="input" type="text" name ="currency_index" placeholder="Pick a stock name..." value="{{$currency[$cid]['currency_index']}}" required minlength="2">
	                  @else
	                      <input class="input" type="text" name ="currency_index" placeholder="Pick a stock name..." required minlength="2">
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-user-secret"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">汇率查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                  @if(isset($currency[$cid]['rate_query_url']))
	                     <input class="input" type="text" name ="rate_query_url" placeholder="Pick a stock name..." value="{{$currency[$cid]['rate_query_url']}}" required>
	                  @else
	                      <input class="input" type="text" name ="rate_query_url" placeholder="Pick a stock name..." required>
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div> 

	          <div class="field">
	               <label class="label">状态</label>
	               <div class="control has-icons-left">
	                  <div class="select">
	                  	<select name="status">
	                  		@if($currency[$cid]['status'])
	                  			<option value='1' selected>开启</option>
	                  			<option value='0'>关闭</option>
	                  		@else
	                  			<option value='1'>开启</option>
	                  			<option value='0' selected>关闭</option>
	                  		@endif
	                  	</select>	
	                  </div>
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-cogs"></i> 
	                  </span>    	                  
	               </div>
	           </div>
        
	            <hr>
	           <div class="field is-grouped">
	              <div class="control">
	                <input type="submit" class="button is-primary" value="编辑"></input>
	                <input type="hidden" name="_token" value="{{ Session::token() }}">
	              </div>
	            </div>

        	 </form>
        	 @endif
        </div>
      </section>

@stop


<div id="confirm-dialog" title="确认" class="inactivo">
<p>
更改货币代码可能会造成问题，确认要更改股市代码吗?
</p>
</div>

@section('scripts')
<script>
	$('#edit-form').parsley();
	$(function() {
		var old_index = $("input[name='currency_index']").val();
		//console.log(old_index);

		$("input[name='market_index']").on("blur",function(){
			if ($("input[name='market_index']").val() != old_index){
    			$( "#confirm-dialog" ).removeClass('inactivo');
    			$( "#confirm-dialog" ).addClass('activo');
    			$("#confirm-dialog").dialog({
    				modal:true,
    				width:400,
    				position: ['center', 'center'],
    				resizable: false,
    				dialogClass: "alert",
					 buttons: {
				        "确认": function() {
				            $(this).dialog("close");
				            old_index = $("input[name='currency_index']").val();
				        },
				        "取消": function(){
				        	$("input[name='market_index']").val(old_index);
				        	$(this).dialog("close");
				        }
					 }
    			});				
			}	
		
		});

	});
    
</script>    
@stop