<?php

namespace Modules\Invoices\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Relations\morphTo;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoiceItem extends Model
{
    use LogsActivity;

    protected $fillable = [
        'invoice_id',
        'item_type',
        'item_id',
        'name',
        'value',
        'amount',
        'type',
        'status',
        'description'
    ];

    /*
    LOG CONFIG
    */
    protected static $submitEmptyLogs = false;
    protected static $logAttributes = ['*'];
    protected static $ignoreChangedAttributes = ['updated_at', 'created_at'];
    protected static $logOnlyDirty = true;

    public function invoice(): belongsTo
    {
        return $this->belongsTo(config('invoices.invoiceModel'), 'invoice_id', 'id');
    }

    public function item(): morphTo
    {
        return $this->morphTo();
    }

    public function getSubTotalAttribute()
    {
        return number_format($this->attributes['value'] * $this->attributes['amount'], 2, ',', '.');
    }

    public function total()
    {
        return $this->attributes['value'] * $this->attributes['amount'];
    }

    public function getFormattedValueAttribute()
    {
        return number_format($this->attributes['value'], 2, ',', '.');
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = floatval($this->convertStringToDouble($value));
    }

    /**
     * @param string|null $param
     * @return mixed|null
     */
    private function convertStringToDouble(?string $param)
    {
        if (empty($param)) {
            return null;
        }

        return str_replace(',', '.', str_replace('.', '', $param));
    }
}
