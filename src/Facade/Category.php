<?php
namespace Laravelcity\Categories\Facade;

use Illuminate\Support\Facades\Facade;

class Category extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'CategoriesClass';
    }
}