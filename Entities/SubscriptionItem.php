<?php

namespace Modules\Invoices\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SubscriptionItem extends Model
{
    use LogsActivity;

    /*
    LOG CONFIG
    */
    protected static $submitEmptyLogs = false;
    protected static $logAttributes = ['*'];
    protected static $ignoreChangedAttributes = ['updated_at', 'created_at'];
    protected static $logOnlyDirty = true;
}
