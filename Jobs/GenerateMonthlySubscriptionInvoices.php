<?php

namespace Modules\Invoices\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Modules\Invoices\Entities\Invoice;
use Modules\Subscriptions\Entities\Subscription;

/**
 * Class GenerateMonthlySubscriptionInvoices. Generate scheduled invoices.
 * @package Modules\Invoices\Jobs
 */
class GenerateMonthlySubscriptionInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $overdueInvoices = Invoice::where('due_date', '<', now()->format('Y-m-d'))
            ->get();
    }
}
