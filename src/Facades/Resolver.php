<?php

namespace SocialDept\AtpResolver\Facades;

use Illuminate\Support\Facades\Facade;

class Resolver extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'resolver';
    }
}
