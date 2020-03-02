<?php

namespace Modules\Invoices\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Invoices\Entities\Invoice;

class InvoiceCompleted
{
    use SerializesModels;
    public $invoice;

    /**
     * Create a new event instance.
     * @param Invoice $invoice
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

}
