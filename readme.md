# Cart Demo

This is a basic demo showing some of the functionality of the Cart package.

In this demo we are covering the following:

- Adding a product to the shopping cart
- Removing a product from the shopping cart
- Update the item quantity
- Apply a coupon to the shopping cart
- Clear the shopping cart
- Adding a product to the wishlist
- Removing a product from the wishlist
- Clear the wishlist
- Usage of various conditions
- Store the whole shopping cart on the database
- When logging in we are syncing the shopping cart back from the database

## Installation

To install this demo, firstly you must be a subscriber of Cartalyst's [Arsenal](http://cartalyst.com/arsenal).

1. Clone this repo by running `git clone git@github.com:cartalyst/demo-cart.git` on your CLI.

2. Setup your virtual host.

3. Go into the directory in your terminal app and install the composer dependencies by running the `composer install` command on your CLI.

4. Configure your database connection.

5. Run the `php artisan demo:install` command on your CLI.

## Demo users

email: `admin@admin.com`
password: `password`

email: `demo1@example.com`
password: `demo123`

email: `demo2@example.com`
password: `demo123`

> *Note:* This demo is not a fully-fledged app. It's a demo, so we're not covering every possible scenario or completed every endpoint.
