@extends('cart.layouts.default')

@section('page')

<div class="row">

	@foreach ($products as $product)
	<div class="col-3 col-sm-3 col-lg-3">
		<div class="thumbnail">
			<div class="caption">
				<h2>{{{ $product->name }}}</h2>
				<p>{{ convert_value($product->price) }}</p>
				<p>
					@if ($cart->find(array('id' => $product->id)))
					<span class="btn btn-success btn-sm disabled">Added</span>
					@else
					<a class="btn btn-info btn-sm" href="{{ URL::to("cart/{$product->id}/add") }}">Add Cart</a>
					@endif

					{{-- Add to wishlist button --}}
					<span class="pull-right">
						{{-- Check if the product is on the wishlist already --}}
						@if ($wishlist->find(array('id' => $product->id)))
							<a class="btn btn-xs tip" href="{{ URL::to("wishlist/{$product->id}/remove") }}" title="Remove from Wishlist"><i class="glyphicon glyphicon-star"></i></a>
						@else
							<a class="btn btn-xs tip" href="{{ URL::to("wishlist/{$product->id}/add") }}" title="Add to Wishlist"><i class="glyphicon glyphicon-star-empty"></i></a>
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
