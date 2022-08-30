<?php
namespace Juanparati\LaravelBQ\Facades;

use Illuminate\Support\Facades\Facade;
use Juanparati\LaravelBQ\BigQueryManager;


class BigQueryManagerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return BigQueryManager::class;
    }
}