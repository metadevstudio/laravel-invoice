<?php

namespace Modules\Invoices\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Item extends Model
{
    protected $guarded = [];

    use LogsActivity;

    /*
    LOG CONFIG
    */
    protected static $submitEmptyLogs = false;
    protected static $logAttributes = ['*'];
    protected static $ignoreChangedAttributes = ['updated_at', 'created_at'];
    protected static $logOnlyDirty = true;

    public function setValueAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['value'] = null;
        } else {
            $this->attributes['value'] = floatval($this->convertStringToDouble($value));
        }
    }

    /**
     *  Convert an string to Double value
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
