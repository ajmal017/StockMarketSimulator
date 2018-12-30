@extends('admin')
@section('main')
  <section class="section">
    <div class="container">        
      <div class="tile is-ancestor">
			<div class="tile is-parent is-8 has-text-centered">
				<article class="tile is-child">
					<p class="title is-4">指数列表</p>
					<hr>
					<table class="table is-bordered is-striped is-narrow is-fullwidth" width="100%">
						<thead>
							<tr>
								<th><abbr title="市场代码">指数代码</abbr></th>
								<th><abbr title="市场名称">指数名称</abbr></th>
								<th></th>				      					      
							</tr>
						</thead>

						<tbody id="index-info">
							@if(!empty($index))
								@foreach($index as $iid => $idata)
									<tr id="{{'tr_' . $iid}}">
										<td>{{$iid}}</td>
										<td>{{$idata['name']}}</td>
										<td class='has-text-right'><a class="button is-info is-small" href="{{route('admin_show_edit_index_form', ['index_index' => $iid])}}">编辑</a>
									</tr>
								@endforeach							
							@endif
						</tbody>

					</table>					
				</article>
			</div>
	   </div>
	</div>
</section>
@stop
