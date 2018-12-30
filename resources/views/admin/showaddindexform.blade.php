@extends('admin')
@section('main')
      <section class="section">
        <div class="container">    
        	 <h2 class="title">添加指数</h2>
        	
        	 <form id="edit-form" action="{{route('addindex')}}" method="post" enctype="multipart/form-data"> 
	           <div class="field">
	             <label class="label">指数名称</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="name" placeholder="Pick a stock name..." required minlength="2">
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-line-chart"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label danger-label">指数代码（重要数据,请查阅清楚）</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="index_index" placeholder="Pick a stock name..." required minlength="1">
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-user-secret"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">指数查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="index_query_url" placeholder="Pick a stock name..." required>
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">分时线图查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="min_chart_url" placeholder="Pick a stock name..." required>
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div>


	           <div class="field">
	             <label class="label">属于市场（不要轻易修改）</label>
	               <div class="control has-icons-left">             
                  		<div class="select">
	                  		<select name="belongsToMarket">
	                  			@foreach($market as $mid => $mdata)		                  				                  				
	                  				<option value="{{$mid}}">{{$mdata['name']}}</option>		                  				
	                  			@endforeach
	                  		</select>
	                  	</div>
                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
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
        	
        </div>
      </section>

@stop


@section('scripts')
<script>
    $('#edit-form').parsley();
  
</script>    
@stop