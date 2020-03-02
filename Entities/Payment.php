<?php

namespace Modules\Invoices\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',
        'date',
        'reference',
        'amount',
        'type',
        'notes',
        'gateway',
        'payment_code',
    ];

    public function invoice(): belongsTo
    {
        return $this->belongsTo(config('invoices.invoiceModel'), 'invoice_id', 'id');
    }

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $this->convertStringToDouble($value);
    }

    public function getValueAttribute()
    {
        return number_format($this->attributes['amount'], 2, ',', '.');
    }

    public function getDateAttribute()
    {
        return date('d/m/Y H:i:s', strtotime($this->attributes['date']));
    }

    public function getPaymentTypeAttribute()
    {
        return config('invoices.payment.types.' . $this->attributes['type']);
    }

    private function convertStringToDouble(?string $param)
    {
        if (empty($param)) {
            return null;
        }

        return str_replace(',', '.', str_replace('.', '', $param));
    }
}
