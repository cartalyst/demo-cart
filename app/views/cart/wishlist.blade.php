@extends('cart.layouts.default')

@section('page')
<table class="table table-bordered">
	<thead>
		<tr>
			<td class="col-md-7">Name</td>
			<td class="col-md-1">Price</td>
			<td class="col-md-2" colspan="2">Total</td>
		</tr>
	</thead>
	<tbody>
		@if ($items->isEmpty())
		<tr>
			<td colspan="3">Your wishlist is empty.</td>
		</tr>
		@else
		@foreach ($items as $item)
		<tr>
			<td>
				<div class="col-md-2">
					<img src="http://placehold.it/80x80" alt="..." class="img-thumbnail">
				</div>
				{{{ $item->get('name') }}}
			</td>
			<td>{{{ Converter::value($item->get('price'))->from('currency.eur')->to('currency.usd')->format() }}}</td>
			<td>
				{{{ Converter::value($item->subTotal())->from('currency.eur')->to('currency.usd')->format() }}}
			</td>
			<td>
				<a class="btn btn-danger btn-xs" href="{{ URL::to("wishlist/{$item->get('id')}/remove") }}">Delete</a>
			</td>
		</tr>
		@endforeach
		@endif
	</tbody>
</table>

@if ( ! $items->isEmpty())
<a href="{{ URL::to('wishlist/destroy') }}" class="btn btn-danger">Empty Wishlist</a>
@endif

<br>
@stop
