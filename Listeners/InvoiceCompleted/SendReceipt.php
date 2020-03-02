<?php

namespace Modules\Invoices\Listeners\InvoiceCompleted;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendReceipt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Mail::send(new \Modules\Invoices\Emails\SendReceipt($event->invoice->invoiceable, $event->invoice));
    }
}
