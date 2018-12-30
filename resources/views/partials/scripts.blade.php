<!--
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="{{asset('js/jquery-ui.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/parsleyjs/2.7.1/parsley.min.js"></script>
<script src="{{asset('js/tools.js')}}"></script>
<script src="{{asset('js/jjsonviewer.js')}}"></script>
<script>

function get_config(){
	//return $.getJSON("http://stock.yucheung.com/getAllConfig");
	return  $.getJSON("/getAllConfig");
}

function get_myinfo(){
	return $.getJSON("/getMyInfo");
}

function get_queryByPlatform(){
	return $.getJSON("/getQueryHeadByPlatform");
}

$(function(){
	$("#msg-delete").on('click', function(){
		$("#msg-div").remove();
	});

	$("#jjson").hide();		
});
</script>

