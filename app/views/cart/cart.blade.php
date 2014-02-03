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
				{{{ Converter::value($item->total())->from('currency.eur')->to('currency.usd')->format() }}}
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
		<tr>
			<td colspan="4">
				<span class="pull-right">Subtotal (with discounts)</span>
			</td>
			<td colspan="2">{{{ Converter::value($cart->applyConditions('discount'))->from('currency.eur')->to('currency.usd')->format() }}}</td>
		</tr>

		{{-- Items Discounts --}}
		@foreach ($cart->itemsConditionsTotal('discount') as $name => $value)
		<tr>
			<td colspan="4">
				<span class="pull-right">{{ $name }}</span>
			</td>
			<td colspan="2">{{ Converter::value($value)->to('currency.usd')->format() }}</td>
		</tr>
		@endforeach

		{{-- Items Taxes --}}
		@foreach ($cart->itemsConditionsTotal('tax') as $name => $value)
		<tr>
			<td colspan="4">
				<span class="pull-right">{{ $name }}</span>
			</td>
			<td colspan="2">{{ Converter::value($value)->to('currency.usd')->format() }}</td>
		</tr>
		@endforeach

		{{-- Cart Discounts --}}
		@foreach ($cart->conditionsTotal('discount', false) as $name => $value)
		<tr>
			<td colspan="4">
				<span class="pull-right">{{ $name }}</span>
			</td>
			<td colspan="2">{{ Converter::value($value)->to('currency.usd')->format() }}</td>
		</tr>
		@endforeach

		{{-- Cart Taxes --}}
		@foreach ($cart->conditionsTotal('tax', false) as $name => $value)
		<tr>
			<td colspan="4">
				<span class="pull-right">{{ $name }}</span>
			</td>
			<td colspan="2">{{ Converter::value($value)->to('currency.usd')->format() }}</td>
		</tr>
		@endforeach

		{{-- Cart Shipping --}}
		@foreach ($cart->conditionsTotal('shipping', false) as $name => $value)
		<tr>
			<td colspan="4">
				<span class="pull-right">{{ $name }}</span>
			</td>
			<td colspan="2">{{ Converter::value($value)->to('currency.usd')->format() }}</td>
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
