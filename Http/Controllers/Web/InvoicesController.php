<?php

namespace Modules\Invoices\Http\Controllers\Web;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Companies\Entities\Company;
use Modules\Invoices\Entities\Invoice;

/**
 * Class InvoicesController
 * @package Modules\Invoices\Http\Controllers\Web
 */
class InvoicesController extends Controller
{
    /**
     * InvoicesController constructor.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            if (Auth::user()->hasPermissionTo('access invoices')) {
                return $next($request);
            } else {
                return redirect()->route('web.dashboardIndex')->with([
                    'message' => 'Você não tem permissão para acessar esta página, contate o administrador do sistema.',
                    'icon' => 'fas fa-times-circle',
                    'color' => 'danger'
                ]);
            }

        })->except('showTemporaryInvoice');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $companyInvoices = Invoice::join('companies', function ($join) {
            $join->on('companies.id', '=', 'invoices.invoiceable_id');
            $join->where('invoices.invoiceable_type', Company::class);
        })
            ->leftJoin('company_contacts', 'company_contacts.company_id', '=', 'companies.id')
            ->where('company_contacts.user_id', Auth::user()->id)
            ->orWhere('companies.user_id', Auth::user()->id)
            ->select('invoices.*')
            ->groupBy('invoices.id')
            ->orderBy('invoices.due_date', 'DESC')
            ->get();

        $userInvoices = auth()->user()->invoices()->orderBy('due_date', 'DESC')->get();

        $invoices = $companyInvoices->merge($userInvoices);

        return view('invoices::web.index', [
            'invoices' => $invoices
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function overdue()
    {
        $companyInvoices = Invoice::join('companies', 'companies.id', '=', 'invoices.invoiceable_id')
            ->join('company_contacts', 'company_contacts.company_id', '=', 'companies.id')
            ->where([
                ['invoices.invoiceable_type', '=', Company::class],
                ['invoices.due_date', '<', now()->format('Y-m-d')],
            ])
            ->orWhere(function ($query) {
                $query->where([
                    ['invoices.due_date', '<', now()->format('Y-m-d')],
                    ['company_contacts.user_id', '=', Auth::user()->id]
                ])
                    ->orWhere([
                        ['invoices.due_date', '<', now()->format('Y-m-d')],
                        ['companies.user_id', '=', Auth::user()->id]
                    ]);
            })->select('invoices.*')
            ->groupBy('invoices.id')
            ->get();

        $userInvoices = auth()->user()->invoices
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->where('status', '!=', config('invoices.invoice.status.paid'));

        $invoices = $companyInvoices->merge($userInvoices);

        return view('invoices::web.index', [
            'invoices' => $invoices
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($invoice)
    {
        $invoice = Invoice::find($invoice);

        return view('invoices::web.show', [
            'invoice' => $invoice
        ]);

    }

    /**
     * Temporary access link to invoice
     * @param Request $request
     * @param $user
     * @param $invoice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showTemporaryInvoice(Request $request, $user, $invoice)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $invoice = Invoice::findOrFail($invoice);

        switch ($invoice->invoiceable_type):
            case Company::class:
                $user = $invoice->invoiceable->owner;
                break;
            case User::class:
                $user = $invoice->invoiceable;
                break;
        endswitch;

        Auth::login($user);

        return redirect()->route('web.dashboard.showInvoice', ['invoice' => $invoice->id]);
    }

    /**
     * Get invoice pdf preview.
     * @param Request $request
     */
    public function getPdfPreview(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice);

        $pdf = \ConsoleTVs\Invoices\Classes\Invoice::make();

        foreach ($invoice->items as $item) {
            $pdf->addItem($item->name, $item->value, $item->amount, $item->id);
        }

        $pdf->number($invoice->id)
            ->name('Fatura')
            ->duplicate_header(true)
            ->due_date($invoice->due_date)
            ->date($invoice->issue_date)
            ->customer([
                'name' => $invoice->invoiceable->name,
                'id' => $invoice->invoiceable->id,
                'phone' => $invoice->invoiceable->invoiceable_phone,
                'location' => $invoice->invoiceable->street,
                'zip' => $invoice->invoiceable->zipcode,
                'city' => $invoice->invoiceable->city,
                'country' => $invoice->invoiceable->country
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
            ->show();
    }

    /**
     * Download invoice pdf
     * @param Request $request
     * @return mixed
     */
    public function getPdfDownload(Request $request)
    {
        $invoice = Invoice::findOrFail($request->invoice);

        $pdf = \ConsoleTVs\Invoices\Classes\Invoice::make();

        foreach ($invoice->items as $item) {
            $pdf->addItem($item->name, $item->value, $item->amount, $item->id);
        }

        return $pdf->number($invoice->id)
            ->name('Fatura')
            ->duplicate_header(true)
            ->due_date($invoice->due_date)
            ->date($invoice->issue_date)
            ->customer([
                'name' => $invoice->invoiceable->name,
                'id' => $invoice->invoiceable->id,
                'phone' => $invoice->invoiceable->invoiceable_phone,
                'location' => $invoice->invoiceable->street,
                'zip' => $invoice->invoiceable->zipcode,
                'city' => $invoice->invoiceable->city,
                'country' => $invoice->invoiceable->country
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
            ->download();
    }

    /**
     * Get invoice receipt
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReceipt($id)
    {
        $invoice = Invoice::findOrFail($id);

        return view('pdf.invoices.receipt', [
            'invoice' => $invoice
        ]);
    }

    /**
     * Download Nota Fiscal
     * @param $invoice
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadnf($invoice)
    {
        $invoice = Invoice::findOrFail($invoice);
        $file = $invoice->getMedia('invoice_nf')[0];

        if (!empty($file->id)) {
            return response()->download($file->getPath(), "{$file->name}.pdf");
        }
    }

}
