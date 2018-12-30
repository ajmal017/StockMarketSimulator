@extends('admin')
@section('main')
<div class="section">
	<div class="container has-text-centered"> 
		<article>
			<p class="title is-4"><span name="owner" id="{{'owner-' . $owner->iid}}">{{$owner->name}}</span>的股票</p>
			<hr>
			<table class="table is-bordered is-striped is-narrow is-fullwidth" width="100%">
				<thead>
					<tr>
						<th><abbr title="股票ID">股票ID</abbr></th>
						<th><abbr title="股票名称">股票名称</abbr></th>
						<th><abbr title="玩家买入价">买入价</abbr></th>
						<th><abbr title="玩家将卖出的价格，默认是买入价">卖出价</abbr></th>
						<th><abbr title="玩家拥有股票数量,如果强卖，请输入小于原有量的数字">拥股量</abbr></th>
						<th><abbr title="玩家最近购买此股票时间, 显示为当地时间">购买时间</abbr></th>
						<th><abbr title="玩家最近卖出此股票时间, 显示为当地时间">卖出时间</abbr></th>
						<th></th>				      					      
					</tr>
				</thead>				

				<tbody id="share-info">
					@foreach($shares as $share)
						<tr id="{{'tr_' . $share->sid}}">
							<form method="POST" enctype="multipart/form-data">
								<td id="{{'sid-' . $share->sid}}">{{$share->sid}}</td>
								<td id="{{'sharename-' . $share->sid}}">{{$share->name}}</td>
								<td>
									<div class="field">
										<div class="control has-icons-left">
										<input class="input" type="text" name ="buying_price" id="{{'buyingprice-' . $share->sid}}" value="{{$share->pivot->buying_price}}" required Integer min="1">
							                <span class="icon is-small is-left">
							                	  <i class="fa fa-money"></i> 
							                </span>  											
										</div>	
									</div>
								</td>

								<td>
									<div class="field">
										<div class="control has-icons-left">
										<input class="input" type="text" name ="selling_price" id="{{'sellingprice-' . $share->sid}}" value="{{$share->pivot->selling_price}}" required Integer min="1">
							                <span class="icon is-small is-left">
							                	  <i class="fa fa-money"></i> 
							                </span>  											
										</div>	
									</div>
								</td>

								<td>
									<div class="field">
										<div class="control has-icons-left">
										<input class="input" type="text" name ="amount" id="{{'amount-' . $share->sid}}" value="{{$share->pivot->amount}}" required Integer min="1">
							                <span class="icon is-small is-left">
							                	  <i class="fa fa-line-chart"></i> 
							                </span>  											
										</div>	
									</div>
								</td>

								<td id="{{'buyingtime-' . $share->sid}}">{{$share->limit_buying_at}}</td>
								<td id="{{'sellingtime-' . $share->sid}}">{{$share->limit_selling_at}}</td>

								<td>									
									<a class="button is-small is-success" name="edit" id="{{'edit-' . $share->sid}}">
										<span class="icon is-small">
									      <i class="fa fa-pencil"></i>
									    </span>
									    <span>编辑</span>
									</a>
																		
									<a class="button is-small is-danger" name="forcesell" id="{{'forcesell-' . $share->sid}}">
										<span class="icon is-small">
									      <i class="fa fa-ban"></i>
									    </span>
									    <span>强卖</span>
									</a>								
									
								</td>


							</form>
						</tr>
					@endforeach
				</tbody>
			</table>
		</article>
	</div>
</div>
@stop


<div id="confirm-dialog" title="提示" class="inactivo">
<p>
</p>
</div>

<meta name="_token" content="{{ Session::token() }}">

@section('scripts')
<script>
    //$('form').parsley();

</script>   
<script src="{{asset('js/shares.js')}}"></script>
@stop