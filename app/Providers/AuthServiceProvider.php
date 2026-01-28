<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Coupon;
use App\Models\Design;

use App\Models\Wallet;
use App\Models\Address;
use App\Models\Measurement;
use App\Models\DesignOption;
use App\Policies\OrderPolicy;
use App\Policies\CouponPolicy;
use App\Policies\DesignPolicy;
use App\Policies\WalletPolicy;
use App\Policies\AddressPolicy;
use App\Policies\MeasurementPolicy;
use App\Policies\DesignOptionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Address::class => AddressPolicy::class,
        DesignOption::class => DesignOptionPolicy::class,
        Measurement::class => MeasurementPolicy::class,
        Design::class => DesignPolicy::class,
        Order::class => OrderPolicy::class,
        Wallet::class => WalletPolicy::class,
        Coupon::class => CouponPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
