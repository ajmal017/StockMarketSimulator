@if(Session::has('success'))
<div class="container is-medium" id="msg-div">
<article class="message is-success">
	<div class="message-header">
	信息
      <button class="delete" id="msg-delete"></button>
    </div>
    <div class="message-body">
   		{{ Session::get('success') }}
   	</div>
</article>
</div>
@elseif(Session::has('failure'))
<div class="container is-medium" id="msg-div">
<article class="message is-danger">
  	<div class="message-header">
  	  警告
      <button class="delete" id="msg-delete"></button>
    </div>
    <div class="message-body">
   		{{ Session::get('failure') }}
   	</div>
</article>
</div>
@endif