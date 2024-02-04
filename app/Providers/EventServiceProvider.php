<?php

namespace App\Providers;

use App\Models\EForm;
use App\Models\LeaveRequest;
use App\Models\RedeemOffshoreLeave;
use App\Models\RedeemReplacementLeave;
use App\Models\TravelClaim;
use App\Observers\EFormObserver;
use App\Observers\LeaveRequestObserver;
use App\Observers\RedeemOffshoreLeaveObserver;
use App\Observers\RedeemReplacementLeaveObserver;
use App\Observers\TravelClaimObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        EForm::class => [
            EFormObserver::class
        ],

        LeaveRequest::class => [
            LeaveRequestObserver::class
        ],

        RedeemReplacementLeave::class => [
            RedeemReplacementLeaveObserver::class
        ],

        RedeemOffshoreLeave::class => [
            RedeemOffshoreLeaveObserver::class
        ],

        TravelClaim::class => [
            TravelClaimObserver::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        EForm::observe(EFormObserver::class);
        LeaveRequest::observe(LeaveRequestObserver::class);
        RedeemReplacementLeave::observe(RedeemReplacementLeaveObserver::class);
        RedeemOffshoreLeave::observe(RedeemOffshoreLeaveObserver::class);
        TravelClaim::observe(TravelClaimObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
