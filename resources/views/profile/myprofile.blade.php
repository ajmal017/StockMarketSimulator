@extends('main')
@section('main')

<section class="section">
	<div class="container" id="profile-container">
		<h2 class="title">{{Session::get('username')}}的个人资料</h2>

		<div class="image section">
			@if(!isset($LargeAvator) || !$LargeAvator)
				<div class="avatar-large" id="current-avator" style="background-image: url(http://bulma.io/images/placeholders/256x256.png)"></div>	
			@else
				<div class="avatar-large" id="current-avator" style="background-image: url({{'/avator/' . $LargeAvator->belongsTo . '/' . base64_encode($LargeAvator->filename)}})"></div>	
			@endif
		</div>

		<div class="file has-name is-primary">
		  	
			  <div class="columns is-mobile">
			  	<form id="upload-form" method="POST" enctype="multipart/form-data"> 
			  	  <div class="column">
					  <label class="file-label">
					    <input class="file-input" type="file" name="avator" id="avator">
					    <span class="file-cta">
					      <span class="file-icon">
					        <i class="fa fa-upload"></i>
					      </span>
					      <span class="file-label">
					        选择文件...
					      </span>
					    </span>
					    <span class="file-name">
					     not selected yet
					    </span>
					  </label>
				  </div>
				</form>
				  <div class="column" id="upload-button">
					  <div class="field is-grouped">
		              	<div class="control">				 	 		
				 	 		<button type="button" class="button is-success" id="upload">上传</button>
				 	 		<!--<input type="hidden" name="_token" id="_token" value="{{ Session::token() }}">-->
				 	 		<!--<input type="submit" class="button is-success" name = "upload" id="upload" value="上传">-->
				 	  	</div>
				 	  </div>
			 	 </div>
			 	 
			  </div>
			
		</div>
	</div>	
</section>
@stop

<div id="confirm-dialog" title="错误" class="inactivo">
<p>
</p>
</div>

<!--<meta name="csrf-token" content="{{ csrf_token() }}">-->
<meta name="_token" content="{{ Session::token() }}">

@section('scripts')
 <!-- <script src="{{asset('js/market.js')}}"></script>  -->
<!--<script src="https://cdn.bootcss.com/jquery.form/4.2.2/jquery.form.min.js"></script>-->
<script src="{{asset('js/profile.js')}}"></script>  
@stop