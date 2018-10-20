<?php

namespace Laravelcity\Categories;

use Illuminate\Support\ServiceProvider;
use Laravelcity\Categories\Lib\Repository;

class CategoriesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot ()
    {

        // Lang
        $this->loadTranslationsFrom(__DIR__ . '/Lang/' , 'Categories');

        //migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        //bind
        $this->app->bind('CategoriesClass' , function () {
            return new Repository();
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register ()
    {
        //configs
        $this->mergeConfigFrom(
            __DIR__ . '/Config/categories.php' , 'categories'
        );
        $this->publishes([
            __DIR__ . '/Config/categories.php' => config_path('categories.php') ,
        ] , 'catconfig');

        // publish lang
        $this->publishes([
            __DIR__ . '/Lang/' => resource_path('lang/vendor/categories') ,
        ]);

    }
}
