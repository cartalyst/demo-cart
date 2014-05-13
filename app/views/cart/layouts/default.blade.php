<!DOCTYPE html>
<html>
<head>
	<title>Cart Demo</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" media="screen">

	<style type="text/css">
		body
		{
			padding-top: 10px;
		}
	</style>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
      <![endif]-->
  </head>
  <body>
  	<div class="container">
  		<nav class="navbar xnavbar-fixed-top navbar-inverse" role="navigation">
  			<div class="navbar-header">
  				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
  					<span class="sr-only">Toggle navigation</span>
  					<span class="icon-bar"></span>
  					<span class="icon-bar"></span>
  					<span class="icon-bar"></span>
  				</button>
  				<a class="navbar-brand" href="{{ URL::to('/') }}">Cart</a>
  			</div>

  			<div class="collapse navbar-collapse navbar-ex1-collapse">
  				<ul class="nav navbar-nav">
  					<li{{ Request::is('/') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Products</a></li>
  					<li{{ Request::is('cart') ? ' class="active"' : null }}><a href="{{ URL::to('cart') }}">Cart <span class="badge cartCount">{{ app('cart')->items()->count() }}</span></a></li>
  					<li{{ Request::is('wishlist') ? ' class="active"' : null }}><a href="{{ URL::to('wishlist') }}">Wishlist <span class="badge wishlistCount">{{ app('wishlist')->items()->count() }}</span></a></li>
  				</ul>

  				<ul class="nav navbar-nav navbar-right">
  					<li><a href="https://cartalyst.com/manual/cart">Manual</a></li>
  					@if (Sentry::check())
  					<li><a href="{{ URL::to('logout') }}">Logout</a></li>
  					@else
  					<li{{ Request::is('login') ? ' class="active"' : null }}><a href="{{ URL::to('login') }}">Login</a></li>
  					@endif
  				</ul>
  			</div>
  		</nav>

  		@yield('page')
  	</div>

  	<script src="//code.jquery.com/jquery.js"></script>
  	<script src="assets/js/bootstrap.min.js"></script>
  	<script type="text/javascript">
  		$('.tip').tooltip();
  	</script>

  	@yield('scripts')

  	<script type="text/javascript">

  		var _gaq = _gaq || [];
  		_gaq.push(['_setAccount', 'UA-26550564-1']);
  		_gaq.push(['_setDomainName', 'cartalyst.com']);
  		_gaq.push(['_trackPageview']);

  		(function() {
  			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  		})();
  	</script>

  </body>
  </html>
