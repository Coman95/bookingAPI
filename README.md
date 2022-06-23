# Booking API

## Description

Web API using PHP with Laravel 9 that manages a booking platform.

### Running the API
Create database for the api and update .env file with database information.

``` 
git clone https://github.com/Coman95/bookingAPI 
cd bookingAPI
composer install
php artisan migrate 
php artisan db:seed
php artisan serve
```

#### Future improvements

- Generate slug from title
- Create Roles Class i.e. [ Admin, Manager, User ]
- Extend Trip search for location/description/price range/start_date
- Add Route for retrieving all users that have booked a certain trip