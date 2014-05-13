@extends('cart.layouts.default')

@section('scripts')
<script src="assets/js/cart.js"></script>
@stop

@section('page')

<div class="page-header">
	<h1>Cart</h1>
	<p class="lead">A modern and framework agnostic shopping cart package featuring multiple instances, item attributes and <a href="https://www.cartalyst.com/manual/conditions" target="_blank">Conditions</a>.</p>
	<p class="lead">
		<a href="https://github.com/cartalyst/demo-cart" class="btn btn-lg btn-default"><i class="fa fa-github"></i> Github</a>
		<a href="https://cartalyst.com/manual/cart" class="btn btn-lg btn-default"><i class="fa fa-file-text-o"></i> Manual</a>
	</p>
</div>

<div class="row">

	@foreach ($products as $product)
	<div class="col-3 col-sm-3 col-lg-3">

		<div class="thumbnail">

			<div class="caption">
				<h2>{{{ $product->name }}}</h2>
				<p>{{ convert_value($product->price) }}</p>
				<p>
					@if ($item = $cart->find(array('id' => $product->id)))
					<a class="btn btn-danger btn-sm btn-remove" href="{{ URL::to("cart/{$item[0]->get('rowId')}/remove") }}">Remove</a>
					@else
					<a class="btn btn-info btn-sm btn-add" href="{{ URL::to("cart/{$product->id}/add") }}">Add Cart</a>
					@endif

					{{-- Add to wishlist button --}}
					<span class="pull-right">
						{{-- Check if the product is on the wishlist already --}}
						@if ($item = $wishlist->find(array('id' => $product->id)))
							<a class="btn btn-xs tip wishlist-remove" href="{{ URL::to("wishlist/{$item[0]->get('rowId')}/remove") }}" title="Remove from Wishlist"><i class="fa fa-star fa-lg"></i></a>
						@else
							<a class="btn btn-xs tip wishlist-add" href="{{ URL::to("wishlist/{$product->id}/add") }}" title="Add to Wishlist"><i class="fa fa-star-o fa-lg"></i></a>
						@endif
					</span>
				</p>

			</div>

		</div>

	</div>
	@endforeach

</div>

{{ $products->links() }}

@stop
