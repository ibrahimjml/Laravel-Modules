## Module Manager For Laravel 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ibrahimjml/laravel-modules.svg?style=flat-square)](https://packagist.org/packages/ibrahimjml/laravel-modules)

## Configure

- **Install**

```
composer require ibrahimjml/laravel-modules
```

- **Publish config, stubs, vite**

```
php artisan vendor:publish --tag=modules-config

php artisan vendor:publish --tag=modules-stubs

php artisan vendor:publish --tag=modules-vite
```

- **Autoload in composer psr-4**

```
"autoload": {
      "psr-4": {
               "App\\": "app/",
               "Modules\\": "Modules/",
               "Database\\Factories\\": "database/factories/",
               "Database\\Seeders\\": "database/seeders/"
       }
   },
```

**dump autoload**
- ` composer dump-autoload`