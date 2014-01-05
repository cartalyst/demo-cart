@extends('cart.layouts.default')

@section('page')
<form role="form" action="" method="post">
<table class="table table-bordered">
	<thead>
		<tr>
			<td class="col-md-6">Name</td>
			<td class="col-md-1">Quantity</td>
			<td class="col-md-1">Price</td>
			<td class="col-md-2" colspan="2">Total</td>
		</tr>
	</thead>
	<tbody>
		@if ($items->isEmpty())
		<tr>
			<td colspan="4">Your shopping cart is empty.</td>
		</tr>
		@else
		@foreach ($items as $item)
		<tr>
			<td>
				<div class="col-md-2">
					<img src="http://placehold.it/80x80" alt="..." class="img-thumbnail">
				</div>
				{{{ $item->get('name') }}}

				@if ( ! $item->get('attributes')->isEmpty())

				<br>
				@foreach ($item->attributes as $option)
				{{{ $option->get('label') }}}: {{{ $option->get('value') }}}

				@if ($option->has('price'))
				<span class="pull-right">
					{{{ $option->get('price') > 0 ? '+' : '-' }}}
					{{{-- Currency::value($option->get('price'))->to('usd')->format() --}}}
				</span>
				@endif

				<br>
				@endforeach

				@endif
				<br>
			</td>
			<td>
				<input class="form-control" type="text" name="update[{{{ $item->get('rowId') }}}][quantity]" value="{{{ $item->get('quantity') }}}" />
			</td>
			<td>{{{ Converter::value($item->get('price'))->from('currency.eur')->to('currency.usd')->format() }}}</td>
			<td>
				{{{ Converter::value($item->subTotal())->from('currency.eur')->to('currency.usd')->format() }}}
			</td>
			<td>
				<a class="btn btn-danger btn-xs" href="{{ URL::to("cart/{$item->get('rowId')}/remove") }}">Delete</a>
			</td>
		</tr>
		@endforeach
		<tr>
			<td colspan="4">
				<span class="pull-right">Items</span>
			</td>
			<td colspan="2">{{{ Cart::quantity() }}}</td>
		</tr>
		<tr>
			<td colspan="4">
				<span class="pull-right">Subtotal</span>
			</td>
			<td colspan="2">{{{ Converter::value($cart->subtotal())->from('currency.eur')->to('currency.usd')->format() }}}</td>
		</tr>

		{{-- Discounts --}}
		@foreach ($cart->discounts() as $condition)
		{{-- Only show the condition if it's valid --}}
		@if ($condition->get('valid'))
		<tr>
			<td colspan="4">
				<span class="pull-right">{{ $condition->get('name') }}</span>
			</td>
			<td colspan="2">{{-- Converter::value($cart->discountValue($condition))->to('currency.usd')->format() --}}</td>
		</tr>
		@endif
		@endforeach

		{{-- Taxes --}}
		@foreach ($cart->taxes() as $rate)
		<tr>
			<td colspan="4">
				<span class="pull-right">{{ $rate->get('name') }}</span>
			</td>
			<td colspan="2">{{ Converter::value($rate->get('result'))->from('currency.usd')->to('currency.eur')->format() }}</td>
		</tr>
		@endforeach

		<tr>
			<td colspan="4">
				<span class="pull-right">Cart Weight</span>
			</td>
			<td colspan="2">{{{ Converter::value($cart->weight())->from('weight.g')->to('weight.kg')->format() }}}</td>
		</tr>

		<tr>
			<td colspan="4">
				<span class="pull-right">Total Usd</span>
			</td>
			<td colspan="2">{{{ Converter::value($total)->to('currency.usd')->format() }}}</td>
		</tr>
		<tr>
			<td colspan="4">
				<span class="pull-right">Total Eur</span>
			</td>
			<td colspan="2">{{{ Converter::value($total)->from('currency.usd')->to('currency.eur')->convert()->format() }}}</td>
		</tr>
		@endif
	</tbody>
</table>

@if ( ! $items->isEmpty())
<button type="submit" class="btn btn-info">Update</button>
<a href="{{ URL::to('cart/destroy') }}" class="btn btn-danger">Empty Cart</a>
<div class="pull-right">
	<a href="#" class="btn btn-warning">Checkout</a>
</div>
@endif
</form>

<br>
@stop
