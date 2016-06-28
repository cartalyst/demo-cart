@extends('cart.layouts.default')

@section('scripts')
<script src="assets/js/cart.js"></script>
@stop

@section('page')

<div class="page-header">
	<h1>Cart</h1>
	<p class="lead">A modern and framework agnostic shopping cart package featuring multiple instances, item attributes and <a href="https://www.cartalyst.com/manual/conditions" target="_blank">Conditions</a>.</p>
	<p class="lead">
		<a href="https://github.com/cartalyst/demo-cart" class="btn btn-lg btn-default"><i class="fa fa-github"></i> GitHub</a>
		<a href="https://cartalyst.com/manual/cart" class="btn btn-lg btn-default"><i class="fa fa-file-text-o"></i> Manual</a>
	</p>
</div>

<div class="row">

	@foreach ($products as $product)
	<div class="col-3 col-sm-3 col-lg-3">

		<div class="thumbnail">

			<div class="caption">
				<h2>{{{ $product->name }}}</h2>
				<p>{{ converter()->value($product->price)->to('currency.usd')->format() }}</p>
				<p>
					@if ($item = $cart->find([ 'id' => $product->id ]))
					<a class="btn btn-danger btn-sm btn-remove" href="{{ route('cart.remove-item', [ $item[0]->get('rowId') ]) }}">Remove</a>
					@else
					<a class="btn btn-info btn-sm btn-add" href="{{ route('cart.add-item', [ $product->id ]) }}">Add Cart</a>
					@endif

					{{-- Add to wishlist button --}}
					<span class="pull-right">
						{{-- Check if the product is on the wishlist already --}}
						@if ($item = $wishlist->find([ 'id' => $product->id ]))
							<a class="btn btn-xs tip wishlist-remove" href="{{ route('wishlist.remove-item', [ $item[0]->get('rowId') ]) }}" title="Remove from Wishlist"><i class="fa fa-star fa-lg"></i></a>
						@else
							<a class="btn btn-xs tip wishlist-add" href="{{ route('wishlist.add-item', [ $product->id ]) }}" title="Add to Wishlist"><i class="fa fa-star-o fa-lg"></i></a>
						@endif
					</span>
				</p>

			</div>

		</div>

	</div>
	@endforeach

</div>

{!! $products->render() !!}

@stop
