<?php

namespace Modules\Invoices\Notifications;

use App\Mail\Admin\Users\SendMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Invoices\Events\NewInvoiceAvailable;

class InvoiceCreated extends Notification
{
    use Queueable;
    private $event;
    /**
     * Create a new notification instance.
     * @param NewInvoiceAvailable $event
     * @return void
     */
    public function __construct(NewInvoiceAvailable $event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Modules\Invoices\Emails\InvoiceCreated
     */
    public function toMail($notifiable)
    {
        return (new \Modules\Invoices\Emails\InvoiceCreated($this->event->invoice));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase($notifiable)
    {
        $now = \Carbon\Carbon::now(new \DateTimeZone('America/Sao_Paulo'));

        return [
            'title' => "Nova fatura #{$this->event->invoice->reference} disponível",
            'icon' => 'la la-info',
            'type' => 'info',
            'body' => "Uma nova fatura com vencimento em {$this->event->invoice->formatted_due_date} está disponível para pagamento",
            'humanDiff' => $now,
            'link' => route('web.dashboard.showInvoice', ['invoice' => $this->event->invoice->id, 'read' => $this->id])
        ];
    }

    public function toBroadcast($notifiable)
    {
        $now = \Carbon\Carbon::now(new \DateTimeZone('America/Sao_Paulo'));

        return new BroadcastMessage([
            'title' => "Nova fatura #{$this->event->invoice->reference} disponível",
            'icon' => 'la la-info',
            'type' => 'info',
            'body' => "Uma nova fatura com vencimento em {$this->event->invoice->formatted_due_date} está disponível para pagamento",
            'humanDiff' => $now,
            'link' => route('web.dashboard.showInvoice', ['invoice' => $this->event->invoice->id, 'read' => $this->id])
        ]);
    }
}
