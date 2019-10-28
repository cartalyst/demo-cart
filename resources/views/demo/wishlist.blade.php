@extends('demo/layouts.default')

@section('content')
<div class="page-header">
    <h1>Wishlist? No problem!</h1>

    <p class="lead">Cart supports multiple cart instances, so that you can have as many shopping cart instances on the same application without any conflicts.</p>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Price</td>
                        <td>Total</td>
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
                            <div class="pull-right">
                                <a class="btn btn-danger btn-sm" href="{{ route('demo.wishlist.remove', [$item->get('rowId')]) }}">Delete</a>
                                <a class="btn btn-info btn-sm" href="{{ route('demo.wishlist.move', [$item->get('rowId')]) }}">Move to Cart</a>
                            </div>

                            <img src="http://placehold.it/80x80" alt="{!! $item->get('name') !!}" class="img-thumbnail"> {!! $item->get('name') !!}
                        </td>
                        <td>{!! Converter::value($item->get('price'))->from('currency.eur')->to('currency.usd')->format() !!}</td>
                        <td> {!! Converter::value($item->subTotal())->from('currency.eur')->to('currency.usd')->format() !!}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        @if ( ! $items->isEmpty())
        <a href="{{ route('demo.wishlist.destroy') }}" class="btn btn-danger">Empty Wishlist</a>
        @endif
    </div>
</div>
@stop
