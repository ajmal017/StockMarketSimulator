@extends('admin')
@section('main')
      <section class="section">
        <div class="container">    
        	 <h2 class="title">{{$market[$mid]['name']}}编辑</h2>
        	 @if(!empty($market))
        	 <form id="edit-form" action="{{route('editmarket', ['id' => $id])}}" method="post" enctype="multipart/form-data"> 
	           <div class="field">
	             <label class="label">股市名</label>
	               <div class="control has-icons-left">
	                  @if(isset($market[$mid]['name']))
	                     <input class="input" type="text" name ="name" placeholder="Pick a stock name..." value="{{$market[$mid]['name']}}" required minlength="1">
	                  @else
	                      <input class="input" type="text" name ="name" placeholder="Pick a stock name..." required minlength="1">
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-line-chart"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label danger-label">股市代码（重要数据）</label>
	               <div class="control has-icons-left">
	                  @if(isset($market[$mid]['market_index']))
	                     <input class="input" type="text" name ="market_index" placeholder="Pick a stock name..." value="{{$market[$mid]['market_index']}}" required minlength="2">
	                  @else
	                      <input class="input" type="text" name ="market_index" placeholder="Pick a stock name..." required minlength="2">
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-user-secret"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">股票查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                  @if(isset($market[$mid]['query_url_head']))
	                     <input class="input" type="text" name ="query_url_head" placeholder="Pick a stock name..." value="{{$market[$mid]['query_url_head']}}" required>
	                  @else
	                      <input class="input" type="text" name ="query_url_head" placeholder="Pick a stock name..." required>
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">涨幅排行榜查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                  @if(isset($market[$mid]['rank_up_url']))
	                     <input class="input" type="text" name ="rank_up_url" placeholder="Pick a stock name..." value="{{$market[$mid]['rank_up_url']}}" required>
	                  @else
	                      <input class="input" type="text" name ="rank_up_url" placeholder="Pick a stock name..." required>
	                  @endif
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div>

	           <div class="field">
	             <label class="label">跌幅排行榜查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                  @if(isset($market[$mid]['rank_down_url']))
	                     <input class="input" type="text" name ="rank_down_url" placeholder="Pick a stock name..." value="{{$market[$mid]['rank_down_url']}}" required>
	                  @else
	                      <input class="input" type="text" name ="rank_down_url" placeholder="Pick a stock name..." required>
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
	                  		@if($market[$mid]['status'])
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

	          @if(isset($currency))
	           <div class="field">
	               <label class="label">使用货币</label>               
	               <div class="control has-icons-left">
	                  <div class="select">
	                  <select  name="allowed_currency">
	                  @if(isset($market[$mid]['allowed_currency']))
	                    @foreach($currency as $cid => $cdata)
	                         @if($market[$mid]['allowed_currency'] == $cdata['currency_index'])
	                            <option value="{{$cid}}" selected>{{$cdata['name']}}</option>
	                         @else
	                          	<option value="{{$cid}}">{{$cdata['name']}}</option>
	                         @endif
	                    @endforeach      
	                  @else
	                       @foreach($currency as $cid => $cdata)
	                          <option value="{{$cid}}">{{$cdata['name']}}</option>
	                       @endforeach 
	                  @endif 
	                  </select>
	                  </div>
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-line-chart"></i>
	                  </span>      
	               </div> 
	            </div>
	            @endif
              
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
更改股市代码可能会造成问题，确认要更改股市代码吗?
</p>
</div>

@section('scripts')
<script>
	$('#edit-form').parsley();
	$(function() {
		var old_index = $("input[name='market_index']").val();
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
				            old_index = $("input[name='market_index']").val();
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