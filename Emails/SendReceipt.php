<?php

namespace Modules\Invoices\Emails;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Invoices\Entities\Invoice;
use Modules\Invoices\Entities\Payment;
use Modules\Invoices\HasInvoices;

/**
 * Class SendReceipt
 * @package Modules\Invoices\Emails
 */
class SendReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User $user
     */
    private $user;
    /**
     * @var Payment $payment
     */
    private $invoice;

    /**
     * SendReceipt constructor.
     * @param User $user
     * @param Invoice $invoice
     */
    public function __construct(HasInvoices $user, Invoice $invoice)
    {
        $this->user = $user;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->replyTo(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->to(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->subject("Comprovante de pagamento fatura {$this->invoice->reference} - " . setting('company_name'))
            ->view('pdf.invoices.receipt', [
                'user' => $this->user,
                'invoice' => $this->invoice
            ]);
    }
}

