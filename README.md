# ActivismeBE - Database Layering 

ActivismeBe Database Layering is a package for Laravel 5 which is used to abstract the database layer. This make applications much easier to maintain. 

## Installation 

**NOTE: This package can be used in Laravel 5.4 or higher.**

You can install the package via composer: 

```bash 
composer require activismebe/database-layering
```

Now add the service provider in `config/app.php` file: 

```php 
'providers' => [
    // ... 
    ActivismeBE\DatabaseLayering\Repositories\Providers\RepositoryProvider::class,
];
```

You can publish the configuration file now with; 

```bash
php artisan vendor:publish
```

After that u ready to go and completed the installation.