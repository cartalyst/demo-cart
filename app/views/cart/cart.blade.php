@extends('cart.layouts.default')

@section('page')

@include('partials.notifications')

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
				</td>
				<td>
					<input class="form-control" type="text" name="update[{{{ $item->get('rowId') }}}][quantity]" value="{{{ $item->get('quantity') }}}" />
				</td>
				<td>{{{ convert_value($item->get('price')) }}}</td>
				<td>{{{ convert_value($item->total()) }}}</td>
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
				<td colspan="2">{{{ convert_value($cart->subtotal()) }}}</td>
			</tr>
			<tr>
				<td colspan="4">
					<span class="pull-right">Subtotal (with discounts)</span>
				</td>
				<td colspan="2">{{{ convert_value($cart->total('discount')) }}}</td>
			</tr>

			{{-- Items Discounts --}}
			@foreach ($cart->itemsConditionsTotal('discount') as $name => $value)
			<tr>
				<td colspan="4">
					<span class="pull-right">{{{ $name }}}</span>
				</td>
				<td colspan="2">{{{ convert_value($value) }}}</td>
			</tr>
			@endforeach

			{{-- Items Taxes --}}
			@foreach ($cart->itemsConditionsTotal('tax') as $name => $value)
			<tr>
				<td colspan="4">
					<span class="pull-right">{{{ $name }}}</span>
				</td>
				<td colspan="2">{{{ convert_value($value) }}}</td>
			</tr>
			@endforeach

			{{-- Items Shipping --}}
			@foreach ($cart->itemsConditionsTotal('shipping') as $name => $value)
			<tr>
				<td colspan="4">
					<span class="pull-right">{{{ $name }}}</span>
				</td>
				<td colspan="2">{{{ convert_value($value) }}}</td>
			</tr>
			@endforeach

			{{-- Cart Discounts --}}
			@foreach ($cart->conditionsTotal('discount', false) as $name => $value)
			<tr>
				<td colspan="4">
					<span class="pull-right">{{{ $name }}}</span>
				</td>
				<td colspan="2">{{{ convert_value($value) }}}</td>
			</tr>
			@endforeach

			{{-- Cart Taxes --}}
			@foreach ($cart->conditionsTotal('tax', false) as $name => $value)
			<tr>
				<td colspan="4">
					<span class="pull-right">{{{ $name }}}</span>
				</td>
				<td colspan="2">{{{ convert_value($value) }}}</td>
			</tr>
			@endforeach

			{{-- Cart Coupons --}}
			@foreach ($cart->conditions('coupon') as $condition)
			<tr class="success">
				<td colspan="4">
					<a href="{{ URL::to('coupon/remove', $condition->get('name')) }}" class="pull-left label label-danger"><i class="glyphicon glyphicon-remove"></i></a>
					<span class="pull-right">{{{ $condition->get('name') }}} ({{{ $condition->get('code') }}})</span>
				</td>
				<td colspan="2">{{{ convert_value($condition->result()) }}}</td>
			</tr>
			@endforeach

			{{-- Cart Shipping --}}
			@foreach ($cart->conditionsTotal('shipping', false) as $name => $value)
			<tr>
				<td colspan="4">
					<span class="pull-right">{{{ $name }}}</span>
				</td>
				<td colspan="2">{{{ convert_value($value) }}}</td>
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
				<td colspan="2">{{{ convert_value($total) }}}</td>
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

{{-- Apply a Coupon --}}
@if ( ! $items->isEmpty() && ! $coupon)

{{ Form::open(array('route' => 'applyCoupon')) }}

	<div class="row">

		<div class="col-md-4">

			<div class="form-group">
				<label for="coupon" class="control-label">Apply Coupon<i class="fa fa-info-circle"></i></label>

				<input type="text" class="form-control" name="coupon" id="coupon" placeholder="Coupon Code" value="" required>

				<span class="help-block">Valid Codes: PROMO14, DISC2014</span>
			</div>

		</div>

	</div>

	<div class="form-group">
		<button class="btn">Apply Coupon</button>
	</div>

{{ Form::close() }}

@endif

@stop
