<?php

namespace Modules\Invoices;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Invoices\Entities\Invoice;

/**
 * Interface HasInvoices
 * @package MetaDevStudio\LaravelInvoice
 */
interface HasInvoices
{
    public function getInvoiceableDocumentAttribute(): string;
    public function getNameAttribute(): string;
    public function invoices(): MorphMany;
    public function getKey();
    public function getInvoiceableTypeAttribute(): string;
}
