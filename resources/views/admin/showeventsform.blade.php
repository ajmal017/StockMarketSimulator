@extends('admin')
@section('main')
  <section class="section">
    <div class="container">        
      <div class="tile is-ancestor">
			<div class="tile is-parent is-12 has-text-centered">
				<article class="tile is-child">
					<p class="title is-4">事件列表</p>
					
					<nav class="breadcrumb is-centered" aria-label="事件查询过滤" id="event-select">
						<ul>
                          <li class="is-active"><a aria-current="page" id="show-all">全部事件</a></li>
                          <li><a id="show-trade">交易事件</a></li>
                          <li><a id="show-update">玩家更新</a></li>
                          <li><a id="show-admin">后台管理</a></li>							
						</ul>	
					</nav>
					<table class="table is-bordered is-striped" width="100%">
						<thead>
							<tr>
								<th width="10%">
									<abbr title="删除">
										<label class="checkbox">
											<input type="checkbox" id="select-all">
											全选
										</label>
									</abbr>
								</th>
								<th width="10%">
									<abbr title="事件ID">ID</abbr>
								</th>
								<th width="10%"><abbr title="事件类型">类型</abbr></th>
								<th width="50%"><abbr title="事件内容">内容</abbr></th>
								<th width="20%">
									<abbr title="发生时间">										
										<div class="select">
										  <select id="orderby">
										    <option value="desc" selected>从近到远</option>
										    <option value="asc">从远到近</option>
										  </select>
										</div>											
									</abbr>
								</th>			      					      
							</tr>
						</thead>

						<tbody id="events-body">
							@if(!empty($events))
							<form id="event-form" method="POST" enctype="multipart/form-data">
								@foreach($events as $event)
									<tr id="{{'tr_' . $event->eid}}">
										<td width="10%">
											<input type="checkbox" id="{{'delete-' . $event->eid}}">
										</td>
										<td width="10%">{{$event->eid}}</td>
										<td width="10%">{{$typemap[$event->type]}}</td>
										<td width="50%">{!!stripcslashes($event->event)!!}</td>
										<td width="20%">{{$event->updated_at}}</td>
									</tr>
								@endforeach							
							</form>	
							@endif

						</tbody>

					</table>
					<div id="links">
						{{ $events->links('partials.pagination') }}
					</div>
					
	          	 @if(!empty($events))
		              <div class="control">
		                <a class="button is-danger" id="del">删除</a>	                
		              </div>
	              @endif
	            </article>

			</div>

	   </div>
	</div>
</section>
@stop

<div id="confirm-dialog" title="提示" class="inactivo">
<p>
</p>
</div>

<meta name="_token" content="{{ Session::token() }}">

@section('scripts')
<script src="{{asset('js/adminevent.js')}}"></script>
@stop