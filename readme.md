# Cart Demo

This is a basic demo showing some of the functionality of the Cart package.

In this demo we are covering the following:

- Add a product to the shopping cart
- Remove a product from the shopping cart
- Update the item quantity
- Apply a coupon to the shopping cart
- Clear the shopping cart
- Add a product to the wishlist
- Remove a product from the wishlist
- Clear the wishlist
- Usage of various conditions
- Store the whole shopping cart on the database
- When logging in we are syncing the shopping cart back from the database


## Installation

To install this demo, firstly you must be a subscriber of Cartalyst's [Arsenal](http://cartalyst.com/arsenal).

1. Clone this repo:

	git clone git@github.com:cartalyst/demo-cart.git

2. Setup your virtual host.

3. Go into the directory in your terminal app and install the composer dependencies:

	composer install

4. Configure your database connection.

5. Run migrations for Sentry and the main application

	php artisan migrate --package=cartalyst/sentry
	php artisan migrate

6. Seed your database (you can do this as many times as you want, it will reset the database each time).

	php artisan db:seed


## Demo users

email: `admim@admin.com`
password: `password`

email: `demo1@example.com`
password: `demo123`

email: `demo2@example.com`
password: `demo123`


> *Note:* This demo is not a fully-fledged app. It's a demo, so we're not covering every possible scenario or completed every endpoint.
