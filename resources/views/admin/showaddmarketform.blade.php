@extends('admin')
@section('main')
      <section class="section">
        <div class="container">    
        	 <h2 class="title">添加市场</h2>
        	 
        	 <form id="add-form" action="{{route('addmarket')}}" method="post" enctype="multipart/form-data"> 
	           <div class="field">
	             <label class="label">股市名</label>
	               <div class="control has-icons-left">
	                    <input class="input" type="text" name ="name" placeholder="Pick a stock name..." required minlength="1">
	                    <span class="icon is-small is-left">
	                    <i class="fa fa-line-chart"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label danger-label">股市代码（重要数据,请查阅清楚）</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="market_index" placeholder="Pick a stock name..." required minlength="2">
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-user-secret"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">股票查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">

	                      <input class="input" type="text" name ="query_url_head" placeholder="Pick a stock name..." required>
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div> 

	           <div class="field">
	             <label class="label">涨幅排行榜查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">
	                      <input class="input" type="text" name ="rank_up_url" placeholder="Pick a stock name..." required>
	                 
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div>

	           <div class="field">
	             <label class="label">跌幅排行榜查询网址（不要轻易修改）</label>
	               <div class="control has-icons-left">

	                      <input class="input" type="text" name ="rank_down_url" placeholder="Pick a stock name..." required>
	                  
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-address-book"></i> 
	                  </span>               
	               </div>
	            </div>

	           <div class="field">
	               <label class="label">使用货币</label>               
	               <div class="control has-icons-left">
	                  <div class="select">
	                  <select  name="allowed_currency">
                       @foreach($currency as $cid => $cdata)
                          <option value="{{$cid}}">{{$cdata['name']}}</option>
                       @endforeach 
	                 
	                  </select>
	                  </div>
	                  <span class="icon is-small is-left">
	                    <i class="fa fa-line-chart"></i>
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
