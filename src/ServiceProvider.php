<?php

namespace SocialiteRavelry;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class ServiceProvider extends IlluminateServiceProvider
{
    public function register() {}

    public function boot()
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('ravelry', fn ($app) => $socialite->buildProvider(
            RavelryProvider::class,
            $app['config']['services.ravelry']
        ));
    }
}
