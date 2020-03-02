<?php
declare(strict_types=1);

namespace Modules\Invoices;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Invoices\Entities\Invoice;

/**
 * Trait Invoiceable
 * @package MetaDevStudio\LaravelInvoice
 */
trait Invoiceable
{

    /**
     * @return MorphMany
     */
    public function invoices(): MorphMany
    {
        return $this->morphMany(Invoice::class, 'invoiceable');
    }

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        if ($this->getMorphClass() == User::class) {
            return $this->attributes['name'] ?? '';
        } else {
            return $this->attributes['social_name'] ?? $this->attributes['alias_name'] ?? '';
        }
    }

    /**
     * @return string
     */
    public function getInvoiceableDocumentAttribute(): string
    {
        return $this->attributes['document'] ?? $this->attributes['document_company'] ?? '';
    }

    /**
     * @return string
     */
    public function getInvoiceableDocumentTypeAttribute(): string
    {
        if ($this->getMorphClass() == User::class) {
            return "cpf";
        } else {
            return "cnpj";
        }
    }

    /**
     * @return string
     */
    public function getInvoiceableTypeAttribute(): string
    {
        if ($this->getMorphClass() == User::class) {
            return 'individual';
        } else {
            return 'corporation';
        }
    }

    /**
     * @return mixed
     */
    public function getInvoiceableEmailAttribute()
    {
        if ($this->getMorphClass() == User::class) {
            return $this->attributes['email'];
        } else {
            return $this->attributes['contact_email'];
        }
    }

    /**
     * @return mixed
     */
    public function getInvoiceablePhoneAttribute()
    {
        if ($this->getMorphClass() == User::class) {
            return $this->attributes['cell'];
        } else {
            return $this->attributes['contact_phone'];
        }
    }

    /**
     * @return mixed
     */
    public function getInvoiceableUserIdAttribute()
    {
        if ($this->getMorphClass() == User::class) {
            return $this->id;
        } else {
            return $this->owner->id;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (!empty($this->name) ? $this->name : 'N/D');
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return (!empty($this->value) ? $this->value : 0.0);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return (!empty($this->type) ? $this->type : 'N/D');
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (!empty($this->description) ? $this->description : 'N/D');
    }

}
