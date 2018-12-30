@extends('admin')
@section('main')
      <section class="section">
        <div class="container">            
        <h2 class="title">

        @if(!empty($config)) 
        全局参数修改
        @else
        添加全局参数
        @endif
       </h2>        

       <!--
        @if(empty($config)) 
         <form id="config-form" action="/globalconfig" method="post" enctype="multipart/form-data"> 
        @else
          <form id="config-form" action="/globalconfig" method="put" enctype="multipart/form-data"> 
        @endif
        -->
        <form id="config-form" action="/globalconfig" method="post" enctype="multipart/form-data"> 
            <div class="field">
             <label class="label">网站名</label>
               <div class="control has-icons-left">
                  @if(isset($config['webname']))
                     <input class="input" type="text" name ="webname" placeholder="Pick a web name..." value="{{$config['webname']}}" required minlength="3">
                  @else
                      <input class="input" type="text" name ="webname" placeholder="Pick a web name..." required minlength="3">
                  @endif
                  <span class="icon is-small is-left">
                    <i class="fa fa-address-book"></i> 
                  </span>               
               </div>
            </div>
            
            <div class="field">
             <label class="label">网址(根网址,没有http://)</label>
               <div class="control has-icons-left">
               @if(isset($config['weburl']))
                  <input class="input" type="text" name ="weburl" placeholder="Pick a web name..." value="{{$config['weburl']}}" required>
                @else
                  <input class="input" type="text" name ="weburl" placeholder="Pick a web name..." required>
                @endif
                  <span class="icon is-small is-left">
                    <i class="fa fa-address-book"></i> 
                  </span>               
               </div>
            </div>

            <div class="field">
             <label class="label">玩家初始资金</label>
               <div class="control has-icons-left">
                @if(isset($config['init_credits']))
                  <input class="input" type="text" name ="init_credits" placeholder="Put a postive number..." value="{{$config['init_credits']}}" required Integer min="10000">
                @else
                  <input class="input" type="text" name ="init_credits" placeholder="Put a postive number..." required Integer min="10000">
                @endif
                  <span class="icon is-small is-left">
                    <i class="fa fa-money"></i>
                  </span>                  
               </div>
            </div>

          
          @if(isset($market)) 
           <div class="field">
               <label class="label">默认股市</label>
               <div class="control has-icons-left">
                  <div class="select">
                  <select name="default_stock_market">
                   @if(isset($config['default_stock_market']))
                      @foreach($market as $mid => $mdata)
                        @if($config['default_stock_market'] == $mdata['market_index'])
                          <option value="{{$mid}}" selected>{{$mdata['name']}}</option>
                        @else
                          <option value="{{$mid}}">{{$mdata['name']}}</option>
                        @endif
                       @endforeach
                  @else
                        @foreach($market as $mid => $mdata)
                          <option value="{{$mid}}">{{$mdata['name']}}</option>
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

          @if(isset($index))
           <div class="field">
               <label class="label">默认指数</label>               
               <div class="control has-icons-left">
                  <div class="select">
                  <select  name="default_index">
                  @if(isset($config['default_index']))
                    @foreach($index as $iid => $idata)
                         @if($config['default_index'] == $idata['index_index'])
                            <option value="{{$iid}}" selected>{{$idata['name']}}</option>
                         @else
                            <option value="{{$iid}}">{{$idata['name']}}</option>
                         @endif
                    @endforeach      
                  @else
                       @foreach($index as $iid => $idata)
                          <option value="{{$iid}}">{{$idata['name']}}</option>
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

          @if(isset($currency))
           <div class="field">
               <label class="label danger-label">默认初始货币（一旦设置不要更改）</label>               
               <div class="control has-icons-left">
                  <div class="select">
                  <select  name="default_init_currency">
                  @if(isset($config['default_init_currency']))
                    @foreach($currency as $cid => $cdata)
                         @if($config['default_init_currency'] == $cdata['currency_index'])
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
                    <i class="fa fa-money"></i>
                  </span>      
               </div> 
            </div>
            @endif            

            <div class="field">
             <label class="label">玩家同时投资最大个股数量</label>
               <div class="control has-icons-left">
                @if(isset($config['max_shares_no']))
                  <input class="input" type="text" name ="max_shares_no" placeholder="Put a postive number..." value="{{$config['max_shares_no']}}" required Integer min="1">
                @else
                  <input class="input" type="text" name ="max_shares_no" placeholder="Put a postive number..." required Integer min="1">
                @endif
                  <span class="icon is-small is-left">
                    <i class="fa fa-line-chart"></i>
                  </span>                  
               </div>
            </div>

           <div class="field">
             <label class="label">活跃指数缓存时间(分钟)</label>
               <div class="control has-icons-left">
                 @if(isset($config['shares_rank_expiration']))
                  <input class="input" type="text" name ="shares_rank_expiration" placeholder="Put a postive number..." value="{{$config['shares_rank_expiration']}}" required Integer min="1">
                 @else
                 <input class="input" type="text" name ="shares_rank_expiration" placeholder="Put a postive number..."  required Integer min="1"> 
                 @endif                 
                  <span class="icon is-small is-left">
                    <i class="fa fa-clock-o"></i>
                  </span>                  
               </div>
            </div>
           
           <div class="field">
             <label class="label">头条新闻缓存时间(分钟)</label>
               <div class="control has-icons-left">
                 @if(isset($config['headline_expiration']))
                  <input class="input" type="text" name ="headline_expiration" placeholder="Put a postive number..." value="{{$config['headline_expiration']}}" required Integer min="1">
               @else
                  <input class="input" type="text" name ="headline_expiration" placeholder="Put a postive number..." required Integer min="1">
               @endif
                  <span class="icon is-small is-left">
                    <i class="fa fa-clock-o"></i>
                  </span>                  
               </div>
            </div>
             
            <hr>
           <div class="field is-grouped">
              <div class="control">
                <input type="submit" class="button is-primary" value="提交"></input>
                <input type="hidden" name="_token" value="{{ Session::token() }}">
              </div>
            </div>  

         </form> 
        </div>
      </section>
@stop

@section('scripts')
<script>
    $('#config-form').parsley();
</script>    
@stop