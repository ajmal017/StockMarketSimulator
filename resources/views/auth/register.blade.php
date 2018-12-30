@extends('main')
@section('main')
        <section class="section">
            <div class="container">
                <h2 class="title">注册会员</h2> 
                <form id="reg-form" action="/register" method="post" enctype="multipart/form-data">   
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
                        <input class="input" type="text" name ="password" id="password" placeholder="Your password..." required minlength="5" maxlength="10">
                        <span class="icon is-small is-left">
                            <i class="fa fa-key"></i>
                        </span>
                      </div>
                    </div>

                   <div class="field">
                      <label class="label has-icons-left">重新输入密码</label>
                      <div class="control has-icons-left">
                        <input class="input" type="text" name ="confirmed" placeholder="Retype your password" data-parsley-equalto="#password">
                       <span class="icon is-small is-left">
                            <i class="fa fa-key"></i>
                        </span>                        
                      </div>
                    </div>

                    <div class="field">
                      <label class="label">Email</label>
                      <div class="control has-icons-left">
                        <input class="input" type="text" name ="email" placeholder="Email input" value="hello@" data-parsley-type="email">
                        <span class="icon is-small is-left">
                          <i class="fa fa-envelope"></i>
                        </span>
                      </div>
                    </div>

                    <div class="field is-grouped">
                      <div class="control">
                        <input type="submit" class="button is-primary" value="注册"></input>
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                      </div>
                    </div> 
                </form> 
            </div>              
        </section>  
@endsection

@section('scripts')
<script>
    $('#reg-form').parsley();
</script>    
@stop
