# Cart Demo

This is a basic demo showing some of the functionality of the Cart package.

## Installation

To install this demo, firstly you must be a subscriber of Cartalyst's [Arsenal](http://cartalyst.com/arsenal).

Installation:

1. Clone this repo:

	git clone git@github.com:cartalyst/demo-cart.git

2. Setup your virtual host.

3. Go into the directory in your terminal app and install composer dependencies:

	composer install

4. Configure your database connection by opening `app/config/database.php` file.

5. Run the migrations

	php artisan migrate
	php artisan migrate --package="cartalyst/sentry"

6. Seed the database

	php artisan db:seed
