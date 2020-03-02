<?php

namespace Modules\Invoices\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Modules\Invoices\Events\NewInvoiceAvailable::class => [
            \Modules\Invoices\Listeners\NewInvoiceAvailable\NotifyCompanyContact::class
        ],
        \Modules\Invoices\Events\InvoiceCompleted::class => [
            \Modules\Invoices\Listeners\InvoiceCompleted\SendReceipt::class
        ]
    ];
}
