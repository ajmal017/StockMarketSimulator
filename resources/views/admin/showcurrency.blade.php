@extends('admin')
@section('main')
  <section class="section">
    <div class="container">        
      <div class="tile is-ancestor">
			<div class="tile is-parent is-8 has-text-centered">
				<article class="tile is-child">
					<p class="title is-4">货币列表</p>
					<hr>
					<table class="table is-bordered is-striped is-narrow is-fullwidth" width="100%">
						<thead>
							<tr>
								<th><abbr title="货币代码">市场代码</abbr></th>
								<th><abbr title="货币名称">市场名称</abbr></th>
								<th></th>				      					      
							</tr>
						</thead>

						<tbody id="currency-info">
							@if(!empty($currency))
								@foreach($currency as $cid => $cdata)
									<tr id="{{'tr_' . $cid}}">
										<td>{{$cid}}</td>
										<td>{{$cdata['name']}}</td>
										<td class='has-text-right'><a class="button is-info is-small" href="{{route('admin_show_edit_currency_form', ['currency_index' => $cid])}}">编辑</a>
									</tr>
								@endforeach							
							@endif
						</tbody>

					</table>
					{{ $currency->links('partials.pagination') }}					
				</article>

			</div>

	   </div>
	</div>
</section>
@stop