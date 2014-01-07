@extends('cart.layouts.default')

@section('page')
<form method="post">

	<div class="form-group">
		<label for="email">Email address</label>
		<input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
	</div>
	<div class="form-group">
		<label for="password">Password</label>
		<input type="password" class="form-control" name="password" id="password" placeholder="Password">
	</div>
	<button type="submit" class="btn btn-default">Login</button>

</form>

@stop
