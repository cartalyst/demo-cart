@extends('cart.layouts.default')

@section('page')

<div class="row">

		@foreach ($products as $product)
		<div class="col-3 col-sm-3 col-lg-3">
			<div class="thumbnail">
				<div class="caption">
					<h2>{{{ $product->name }}}</h2>
					<p></p>
					<p>
						@if ($cart->find(array('id' => $product->slug)))
						<span class="btn btn-info btn-sm disabled">Add Cart</span>
						@else
						<a class="btn btn-info btn-sm" href="{{ URL::to("cart/{$product->slug}/add") }}">Add Cart</a>
						@endif

						{{-- Add to wishlist button --}}
						<span class="pull-right">
							{{-- Check if the product is on the wishlist already --}}
							@if ($cart->find(array('id' => $product->slug), 'wishlist'))
							<a><i class="glyphicon glyphicon-star"></i></a>
							@else
							<a class="btn btn-xs" href="{{ URL::to("product/add/{$product->slug}/wishlist") }}"><i class="glyphicon glyphicon-star-empty"></i></a>
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
