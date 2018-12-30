@extends('admin')
@section('main')

<div class="section">
	<div class="container has-text-centered"> 
		<article>
			<p class="title is-4">玩家列表</p>
			<hr>
			<table class="table is-bordered is-striped is-narrow is-fullwidth" width="100%">
				<thead>
					<tr>
						<th><abbr title="玩家ID">玩家ID</abbr></th>
						<th><abbr title="除股市投资外的资产">玩家资产</abbr></th>
						<th><abbr title="玩家绑定货币">玩家货币</abbr></th>
						<th><abbr title="玩家所持股票">玩家股票</abbr></th>
						<th><abbr title="玩家等级">玩家等级</abbr></th>
						<th><abbr title="玩家等级">玩家资讯</abbr></th>
						<th></th>				      					      
					</tr>
				</thead>				

				<tbody>
					@foreach($investors as $investor)
						<tr id="{{'tr_' . $investor->iid}}">
							<form method="POST" enctype="multipart/form-data">
								<td id="{{'username-' . $investor->iid}}">{{$investor->name}}</td>
								<td>
									<div class="field">
										<div class="control has-icons-left">
										<input class="input" type="text" name ="coins" id="{{'coins-' . $investor->iid}}" value="{{$investor->coins}}" required Integer min="1">
							                <span class="icon is-small is-left">
							                	  <i class="fa fa-money"></i> 
							                </span>  											
										</div>	
									</div>
								</td>

								<td>
						           <div class="field">						                       
						               <div class="control has-icons-left">
						                  <div class="select">
						                  <select name="bind_to" id="{{'bind_to-' . $investor->iid}}">						                  
						                    @foreach($currency as $cid => $cdata)
						                         @if($investor->bind_to == $cdata['currency_index'])
						                            <option value="{{$cid}}" selected>{{$cdata['name']}}</option>
						                         @else
						                            <option value="{{$cid}}">{{$cdata['name']}}</option>
						                         @endif
						                    @endforeach      
						                  </select>
						                  </div>
						                  <span class="icon is-small is-left">
						                    <i class="fa fa-usd"></i>
						                  </span>      
						               </div> 
						            </div>
								</td>

								<td>
									@if(isset($shares[$investor->iid]) && !empty($shares[$investor->iid]))
										<a href="{{'/admin/showshares/' . $investor->iid}}" class="button is-small is-primary">
									@else
										<a href="{{'/admin/showshares/' . $investor->iid}}" class="button is-small is-primary" disabled>
									@endif
										<span class="icon is-small">
									      <i class="fa fa-line-chart"></i>
									    </span>
									    <span>股票</span>
									</a>
								</td>

								<td>
									<div class="field">
										<div class="control has-icons-left">
											<input class="input" type="text" name ="level" id="{{'level-' . $investor->iid}}" value="{{$investor->level}}" required Integer min="1">
							                <span class="icon is-small is-left">
							                	  <i class="fa fa-star"></i> 
							                </span>  											
										</div>	
									</div>									
								</td>

								<td>
									<a href="{{'/admin/showprofile/' . $investor->iid}}" class="button is-small is-warning">
										<span class="icon is-small">
									      <i class="fa fa-info"></i>
									    </span>
									    <span>资讯</span>
									</a>
								</td>

								<td>									
									<a class="button is-small is-success" name="edit" id="{{'edit-' . $investor->iid}}">
										<span class="icon is-small">
									      <i class="fa fa-pencil"></i>
									    </span>
									    <span>编辑</span>
									</a>
																		
									<a class="button is-small is-danger" name="ban" id="{{'ban-' . $investor->iid}}">
										<span class="icon is-small">
									      <i class="fa fa-ban"></i>
									    </span>
									    <span>禁止</span>
									</a>								
									
								</td>


							</form>
						</tr>
					@endforeach
				</tbody>
			</table>
			{{$investors->links('partials.pagination')}}
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
    $('form').parsley();

</script>   
<script src="{{asset('js/investors.js')}}"></script>
@stop