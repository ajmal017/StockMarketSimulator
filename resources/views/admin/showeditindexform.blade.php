@extends('admin')
@section('main')
      <section class="section">
        <div class="container">    
        	 <h2 class="title">{{$index[$iid]['name']}}编辑</h2>
        	 @if(!empty($index))
        	 <form id="edit-form" action="{{route('editindex', ['id' => $id])}}" method="post" enctype="multipart/form-data"> 
	           <div class="field">
	             <label class="label">指数名称</label>
	               <div class="control has-icons-left">
	                  @if(isset($index[$iid]['name']))
	                     <input class="input" type="text" name ="name" placeholder="Pick a stock name..." value="{{$index[$iid]['name']}}" required minlength="2">
	                  @else
	                      <input class="input" type="text" name ="name" placeholder="Pick a stock name..." required minlength="2">
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-line-chart"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label danger-label">指数代码（重要数据）</label>
	               <div class="control has-icons-left">
	                  @if(isset($index[$iid]['index_index']))
	                     <input class="input" type="text" name ="index_index" placeholder="Pick a stock name..." value="{{$index[$iid]['index_index']}}" required minlength="1">
	                  @else
	                      <input class="input" type="text" name ="index_index" placeholder="Pick a stock name..." required minlength="1">
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-user-secret"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">指数查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                  @if(isset($index[$iid]['index_query_url']))
	                     <input class="input" type="text" name ="index_query_url" placeholder="Pick a stock name..." value="{{$index[$iid]['index_query_url']}}" required>
	                  @else
	                      <input class="input" type="text" name ="index_query_url" placeholder="Pick a stock name..." required>
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">分时线图查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                  @if(isset($index[$iid]['min_chart_url']))
	                     <input class="input" type="text" name ="min_chart_url" placeholder="Pick a stock name..." value="{{$index[$iid]['min_chart_url']}}" required>
	                  @else
	                      <input class="input" type="text" name ="min_chart_url" placeholder="Pick a stock name..." required>
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div>

	           <div class="field">
	             <label class="label">属于市场（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                  @if(isset($index[$iid]['belongsToMarket']))
	                  	<div class="select">
	                  		<select name="belongsToMarket">
	                  			@foreach($market as $mid => $mdata)
	                  				@if($mid == $index[$iid]['belongsToMarket'])
	                  					<option value="{{$mid}}" selected>{{$mdata['name']}}</option>
	                  				@else
	                  					<option value="{{$mid}}">{{$mdata['name']}}</option>
	                  				@endif
	                  			@endforeach
	                  		</select>
	                  	</div>
						@else                  
	                  		<div class="select">
		                  		<select name="belongsToMarket">
		                  			@foreach($market as $mid => $mdata)		                  				                  				
		                  				<option value="{{$mid}}">{{$mdata['name']}}</option>		                  				
		                  			@endforeach
		                  		</select>
		                  	</div>
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
	                  		@if($index[$iid]['status'])
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
更改指数代码可能会造成问题，确认要更改指数代码吗?
</p>
</div>

@section('scripts')
<script>
    $('#edit-form').parsley();
	$(function() {
		var old_index = $("input[name='index_index']").val();
		//console.log(old_index);

		$("input[name='index_index']").on("blur",function(){
			if ($("input[name='index_index']").val() != old_index){
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
				            old_index = $("input[name='index_index']").val();
				        },
				        "取消": function(){
				        	$("input[name='index_index']").val(old_index);
				        	$(this).dialog("close");
				        }
					 }
    			});				
			}	
		
		});

	});    
</script>    
@stop