<?php

namespace Modules\Invoices\Listeners\NewInvoiceAvailable;

use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Modules\Invoices\Entities\Invoice;
use Modules\Invoices\Notifications\InvoiceCreated;

class NotifyCompanyContact
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
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $invoice = Invoice::find($event->invoice->id);

        if($invoice->invoiceable_type == User::class){
            Notification::send($event->invoice->invoiceable, new InvoiceCreated($event));
        }else{
            Notification::send($invoice->invoiceable->owner, new InvoiceCreated($event));
        }

        $invoice->sent_date = now()->format('Y-m-d');
        $invoice->save();
    }
}
