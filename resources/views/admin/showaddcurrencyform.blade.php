@extends('admin')
@section('main')
      <section class="section">
        <div class="container">    
        	 <h2 class="title">货币添加</h2>
        	
        	 <form id="add-form" action="{{route('addcurrency')}}" method="post" enctype="multipart/form-data"> 
	           <div class="field">
	             <label class="label">货币名</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="name" placeholder="Pick a stock name..." required minlength="1">
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-money"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label danger-label">货币代码（重要数据）</label>
	               <div class="control has-icons-left">	                  
	                      <input class="input" type="text" name ="currency_index" placeholder="Pick a stock name..." required minlength="2">
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-user-secret"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">汇率查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="rate_query_url" placeholder="Pick a stock name..." required>
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div> 
       
	            <hr>
	           <div class="field is-grouped">
	              <div class="control">
	                <input type="submit" class="button is-primary" value="添加"></input>
	                <input type="hidden" name="_token" value="{{ Session::token() }}">
	              </div>
	            </div>

        	 </form>
        	 
        </div>
      </section>

@stop

@section('scripts')
<script>
	$('#add-form').parsley();
</script>    
@stop