<html>
    @include('partials.header')
  <body>
    @include('partials.banner')
    <div class="container" id="nav-container">
       @include('partials.admin-navbar')    
    </div>
    @include('partials.message')
    @yield('main')

    @include('partials.footer') 
    @include('partials.scripts')

    @yield('scripts') 
  </body>
</html>