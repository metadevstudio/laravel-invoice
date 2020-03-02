<?php

namespace Modules\Invoices;

use Illuminate\Database\Eloquent\Relations\hasMany;

/**
 * Trait HasPayments
 * @package Modules\Invoices
 */
trait HasPayments
{

    /**
     * @return hasMany
     */
    public function payments(): hasMany
    {
        return $this->hasMany(config('invoices.paymentModel'), 'user_id', 'id');
    }
}
