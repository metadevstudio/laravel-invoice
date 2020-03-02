<?php

namespace Modules\Invoices\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Modules\Invoices\Entities\Invoice;

class InvoiceOverdue extends Mailable
{
    use Queueable, SerializesModels;

    private $invoice;
    private $messageSubject;
    private $messageText;
    private $btnText;
    private $btnLink;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->messageSubject = "Fatura em atraso - " . setting('company_name');
        $this->messageText = "Olá {$this->invoice->invoiceable->name}, não identificamos o pagamento da sua fatura de número ${$this->invoice->reference}. Caso esteja tendo algum problema na visualização, entre em contato no e-mail: " . setting('company_email');
        $this->btnText = "Ver fatura";
        $this->btnLink = \URL::temporarySignedRoute('web.temporaryInvoice', now()->addDays(5), ['user' => $this->invoice->invoiceable->id, 'invoice' => $this->invoice->id]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $text = $this->messageText;
        $subject = $this->messageSubject;

        return $this->replyTo(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->to($this->invoice->invoiceable->invoiceable_email, $this->invoice->invoiceable->name)
            ->bcc(env('MAIL_FROM_ADDRESS'))
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->subject($subject . ' - ' . setting('company_name'))
            ->view('emails.notifications.message', [
                'title' => $subject . ' - ' . setting('company_name'),
                'text' => $text,
                'btnLink' => $this->btnLink,
                'btnText' => $this->btnText
            ]);
    }
}
