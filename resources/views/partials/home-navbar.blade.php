<nav class="navbar is-transparent">
  <div class="navbar-brand">

    <div class="navbar-burger burger" data-target="home-navbar">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <div id="home-navbar" class="navbar-menu">
    <div class="navbar-start">

      <a class="navbar-item " href="/">
        首页
      </a>

      <div class="navbar-item has-dropdown is-hoverable">
      @if(empty($market))
        <a class="{{Request::is('showmarket') ? "navbar-link is-active" : "navbar-link"}}" href="/showmarket">
          股市资讯
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="{{Request::is('showmarket/sh') ? "navbar-item is-active" : "navbar-item"}}" href="/showmarket/sh">
            上证资讯
          </a>
          <a class="{{Request::is('showmarket/sz') ? "navbar-item is-active" : "navbar-item"}}" href="/showmarket/sz">
            深证资讯
          </a>
          <a class="{{Request::is('showmarket/ny') ? "navbar-item is-active" : "navbar-item"}}" href="/showmarket/ny">
            NYSE资讯(开发中)
          </a>
          <a class="{{Request::is('showmarket/nas') ? "navbar-item is-active" : "navbar-item"}}" href="/showmarket/nas">
            NASDAQ资讯(开发中)
          </a>
        </div>
        @else
          <a class="{{Request::is('showmarket') ? "navbar-link is-active" : "navbar-link"}}" href="/showmarket">
          股市资讯
          </a>
          <div class="navbar-dropdown is-boxed">
          @foreach($market as $key => $mdata)
            @if($mdata['status'])
              <a class="{{Request::is('showmarket/' . $key) ? "navbar-item is-active" : "navbar-item"}}" href="{{'/showmarket/' . $key}}">
                {{$mdata['name']}}                
                  @if(in_array($key, $open_markets))
                    <span class="icon is-small has-text-success">
                      <i class="fa fa-check"></i>
                    </span>
                  @else
                    <span class="icon is-small has-text-danger">
                      <i class="fa fa-times"></i>
                   </span>                    
                  @endif
              </a>
            @endif            
          @endforeach
          </div>
        @endif
      </div>
      

      @if(Auth::check())
      <div class="navbar-item has-dropdown is-hoverable">
      @if(empty($market))
        <a class="navbar-link  is-active" href="#">
          股票投资
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="navbar-item is-active" href="#">
            上海A股
          </a>
          <a class="navbar-item " href="#">
            深圳A股
          </a>
          <a class="navbar-item " href="#">
            Dow Jone
          </a>
          <a class="navbar-item " href="#">
            NASDAQ
          </a>
        </div>
      @else
        <a class="{{Request::is('showtrademarket') ? "navbar-link is-active" : "navbar-link"}}" href="/showtrademarket">
        股票投资
        </a>
          <div class="navbar-dropdown is-boxed">      
        @foreach($market as $key => $mdata)
           @if($mdata['status'])
              <a class="{{Request::is('showtrademarket/' . $key) ? "navbar-item is-active" : "navbar-item"}}" href="{{'/showtrademarket/' . $key}}">
                {{$mdata['name']}}
                  @if(in_array($key, $open_markets))
                    <span class="icon is-small has-text-success">
                      <i class="fa fa-check"></i>
                    </span>
                  @else
                    <span class="icon is-small has-text-danger">
                      <i class="fa fa-times"></i>
                   </span>                    
                  @endif                
              </a>
            @endif           
        @endforeach
        </div>
      @endif
      </div>
      
      <a class="navbar-item " href="/myaccount">
        玩家账户
      </a>

     <a class="navbar-item " href="/myprofile">
        玩家资讯
      </a>
      @endif      
    </div>

    <div class="navbar-end">
    @if(!Auth::check())
      <a class="navbar-item " href="/login">
        登录
      </a>      
     <a class="navbar-item " href="/register">
        注册
      </a>        
    @else
      <div class="navbar-item">
        @if(isset($avator))
          <a href="/myprofile"><div class="avatar-small" id="current-avator" style="background-image: url({{'/avator/' . $avator->belongsTo . '/' . base64_encode($avator->filename) . '/small'}})"></div></a>    
        @endif
        <span>你好, {{Session::get('username')}}
      </div>
      <a class="navbar-item " href="/logout">
        退出
      </a> 
      <a class="navbar-item" href="/admin">管理后台</a>       
    @endif
  </div>
</nav>

  <div id="jjson" class="jjson">
      
  </div>
  