<?php

namespace Modules\Invoices\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Relations\morphTo;
use Illuminate\Support\Facades\DB;
use Modules\Projects\Entities\Project;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

/**
 * Class Invoice
 * @package MetaDevStudio\LaravelInvoice\Models
 */
class Invoice extends Model implements HasMedia
{

    use HasMediaTrait, LogsActivity;

    /*
    LOG CONFIG
    */
    protected static $submitEmptyLogs = false;
    protected static $logAttributes = ['*'];
    protected static $ignoreChangedAttributes = ['updated_at', 'created_at'];
    protected static $logOnlyDirty = true;

    /**
     *
     */
    const COMPLETED = 1;
    /**
     *
     */
    const PENDING = 0;
    const OPEN = 'open';

    private $chart;
    private $error;

    /**
     * @var array
     */
    protected $guarded = ['company_id', 'user_id'];

    protected $casts = [
        'due_date' => 'date',
        'issue_date' => 'date',
        'sent_date' => 'date',
    ];

    public function getNfLink()
    {
        $file = $this->getMedia('invoice_nf');

        if(!empty($file[0])){
            return route('web.invoice.downloadnf', ['invoice' => $this->id]);
        }else{
            return null;
        }
    }

    /**
     * @return hasMany
     */
    public function items(): hasMany
    {
        return $this->hasMany(config('invoices.invoiceItemModel'), 'invoice_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    /**
     * @return hasMany
     */
    public function payments(): hasMany
    {
        return $this->hasMany(config('invoices.paymentModel'), 'invoice_id', 'id');
    }

    /**
     * @return morphTo
     */
    public function invoiced(): morphTo
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * @return morphTo
     */
    public function contract(): morphTo
    {
        return $this->invoiceable();
    }

    /**
     * @return morphTo
     */
    public function invoiceable(): morphTo
    {
        return $this->morphTo();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOpen($query)
    {
        return $query->where('status', config('invoices.invoice.status.open'));
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePaidInvoices($query)
    {
        return $query->where('status', config('invoices.invoice.status.paid'));
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOverdue($query)
    {
        return $query->where([
            ['due_date', '<', now()],
            ['status', '!=', config('invoices.invoice.status.paid')]
        ]);
    }

    /**
     * Resulta no valor do período total a receber
     * @return string
     */
    static public function totalToReceive()
    {
        $invoices = Invoice::where([
            ['status', '!=', config('invoices.invoice.status.paid')]
        ])->get();

        $sum = 0;

        foreach ($invoices as $invoice) {
            $sum += $invoice->pendingPayment();
        }

        return number_format($sum, 2, ',', '.');
    }

    /**
     * Resulta no valor total a receber das faturas atrasadas
     * @return string
     */
    static public function totalOverdue()
    {
        $invoices = Invoice::where([
            ['status', '!=', config('invoices.invoice.status.paid')],
            ['due_date', '<', now()]
        ])->get();

        $sum = 0;

        foreach ($invoices as $invoice) {
            $sum += $invoice->pendingPayment();
        }

        return number_format($sum, 2, ',', '.');
    }

    /**
     * Resulta no valor total a receber das faturas atrasadas
     * @return string
     */
    static public function totalMonthOverdue()
    {
        $invoices = Invoice::where([
            ['status', '!=', config('invoices.invoice.status.paid')],
            ['due_date', '<', now()],
            ['issue_date', '>=', now()->firstOfMonth()->format(('Y-m-d'))],
            ['issue_date', '<=', now()->lastOfMonth()->format('Y-m-d')]
        ])->get();

        $sum = 0;

        foreach ($invoices as $invoice) {
            $sum += $invoice->pendingPayment();
        }

        return number_format($sum, 2, ',', '.');
    }

    /**
     * Resulta no valor total recebido durante todos os períodos
     * @return string
     */
    static public function totalReceived()
    {
        $payments = Payment::all();

        $sum = $payments->sum('amount');

        return number_format($sum, 2, ',', '.');
    }

    /**
     * Resulta no valor total recebido durante todos os períodos
     * @return string
     */
    static public function totalMonthReceived()
    {
        $payments = Payment::whereMonth('date', '=', date('m'))
            ->whereYear('date', '=', date('Y'))
            ->get();
        $sum = $payments->sum('amount');
        return number_format($sum, 2, ',', '.');
    }

    /**
     * Resulta em todos os valores a receber do mês atual
     * @return string
     */
    static public function totalMonthToReceive()
    {
        $invoices = Invoice::where([
            ['status', '!=', config('invoices.invoice.status.paid')],
            ['issue_date', '>=', now()->firstOfMonth()->format(('Y-m-d'))],
            ['issue_date', '<=', now()->lastOfMonth()->format('Y-m-d')]
        ])->get();

        $sum = 0;

        foreach ($invoices as $invoice) {
            $sum += $invoice->pendingPayment();
        }

        return $sum;
    }

    /**
     * @return string
     */
    public function getSubTotalAttribute()
    {
        $items = $this->items()->get()->sum(function ($item) {
            return ($item->value * $item->amount);
        });

        //$total = (($items * $this->tax()) / 100) + $items;

        return number_format($items, 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getTotalAttribute()
    {
        $items = $this->items()->get()->sum(function ($item) {
            return ($item->value * $item->amount);
        });
        $discount = $this->attributes['discount'];

        $total = (($items * $this->tax()) / 100) + $items;
        $total = $total - $discount;

        return number_format($total, 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getPendingPaymentAttribute()
    {
        $items = $this->items()->get()->sum(function ($item) {
            return ($item->value * $item->amount);
        });
        $discount = $this->attributes['discount'];

        $total = (($items * $this->tax()) / 100) + $items;
        $total = $total - $discount;

        $payments = $this->payments()->get()->sum(function ($item) {
            return ($item->amount);
        });

        return number_format($total - $payments, 2, ',', '.');
    }

    /**
     * @return float
     */
    public function pendingPayment(): float
    {
        $items = $this->items()->get()->sum(function ($item) {
            return ($item->value * $item->amount);
        });
        $discount = $this->attributes['discount'];

        $total = (($items * $this->tax()) / 100) + $items;
        $total = $total - $discount;

        $payments = $this->payments()->get()->sum(function ($item) {
            return ($item->amount);
        });

        return floatval($total - $payments);
    }

    /**
     * @return false|string
     */
    public function getFormattedIssueDateAttribute()
    {
        return date('d/m/Y', strtotime($this->attributes['issue_date']));
    }

    /**
     * @return false|string
     */
    public function getFormattedDueDateAttribute()
    {
        return date('d/m/Y', strtotime($this->attributes['due_date']));
    }

    /**
     * @return string
     */
    public function getTaxValueAttribute()
    {
        return number_format(($this->tax() * $this->subTotal()) / 100, 2, ',', '.');
    }

    /**
     * @return string
     */
    public function getDiscountValueAttribute()
    {
        return number_format($this->attributes['discount'], 2, ',', '.');
    }

    /**
     * @return mixed
     */
    public function getOriginalIssueDateAttribute()
    {
        return $this->attributes['issue_date'];
    }

    /**
     * @return mixed
     */
    public function getOriginalDueDateAttribute()
    {
        return $this->attributes['due_date'];
    }

    /**
     * @param $value
     */
    public function setTaxAttribute($value)
    {
        $this->attributes['tax'] = floatval($value);
    }

    /**
     * @param $value
     */
    public function setPaidAttribute($value)
    {
        $this->attributes['paid'] = $this->convertStringToDouble($value);
    }

    /**
     * @param $value
     */
    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = $this->convertStringToDouble($value);
    }

    /**
     * @return float
     */
    public function subTotal(): float
    {
        $items = $this->items()->get()->sum(function ($item) {
            return ($item->value * $item->amount);
        });

        return (float)$items ?? 0;
    }

    /**
     * @return float
     */
    public function total(): float
    {
        $items = $this->items()->get()->sum(function ($item) {
            return ($item->value * $item->amount);
        });
        $discount = $this->attributes['discount'];

        $total = (($items * $this->tax()) / 100) + $items;
        $total = $total - $discount;

        return $total;
    }

    /**
     * @return int
     */
    public function tax()
    {
        return $this->attributes['tax'] ?? 0;
    }

    /**
     * @param Builder $query
     * @param string $transaction_id
     * @return mixed
     */
    public function scopeFindByTransactionId($query, $transaction_id)
    {
        return $query->where('transaction_id', $transaction_id);
    }

    public function totalPaid()
    {
        $payments = $this->payments()->get()->sum(function ($item) {
            return ($item->amount);
        });

        return $payments;
    }

    public function getTotalPaidAttribute()
    {
        $payments = $this->payments()->get()->sum(function ($item) {
            return ($item->amount);
        });

        return number_format($payments, 2, '.', ',');
    }

    /**
     * Payment completed.
     *
     * @return boolean
     */
    public function paid(): bool
    {
        $items = $this->items()->get()->sum(function ($item) {
            return ($item->value * $item->amount);
        });
        $discount = $this->attributes['discount'];

        $total = (($items * $this->tax()) / 100) + $items;
        $total = $total - $discount;

        $payments = $this->payments()->get()->sum(function ($item) {
            return ($item->amount);
        });

        if ($total > 0) {
            return (($total - $payments) <= 0);
        } else {
            return false;
        }
    }

    /**
     * Payment is still pending.
     *
     * @return boolean
     */
    public function unpaid()
    {
        return in_array($this->payment_status, [self::PENDING]);
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


    public function issueDate($issue_date = null): self
    {
        $this->attributes['issue_date'] = (!empty($issue_date) ? $issue_date : date('Y-m-d'));
        $this->save();
        return $this;
    }

    public function dueDate($due_date = null): self
    {
        $this->attributes['due_date'] = (!empty($due_date) ? $due_date : date('Y-m-d'));
        $this->save();
        return $this;
    }

    public function addDiscount($discount = null): self
    {
        $this->attributes['discount'] += (!empty($discount) ? $discount : 0);
        $this->save();
        return $this;
    }

    public function addPayment(array $data)
    {
        $payment = new Payment;
        $payment->user_id = $this->attributes['invoiced_id'];
        $payment->date = $data['date'];
        $payment->amount = $data['amount'] ?? null;
        $payment->reference = $data['reference'] ?? null;
        $payment->payment_fee = $data['payment_fee'] ?? null;
        $payment->type = $data['type'] ?? null;
        $payment->notes = $data['notes'] ?? null;
        $payment->gateway = $data['gateway'] ?? null;
        $payment->payment_code = $data['payment_code'] ?? null;

        try {
            $payment = $this->payments()->save($payment);
            return $payment;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return null;
        }
    }

    public function getError()
    {
        return $this->erro;
    }

    public function changeStatus($status): self
    {
        $this->attributes['status'] = $status;
        $this->save();
        return $this;
    }

    public function generateReference(): string
    {
        return "FAT-" . str_pad((!empty(Invoice::all()->last()->id) ? Invoice::all()->last()->id + 1 : 1), 5, "0", STR_PAD_LEFT);
    }

    public function terms($terms): self
    {
        $this->attributes['terms'] = $terms;
        $this->save();
        return $this;
    }

}
