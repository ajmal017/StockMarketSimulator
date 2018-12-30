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
        回到首页
      </a>

      <a class="navbar-item " href="/admin">
        全局参数
      </a>      

      <div class="navbar-item has-dropdown is-hoverable">
        <a class="{{Request::is('admin/showmarkets') ? "navbar-link is-active" : "navbar-link"}}" href="/admin/showmarkets">
          市场管理
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="{{Request::is('admin/showmarkets') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/showmarkets">
            编辑市场
          </a>
          <a class="{{Request::is('admin/addmarketform') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/addmarketform">
            增加市场
          </a>
         </div>
      </div>

      <div class="navbar-item has-dropdown is-hoverable">
        <a class="{{Request::is('admin/showindice') ? "navbar-link is-active" : "navbar-link"}}" href="/admin/showindice">
          指数管理
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="{{Request::is('admin/showindice') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/showindice">
            编辑指数
          </a>
          <a class="{{Request::is('admin/addindexform') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/addindexform">
            增加指数
          </a>
         </div>
      </div>

      <div class="navbar-item has-dropdown is-hoverable">
        <a class="{{Request::is('admin/showcurrency') ? "navbar-link is-active" : "navbar-link"}}" href="/admin/showcurrency">
          货币管理
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="{{Request::is('admin/showcurrency') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/showcurrency">
            编辑货币
          </a>
          <a class="{{Request::is('admin/addcurrencyform') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/addcurrencyform">
            增加货币
          </a>
         </div>
      </div>

      <div class="navbar-item has-dropdown is-hoverable">
        <a class="{{Request::is('admin/showinvestors') ? "navbar-link is-active" : "navbar-link"}}" href="/admin/showinvestors">
          投资者管理
        </a>
        <div class="navbar-dropdown is-boxed">
          <a class="{{Request::is('admin/showinvestors') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/showinvestors">
            编辑投资者
          </a>
          <a class="{{Request::is('admin/addinvestorform') ? "navbar-item is-active" : "navbar-item"}}" href="/admin/addinvestorform">
            增加投资者
          </a>
         </div>
      </div>

      <a class="navbar-item " href="/admin/manageevents">
        事件/日志管理
      </a>  
      
      <a class="navbar-item " href="/admin/update_admin_cache">
        更新缓存
      </a>  

    </div>

    <div class="navbar-end">
      <div class="navbar-item">
        @if(isset($avator))
          <a href="/myprofile"><div class="avatar-small" id="current-avator" style="background-image: url({{'/avator/' . $avator->belongsTo . '/' . base64_encode($avator->filename) . '/small'}})"></div></a>    
        @endif      
        <span>你好, {{Session::get('username')}}(管理员)
      </div>
      <a class="navbar-item " href="/logout">
        退出
      </a>        
    </div>
</nav>