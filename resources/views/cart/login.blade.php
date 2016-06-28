@extends('cart.layouts.default')

@section('page')

	<div class="row">
		<div class="col-md-4 col-md-offset-4">

			<div class="well">
				<h3>Demo Users</h3>

				<table class="table">
					<thead>
						<tr>
							<th>Email</th>
							<th>Password</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>admin@admin.com</td>
							<td>password</td>
						</tr>
						<tr>
							<td>demo1@example.com</td>
							<td>demo123</td>
						</tr>
						<tr>
							<td>demo2@example.com</td>
							<td>demo123</td>
						</tr>
					</tbody>
				</table>
			</div>

			<form method="post">

                {{ csrf_field() }}

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
		</div>
	</div>

@stop
