<?php

namespace Modules\Invoices\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Modules\Invoices\Entities\Invoice;

class InvoiceCreated extends Mailable
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
        $this->messageSubject = "Nova fatura disponível";
        $this->messageText = "Olá {$this->invoice->invoiceable->name}, uma nova fatura está disponível em sua central do cliente. Para verificar os detalhes e realizar o pagamento clique no botão abaixo.";
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

        /*$pdf = \ConsoleTVs\Invoices\Classes\Invoice::make();

        foreach ($this->invoice->items as $item) {
            $pdf->addItem($item->name, $item->value, $item->amount, $item->id);
        }

        $filename = Str::slug("Fatura {$this->invoice->id}");
        $path = config('filesystems.disks.public.root') . "/pdf/{$filename}.pdf";
        $pdf->number($this->invoice->id)
            ->name('Fatura')
            ->duplicate_header(true)
            ->due_date($this->invoice->due_date)
            ->date($this->invoice->issue_date)
            ->customer([
                'name' => $this->invoice->invoiceable->social_name,
                'id' => $this->invoice->invoiceable->id,
                'phone' => $this->invoice->invoiceable->contact_phone,
                'location' => $this->invoice->invoiceable->street,
                'zip' => $this->invoice->invoiceable->zipcode,
                'city' => $this->invoice->invoiceable->city,
                'country' => $this->invoice->invoiceable->country
            ])
            ->business([
                'name' => setting('company_name'),
                'phone' => setting('company_phone'),
                'location' => setting('company_address'),
                'city' => setting('company_city'),
                'zip' => '',
                'country' => ''
            ])
            ->logo(url(\Illuminate\Support\Facades\Storage::url(setting('company_logo'))))
            ->save("public/pdf/{$filename}");*/

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
