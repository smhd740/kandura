<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Address;
use App\Policies\AddressPolicy;

use App\Models\Design;
use App\Models\DesignOption;
use App\Models\Measurement;
use App\Policies\DesignPolicy;
use App\Policies\DesignOptionPolicy;
use App\Policies\MeasurementPolicy;
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Address::class => AddressPolicy::class,
        DesignOption::class => DesignOptionPolicy::class,
        Measurement::class => MeasurementPolicy::class,
        Design::class => DesignPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
