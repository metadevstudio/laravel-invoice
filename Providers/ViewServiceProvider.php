<?php

namespace Modules\Invoices\Providers;

use Modules\Companies\Entities\Company;
use Modules\Invoices\Entities\Invoice;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /*View::composer('admin.master.master', function ($view) {

            // Salas com aprovação pendente
            $roomPendingApprove = Room::pendingApprove()->get()->count();
            // Solicitações de aluguel
            $pendingBookingRequests = BookingRequest::pending()->get()->count();
            //Faturas atrasadas
            $overdueInvoices = Invoice::overdue()->get()->count();

            $view->with('pendingBookings', Booking::pending()->get()->count());
            $view->with('overdueInvoices', $overdueInvoices);
            $view->with('pendingBookingRequests', $pendingBookingRequests);
            $view->with('roomPendingApprove', $roomPendingApprove);

            $view->with('invoicesTotalCount', $overdueInvoices);
            $view->with('contractsTotalCount', $pendingBookingRequests);
            $view->with('roomTotalCount', $roomPendingApprove);
        });*/

        View::composer("invoices::web.layouts.menu", function ($view){

        });
    }
}
