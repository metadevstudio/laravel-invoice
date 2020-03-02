<?php

namespace Modules\Invoices\Http\Controllers\Admin;

use App\Contract;
use App\Mail\Admin\SendReceipt;
use Modules\Invoices\Entities\Item;
use Modules\Invoices\Events\NewInvoiceAvailable;
use Modules\Invoices\Invoiceable;
use Modules\Invoices\Notifications\InvoiceCreated;
use Modules\Invoices\Events\InvoiceCompleted;
use App\Room;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Companies\Entities\Company;
use Modules\Invoices\Entities\Invoice;
use Modules\Invoices\Entities\InvoiceItem;
use Modules\Invoices\Entities\Payment;
use mysql_xdevapi\Exception;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class InvoicesController
 * @package Modules\Invoices\Http\Controllers\Admin
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
                return redirect()->route('admin.home')->with([
                    'message' => 'Você não tem permissão para acessar esta página, contate o administrador do sistema.',
                    'icon' => 'fas fa-times-circle',
                    'color' => 'danger'
                ]);
            }

        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /** @var Invoice $invoices */
        $invoices = Invoice::orderBy('issue_date', 'DESC')->get();

        $monthInvoices = Invoice::where([
            ['issue_date', '>=', now()->firstOfMonth()->format(('Y-m-d'))],
            ['issue_date', '<', now()->lastOfMonth()->format('Y-m-d')]
        ]);

        $paidInvoices = Invoice::where([
            ['issue_date', '>=', now()->firstOfMonth()->format(('Y-m-d'))],
            ['issue_date', '<', now()->lastOfMonth()->format('Y-m-d')]
        ])->where('status', \Config::get('invoices.invoice.status.paid'));

        $companyModule = \Module::find('Companies');

        $companies = (!empty($companyModule) && $companyModule->isEnabled() ? Company::all() : null);
        $clients = User::all();

        return view('invoices::admin.index', [
            'invoices' => $invoices,
            'openInvoices' => Invoice::open(),
            'paidInvoices' => $paidInvoices,
            'overdueInvoices' => Invoice::overdue(),
            'monthInvoices' => $monthInvoices,
            'clients' => $clients,
            'companies' => $companies,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function overdueInvoices()
    {
        $invoices = Invoice::overdue()->get();
        $clients = User::all();

        return view('invoices::admin.overdue', [
            'invoices' => $invoices,
            'clients' => $clients
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => Rule::requiredIf(empty($request->company_id)),
            'company_id' => Rule::requiredIf(empty($request->user_id)),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        if (!empty($request->company_id)) {
            /** @var Invoiceable $invoiceable */
            $invoiceable = Company::find($request->company_id);
        } else {
            /** @var Invoiceable $invoiceable */
            $invoiceable = User::find($request->user_id);
        }

        $invoice = new Invoice();
        $invoice->fill([
            'reference' => "FAT-" . str_pad((!empty(Invoice::all()->last()->id) ? Invoice::all()->last()->id + 1 : 1), 8, "0", STR_PAD_LEFT),
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'project_id' => $request->project,
            'discount' => (float)$request->discount,
            'terms' => $request->terms,
            'status' => Invoice::OPEN
        ]);

        try {
            $invoiceable->invoices()->save($invoice);
            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $invoice = Invoice::where('id', $request->invoice)->first();
        $itemsModal = Item::all();

        $companyModule = \Module::find('Companies');

        $companies = (!empty($companyModule) && $companyModule->isEnabled() ? Company::all() : null);
        $clients = (User::all());

        return view('invoices::admin.edit', [
            'invoice' => $invoice,
            'itemsModal' => $itemsModal,
            'companies' => $companies,
            'clients' => $clients
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => Rule::requiredIf(empty($request->company_id)),
            'company_id' => Rule::requiredIf(empty($request->user_id)),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $invoice = Invoice::find($id);
        $invoice->fill($request->all());

        if (!empty($request->company_id)) {
            /** @var Invoiceable $invoiceable */
            $invoiceable = Company::find($request->company_id);
        } else {
            /** @var Invoiceable $invoiceable */
            $invoiceable = User::find($request->user_id);
        }

        $invoice->invoiceable_type = $invoiceable->getMorphClass();
        $invoice->invoiceable_id = $invoiceable->id;

        try {
            $invoice->save();
            return redirect()->back()
                ->with([
                    'message' => 'Fatura atualizada com sucesso',
                    'color' => 'success',
                    'icon' => 'fas fa-thumbs-up'
                ]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage(), 'color' => 'danger']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        try {
            $invoice->delete();
            return redirect()->route('admin.invoices.index');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addInvoiceItem(Request $request, $id)
    {
        $json = [];

        $validator = Validator::make($request->all(), [
            'item_name' => Rule::requiredIf(empty($request->item_id)),
            'item_value' => Rule::requiredIf(empty($request->item_id)),
            'item_type' => Rule::requiredIf(empty($request->item_id))
        ]);

        if ($validator->fails()) {
            $message = "";

            foreach ($validator->errors() as $error) {
                $message .= $error . "\n";
            }

            $json['success'] = false;
            $json['message'] = $message;

            return response()->json($json);
        }

        /** @var Invoice $invoice */
        $invoice = Invoice::findOrFail($id);

        try {

            if (empty($request->item_id)) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->name = $request->item_name;
                $invoiceItem->value = $request->item_value;
                $invoiceItem->amount = $request->amount;
                $invoiceItem->type = $request->item_type;
                $invoiceItem->description = $request->description;

                $invoice->items()->save($invoiceItem);

                $invoice->update([
                    'sum' => $invoice->total()
                ]);
            } else {

                $item = Item::find($request->item_id);

                if(empty($item)){
                    $json['success'] = false;
                    $json['message'] = "Item não encontrado";

                    return response()->json($json);
                }

                $invoiceItem = new InvoiceItem();
                $invoiceItem->name = $item->name;
                $invoiceItem->value = str_replace('.', ',', $item->value);
                $invoiceItem->amount = $request->amount;
                $invoiceItem->type = $item->type;
                $invoiceItem->description = $item->description;

                $invoice->items()->save($invoiceItem);
                $invoice->update([
                    'sum' => $invoice->total()
                ]);
            }

            $json['success'] = true;
            $json['data'] = $invoiceItem;
            $json['data']['invoiceSubTotal'] = $invoiceItem->invoice()->first()->sub_total;
            $json['data']['invoiceTotal'] = $invoiceItem->invoice()->first()->total;
            $json['data']['editLink'] = route('admin.invoiceItems.getData', ['id' => $invoiceItem->id]);

            return response()->json($json);

        } catch (\Exception $e) {
            $json['success'] = true;
            $json['message'] = $e->getMessage();

            return response()->json($json);
        }

    }

    /**
     * @param Request $request
     * @param $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadNf(Request $request, $invoice)
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::find($invoice);
        $json = [];

        $validator = Validator::make($request->all(), [
            'nf' => 'mimes:pdf'
        ]);

        if ($validator->fails()) {
            $json['success'] = false;
            $json['message'] = "Você deve enviar um arquivo em formato pdf!";
            return response()->json($json);
        }

        if (empty($invoice->id)) {
            $json['success'] = false;
            $json['message'] = "Fatura não encontrada";
            return response()->json($json);
        }

        if ($request->allFiles()['nf']) {
            $nf = $invoice->getMedia('invoice_nf');

            if (!empty($nf[0]->id)) {
                $file = Media::find($nf[0]->id);
                $file->delete();
            }

            try {
                $media = $invoice->addMedia($request->nf)
                    ->usingFileName($request->nf->hashName())
                    ->toMediaCollection('invoice_nf');

                $json['success'] = true;
                $json['nfBtn'] = "<i class=\"fas fa-file-pdf\"></i> <a
                                                    href=\"" . route('web.invoice.downloadnf', ['invoice' => $invoice->id]) . "\"
                                                    target=\"_blank\">{$media->name}</a>";
                return response()->json($json);
            } catch (\Exception $e) {
                $json['success'] = false;
                $json['message'] = "{$e->getMessage()}";
                return response()->json($json);
            }

        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeInvoiceItem(Request $request)
    {
        /** @var InvoiceItem $invoiceItem */
        $invoiceItem = InvoiceItem::find($request->item_id);
        $inVoice = $invoiceItem->invoice()->first();
        $json = [];

        try {
            $invoiceItem->delete();

            /** @var Invoice $invoice */
            $invoice = Invoice::find($invoiceItem->invoice()->first()->id);
            $invoice->update([
                'sum' => $invoice->total(),
                'status' => ($invoice->pendingPayment() > 0 ? \Config::get('invoices.invoice.status.partial_paid') : \Config::get('invoices.invoice.status.paid'))
            ]);

            $json['success'] = true;
            $json['subTotal'] = $inVoice->sub_total;

            return response()->json($json);

        } catch (\Exception $e) {
            $json['success'] = false;
            $json['message'] = $e->getMessage();

            return response()->json($json);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePayment(Request $request)
    {
        /** @var Payment $payment */
        $payment = Payment::find($request->id);

        $json = [];

        try {
            $payment->delete();

            /** @var Invoice $invoice */
            $invoice = $payment->invoice()->first();

            $json['success'] = true;
            $json['pendingPayment'] = $payment->invoice()->first()->pending_payment;

            return response()->json($json);

        } catch (\Exception $e) {
            $json['success'] = false;
            $json['message'] = $e->getMessage();

            return response()->json($json);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addPayment(Request $request)
    {

        /** @var Invoice $invoice */
        $invoice = Invoice::find($request->invoice);

        if (empty($invoice)) {
            return redirect()->back()->withErrors(['Fatura não encontrada.']);
        }

        if ($invoice->paid()) {
            return redirect()->back()->with(['message' => 'Oops! A fatura já foi paga.', 'color' => 'danger', 'icon' => 'fa fa-times-circle']);
        }

        try {
            /** @var Payment $payment */
            $payment = $invoice->payments()->create([
                'user_id' => ($invoice->invoiceable_type == User::class ? $invoice->invoiceable->id : $invoice->invoiceable->owner->id),
                'reference' => str_pad((!empty(Payment::all()->last()->id) ? Payment::all()->last()->id + 1 : 1), 5, "0", STR_PAD_LEFT),
                'amount' => $request->amount,
                'type' => $request->type,
                'notes' => $request->notes,
                'date' => $request->date
            ]);

            $invoice->update([
                'paid' => $request->amount,
                'status' => (!$invoice->paid() ? \Config::get('invoices.invoice.status.partial_paid') : \Config::get('invoices.invoice.status.paid')),
                'paid_date' => (!$invoice->paid() ? null : date('Y-m-d'))
            ]);

            if (!empty($invoice->invoiceable_type) && $invoice->paid() && $invoice->invoiceable_type == Contract::class) {
                $contract = $invoice->contract()->first();
                $start_at = Carbon::createFromFormat('d/m/Y', $contract->start_at)->format('Y-m-d');
                $next_payment = (!empty($contract->next_payment) ? date('Y-m-d', strtotime($contract->frequency, strtotime($contract->next_payment))) : (date('Y-m-d', strtotime($contract->frequency, strtotime($start_at)))));

                try {
                    $contract->update([
                        'next_payment' => $next_payment,
                        'status' => 'active'
                    ]);
                } catch (\Exception $e) {
                    return redirect()->back()->with(["message" => "{$e->getMessage()}", 'color' => 'danger', 'icon' => 'fa fa-times-circle']);
                }
            }

        } catch (\Exception $e) {
            return redirect()->back()->with(["message" => "{$e->getMessage()}", 'color' => 'danger', 'icon' => 'fa fa-times-circle']);
        }

        if (!empty($request->send_receipt) && !$invoice->paid()) {
            Mail::send(new SendReceipt($invoice->invoiced()->first(), $invoice));
        } elseif ($invoice->paid() && !empty($request->send_receipt)) {
            event(new InvoiceCompleted($invoice));
        }


        return redirect()->back()->with(['message' => 'Pagamento adicionado com sucesso.', 'color' => 'success', 'icon' => 'la la-thumbs-up']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReceipt(Request $request)
    {
        $invoice = Invoice::find($request->id);
        $json = [];

        try {
            Mail::send(new SendReceipt($invoice->invoiced()->first(), $invoice));
            $json['success'] = true;
            $json['message'] = "Comprovante enviado com sucesso.";
            return response()->json($json);
        } catch (\Exception $e) {
            $json['success'] = false;
            $json['message'] = $e->getMessage() . " {$e->getFile()}";
            return response()->json($json);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function sendInvoice(Request $request)
    {
        $json = [];
        $invoice = Invoice::findOrFail($request->invoice);

        if (empty($invoice)) {
            return redirect()->back()->with('error', 'Fatura não encontrada.');
        }

        try {
            event(new NewInvoiceAvailable($invoice));
            $json['success'] = true;
            return response()->json($json);
        } catch (\Exception $e) {
            $json['success'] = false;
            $json['message'] = $e->getMessage() . " {$e->getFile()}.";
            Log::stack(['slack', 'daily'])->error($e->getMessage() . " {$e->getFile()}. {$e->getTraceAsString()}");
            return response()->json($json);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextPaymentID()
    {
        $statement = DB::select("show table status like 'payments'");
        return response()->json(['room_id' => $statement[0]->Auto_increment]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextInvoiceID()
    {
        $statement = DB::select("show table status like 'invoices'");
        return response()->json(['invoice_id' => $statement[0]->Auto_increment]);
    }

    /**
     * @param Request $request
     */
    public function getPdfPreview(Request $request)
    {
        $invoice = Invoice::find($request->id);

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
     * @param Request $request
     */
    public function getPdfDownload(Request $request)
    {
        $invoice = Invoice::find($request->id);

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
            ->download();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getReceipt(Request $request)
    {
        $invoice = Invoice::find($request->id);

        return view('pdf.invoices.receipt', [
            'invoice' => $invoice
        ]);
    }

    /**
     * @param Request $request
     * @param $status
     * @param $period
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function filter(Request $request, $status, $period)
    {
        $invoices = Invoice::query()
            ->when($status == 'all', function ($query) {
                return $query;
            })
            ->when($period == 'all', function ($query) {
                return $query;
            })
            ->when($status == \Config::get('invoices.invoice.status.open'), function ($query) {
                return $query->where('status', \Config::get('invoices.invoice.status.open'));
            })
            ->when($status == \Config::get('invoices.invoice.status.paid'), function ($query) {
                return $query->where('status', \Config::get('invoices.invoice.status.paid'));
            })
            ->when($status == \Config::get('invoices.invoice.status.overdue'), function ($query) {
                return $query->where([
                    ['due_date', '<', now()],
                    ['status', '!=', \Config::get('invoices.invoice.status.paid')]
                ]);
            })
            ->when($status == \Config::get('invoices.invoice.status.partial_paid'), function ($query) {
                return $query->where([
                    ['status', '=', \Config::get('invoices.invoice.status.partial_paid')]
                ]);
            })
            ->when($status == 'sent', function ($query) {
                return $query->whereNotNull('sent_date');
            })
            ->when($period == 'thisMonth', function ($query) {
                return $query->where([
                    ['issue_date', '>=', now()->firstOfMonth()->format('Y-m-d')],
                    ['issue_date', '<=', now()->lastOfMonth()->format('Y-m-d')],
                ]);
            })
            ->when($period == 'lastMonth', function ($query) {
                return $query->where([
                    ['issue_date', '>=', now()->subMonth()->firstOfMonth()->format('Y-m-d')],
                    ['issue_date', '<=', now()->subMonth()->lastOfMonth()->format('Y-m-d')],
                ]);
            })
            ->when($period == 'last3Month', function ($query) {
                return $query->where([
                    ['issue_date', '>=', now()->subMonths(3)->firstOfMonth()->format('Y-m-d')],
                    ['issue_date', '<=', now()->subMonths(3)->addMonths(2)->lastOfMonth()->format('Y-m-d')],
                ]);
            })
            ->when($period == 'last6Month', function ($query) {
                return $query->where([
                    ['issue_date', '>=', now()->subMonths(6)->firstOfMonth()->format('Y-m-d')],
                    ['issue_date', '<=', now()->subMonths(6)->addMonths(5)->lastOfMonth()->format('Y-m-d')],
                ]);
            })
            ->when($period == 'thisYear', function ($query) {
                return $query->where([
                    ['issue_date', '>=', now()->firstOfYear()->format('Y-m-d')],
                    ['issue_date', '<=', now()->lastOfYear()->format('Y-m-d')],
                ]);
            })
            ->when($period == 'lastYear', function ($query) {
                return $query->where([
                    ['issue_date', '>=', now()->subYear()->firstOfYear()->format('Y-m-d')],
                    ['issue_date', '<=', now()->subYear()->lastOfYear()->format('Y-m-d')],
                ]);
            })
            ->get();

        $clients = User::all();
        $companyModule = \Module::find('Companies');
        $companies = (!empty($companyModule) && $companyModule->isEnabled() ? Company::all() : null);

        $filter = [
            'status' => $status,
            'period' => $period
        ];

        return view('invoices::admin.index', [
            'invoices' => $invoices,
            'filter' => $filter,
            'clients' => $clients,
            'companies' => $companies
        ]);
    }
}
