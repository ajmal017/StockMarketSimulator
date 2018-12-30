@extends('admin')
@section('main')
  <section class="section">
    <div class="container">        
      <div class="tile is-ancestor">
			<div class="tile is-parent is-8 has-text-centered">
				<article class="tile is-child">
					<p class="title is-4">市场列表</p>
					<hr>
					<table class="table is-bordered is-striped is-narrow is-fullwidth" width="100%">
						<thead>
							<tr>
								<th><abbr title="市场代码">市场代码</abbr></th>
								<th><abbr title="市场名称">市场名称</abbr></th>
								<th></th>				      					      
							</tr>
						</thead>

						<tbody id="market-info">
							@if(!empty($market))
								@foreach($market as $mid => $mdata)
									<tr id="{{'tr_' . $mid}}">
										<td>{{$mid}}</td>
										<td>{{$mdata['name']}}</td>
										<td class='has-text-right'><a class="button is-info is-small" href="{{route('admin_show_edit_market_form', ['market_index' => $mid])}}">编辑</a>
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
