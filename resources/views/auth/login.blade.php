@extends('main')
@section('main')
        <section class="section">
            <div class="container is-medium">
                <h2 class="title">登录</h2> 
                <form id="login-form" action="/login" method="post" enctype="multipart/form-data">   
                    <div class="field">
                      <label class="label">用户ID</label>
                      <div class="control has-icons-left">
                        <input class="input" type="text" name ="name" placeholder="Your ID..." required minlength="6">
                        <span class="icon is-small is-left">
                          <i class="fa fa-user"></i>
                        </span>                          
                      </div>
                    </div>

                    <div class="field">
                      <label class="label">密码</label>
                      <div class="control has-icons-left">
                        <input class="input" type="password" name ="password" id="password" placeholder="Your password..." required minlength="5" maxlength="10">
                        <span class="icon is-small is-left">
                            <i class="fa fa-key"></i>
                        </span>
                      </div>
                    </div>
 

                    <div class="field is-grouped">
                      <div class="control">
                        <input type="submit" class="button is-primary" value="登录"></input>
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                      </div>
                    </div> 
                </form> 
            </div>              
        </section>  
@endsection

@section('scripts')
<script>
    $('#login-form').parsley();
</script>    
@stop
