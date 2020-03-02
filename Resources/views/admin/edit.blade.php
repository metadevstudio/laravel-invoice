@extends("admin.master.master")
@section("content")
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                @if($errors->all())
                    @foreach($errors->all() as $error)
                        @message(['type' => 'danger', 'icon' => 'la la-thumbs-down'])
                        <strong>Oops!</strong> {{ $error }}
                        @endmessage
                    @endforeach
                @endif

                @if(session()->exists('message'))
                    @message(['type' => session()->get('color'), 'icon' => session()->get('icon')])
                    {{ session()->get('message') }}
                    @endmessage
                @endif

            </div>
        </div>
        <!-- Client Data -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title text-uppercase d-none d-md-block">Detalhes da Fatura</h1>
                        <div class="heading-elements my-0 float-sm-right float-md-none">
                            <div class="action">
                                @if($invoice->paid())
                                    <div class="btn-group">
                                        <button type="button" id="receiptButton"
                                                class="btn btn-info btn-icon btn-sm dropdown-toggle text-uppercase"
                                                data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" data-target="receipt">Comprovante de pagamento
                                        </button>
                                        <div class="dropdown-menu" id="receipt" aria-labelledby="receiptButton">
                                            <a class="dropdown-item sendReceipt"
                                               data-action="{{ route('admin.invoice.sendReceipt', ['id' => $invoice->id]) }}">
                                                <i class="far fa-envelope"></i> Enviar ao cliente
                                            </a>
                                            <a class="dropdown-item"
                                               href="{{ route('admin.invoice.receipt', ['id' => $invoice->id]) }}"
                                               target="_blank"><i
                                                    class="far fa-file-pdf"></i> Visualizar Comprovante</a>
                                        </div>
                                    </div>
                                @endif
                                <div class="btn-group ">
                                    <button type="button"
                                            class="btn btn-info btn-icon btn-sm dropdown-toggle text-uppercase"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" id="pdfButton">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="pdfButton">
                                        <a class="dropdown-item"
                                           href="{{ route('admin.invoice.getPdfDownload', ['id' => $invoice->id]) }}"
                                           target="_blank"><i
                                                class="la la-edit"></i> Download</a>
                                        <a class="dropdown-item"
                                           href="{{ route('admin.invoice.getPdfPreview', ['id' => $invoice->id]) }}"
                                           target="_blank"><i
                                                class="la la-eye"></i> Pré-Visualizar</a>
                                    </div>
                                </div>

                                <div class="btn-group">
                                    <button type="button"
                                            class="btn btn-info btn-icon btn-sm dropdown-toggle text-uppercase"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" id="actionsButton">
                                        <i class="fas fa-cogs"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="actionsButton">
                                        <a href="#" class="dropdown-item" style="margin-right: 5px;"
                                           data-target="#editInvoice" data-toggle="modal"><i
                                                class="fas fa-edit"></i> Editar Fatura</a>

                                        @if(!$invoice->paid())
                                            <a href="#" class="dropdown-item"
                                               style="margin-right: 5px;"
                                               data-toggle="modal" data-target="#addPayment">
                                                <i class="fas fa-money-bill-wave"></i> Adicionar Pagamento
                                            </a>
                                        @endif

                                        @if(!$invoice->paid())
                                            @if(!empty($invoice->invoiceable))
                                                <a href="#" class="dropdown-item sendInvoice"
                                                   data-action="{{ route('admin.invoice.sendInvoice', ['invoice' => $invoice->id]) }}">
                                                    <span class="button-loading"></span>
                                                    <i class="far fa-envelope"></i> Enviar Fatura ao cliente
                                                </a>
                                            @endif
                                        @endif

                                        <form action="{{ route('admin.invoices.destroy', ['id' => $invoice->id]) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item deleteInvoice"><i
                                                    class="fas fa-times-circle"></i> Excluir Fatura
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Referência da Fatura:</strong>
                                    <span>{{ $invoice->reference }}</span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Cliente:</strong>
                                    <a href="{{ route('admin.companies.edit', ['id' => $invoice->invoiceable->id ]) }}"
                                       class="btn btn-outline-info btn-sm">{{ $invoice->invoiceable->name }}</a>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Status:</strong>
                                    @if($invoice->status == config('invoices.invoice.status.paid'))
                                        <a href="javascript:void(0)" class="btn btn-outline-success btn-sm">
                                            Pago
                                        </a>
                                    @endif

                                    @if($invoice->status == config('invoices.invoice.status.partial_paid'))
                                        <a href="javascript:void(0)" class="btn btn-outline-info btn-sm">
                                            Parcialmente pago
                                        </a>
                                    @endif

                                    @if($invoice->status == config('invoices.invoice.status.overdue'))
                                        <a href="javascript:void(0)" class="btn btn-outline-red btn-sm">
                                            Atrasado
                                        </a>
                                    @endif
                                    @if($invoice->status == config('invoices.invoice.status.open'))
                                        <a href="javascript:void(0)" class="btn btn-outline-dark btn-sm">
                                            Aberto
                                        </a>
                                    @endif

                                    @if($invoice->status == 'waiting_payment')
                                        <a href="javascript:void(0)" class="btn btn-outline-warning btn-sm">
                                            Aguardando pagamento
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Contato:</strong>
                                    <span>{{ $invoice->invoiceable->telephone }}</span>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Emissão:</strong>
                                    <span
                                        class="btn btn-outline-dark btn-sm">{{ $invoice->formatted_issue_date }}</span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Vencimento:</strong>
                                    <span
                                        class="btn {{ ($invoice->original_due_date < now() ? 'btn-outline-red' : 'btn-outline-dark') }} btn-sm">{{ $invoice->formatted_due_date }}</span>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Último envio da fatura:</strong>
                                    <span>{{ (!empty($invoice->sent_date) ? $invoice->sent_date->format('d/m/Y') : "Nunca enviado") }}</span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <form action="#" name="sendnf">
                                        <input type="file" name="nf" class="d-none" id="nf">
                                        <label class="btn btn-md btn-pinterest" for="nf">
                                            Anexar Nota Fiscal
                                        </label>
                                        @if(!empty($invoice->getNfLink()))
                                            <span class="ml-1"><i class="fas fa-file-pdf"></i> <a
                                                    href="{{ $invoice->getNfLink() }}"
                                                    target="_blank">{{ $invoice->getMedia('invoice_nf')[0]->name }}</a></span>
                                        @else
                                            <span class="ml-1"></span>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title text-uppercase">Itens da Fatura</h1>
                        <div class="heading-elements" style="margin-top: -10px;">
                            @if($invoice->status != config('invoice.invoice.status.paid'))
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addItem"><i
                                        class="la la-plus d-block d-md-none"></i> <span class="d-none d-md-block">Adicionar Item</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Ação</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Qtde</th>
                                    <th>Preço unidade</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody class="invoice-table-body">
                                @foreach($invoice->items()->get() as $item)
                                    <tr>
                                        <td width="50" style="padding: 10px 20px;">
                                            <button class="btn shadow-none btn-sm p-0 font-size-small itemRemove"
                                                    data-item-id="{{ $item->id }}"
                                                    data-action="{{ route('admin.invoice.removeItem') }}"><i
                                                    class="la la-times red"></i>
                                            </button>
                                            <button class="btn shadow-none btn-sm p-0 font-size-small getItemData" data-action="{{ route('admin.invoiceItems.getData', ['id' => $item->id]) }}"><i
                                                    class="la la-pencil-square"></i></button>
                                        </td>
                                        <td>{{ $item->name }}</td>
                                        <td>{!! $item->description !!}</td>
                                        <td>{{ $item->amount }}</td>
                                        <td>R$ {{ $item->formatted_value }}</td>
                                        <td width="130">R$ {{ $item->sub_total }}</td>
                                    </tr>
                                @endforeach
                                {{--<tr>
                                    <td width="50" style="padding: 10px 20px;">
                                        <button class="btn  btn-sm p-0 font-size-small"><i class="la la-times red"></i>
                                        </button>
                                        <button class="btn btn-sm p-0 font-size-small"><i
                                                    class="la la-pencil-square"></i></button>
                                    </td>
                                    <td>Desenvolvimento de Sistemas</td>
                                    <td>Desenvolvimento de sistemas empresariais</td>
                                    <td>1</td>
                                    <td>R$ 300,00</td>
                                    <td width="130">R$ 300,00</td>
                                </tr>--}}
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td><br></td>
                                </tr>
                                <tr class="text-right">
                                    <td colspan="6" class="invoiceSubTotal">Subtotal: R$ {{ $invoice->sub_total }}</td>
                                </tr>
                                @if($invoice->tax)
                                    <tr class="text-right">
                                        <td colspan="6">Taxa de administração ({{ $invoice->tax() }}%):
                                            R$ {{ $invoice->tax_value }}</td>
                                    </tr>
                                @endif
                                @if($invoice->discount)
                                    <tr class="text-right">
                                        <td colspan="6" class="red">Desconto: - R$ {{ $invoice->discount_value }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="background-color: #fbfbfb;" colspan="6"
                                        class="text-right text-bold-700 bg-darken-2 invoiceTotal">Total:
                                        R$ {{ $invoice->total }}
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Client Data -->

        <!-- Pagamentos -->
        @if($invoice->payments()->count())
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom-dark">
                            <h1 class="card-title text-uppercase">Pagamentos</h1>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Ação</th>
                                        <th>Notas</th>
                                        <th>Id do Pagamento</th>
                                        <th>Tipo</th>
                                        <th>Data de Pagamento</th>
                                        <th>Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody class="payment-table-body">
                                    @foreach($invoice->payments()->get() as $payment)
                                        <tr>
                                            <td width="50" style="padding: 10px 20px;">
                                                <button class="btn shadow-none btn-sm p-0 font-size-small paymentRemove"
                                                        data-action="{{ route('admin.invoice.removePayment', ['id' => $payment->id]) }}">
                                                    <i
                                                        class="la la-times red"></i>
                                                </button>
                                                {{--<button class="btn btn-sm p-0 font-size-small"><i
                                                            class="la la-pencil-square"></i></button>--}}
                                            </td>
                                            <td>{{ $payment->notes }}</td>
                                            <td>#{{ $payment->reference }}</td>
                                            <td>
                                                @switch($payment->type)
                                                    @case('cash')
                                                    {{ "Dinheiro" }}
                                                    @break
                                                    @case('billet')
                                                    {{ "Boleto Bancário" }}
                                                    @break
                                                    @case('instant')
                                                    {{ "Paypal" }}
                                                    @break
                                                    @case('bank_transfer')
                                                    Transferência Bancária
                                                    @break
                                                    @default
                                                    ""
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>{{ $payment->date }}</td>
                                            <td width="130">R$ {{ $payment->value }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td style="background-color: #fbfbfb;" colspan="6"
                                            class="text-right text-bold-700 bg-darken-2 paymentTotal">Pagamento
                                            Pendente:
                                            R$ {{ $invoice->pending_payment }}
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    <!-- Pagamentos -->

        <!-- Modal addItem -->
        <div class="modal fade text-left" id="addItem" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Adicionar Item</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="black">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.invoice.addItem', ['id' => $invoice->id]) }}" name="addItem">
                        @csrf
                        <div class="modal-body">
                            <div class="row align-middle select-item">
                                <div class="col-10">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Item</span>
                                        <select name="item_id" class="form-control select2">
                                            <option disabled selected>Selecione um item</option>
                                            @foreach($itemsModal as $item)
                                                <option data-description="{{ $item->description }}"
                                                        value="{{ $item->id }}">
                                                    {{ $item->name }} - R$ {{ $item->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                                <div class="align-items-center d-flex col-2 add-manual" style="margin-top: 18px;">
                                    <a href="javascript:void(0);" class="btn btn-info">+</a>
                                </div>
                            </div>
                            <div class="row manual-item hidden">
                                <div class="col-12">
                                    <label class="label w-100">
                                        Nome
                                        <input name="item_name" type="text" class="form-control" value="">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        Valor
                                        <input name="item_value" type="text" class="form-control mask-money" value="">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        Tipo
                                        <input name="item_type" type="text" class="form-control" value="">
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Quantidade/Horas</span>
                                        <input type="number" name="amount" value="1" class="form-control">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Descrição</span>
                                        <textarea name="description" cols="30" rows="10"
                                                  class="form-control"></textarea>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-danger" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-success">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal addItem -->

        <!-- Modal addPayment -->
        <div class="modal fade text-left" id="addPayment" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Adicionar Pagamento</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="black">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.invoice.addPayment') }}" name="addPayment" method="POST">
                        @csrf
                        <input type="hidden" value="{{ $invoice->id }}" name="invoice">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Valor <span class="red">*</span></span>
                                        <input type="text" name="amount" class="mask-money form-control"
                                               value="{{ ($invoice->pending_payment > 0 ? $invoice->pending_payment : '') }}">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Data <span class="red">*</span></span>
                                        <input type="datetime-local" name="date" class="form-control">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Tipo de Pagamento <span class="red">*</span></span>
                                        <select name="type" class="form-control select2">
                                            <option value="cash" selected>Dinheiro</option>
                                            <option value="billet">Boleto Bancário</option>
                                            <option value="bank_transfer">Transferência Bancária</option>
                                            <option value="paypal">PayPal</option>
                                        </select>

                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Descrição <span class="red">*</span></span>
                                        <textarea name="notes" cols="30" rows="10"
                                                  class="form-control"></textarea>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Enviar comprovante ao cliente</span>
                                        <div class="w-100">
                                            <input type="checkbox" name="send_receipt"
                                                   class="make-switch switchBootstrap"
                                                   data-on-text="Sim"
                                                   data-off-text="Não" data-on-color="success"
                                                   data-off-color="danger"/>
                                        </div>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-danger" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-success">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal addPayment -->

        <!-- Modal editInvoice-->
        <div class="modal fade text-left" id="editInvoice" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Editar Fatura</h4>
                        <button type="button" class="close grey-blue" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.invoices.update', ['id' => $invoice->id]) }}" name="editInvoice"
                          method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">ID de Referência <span class="red">*</span></span>
                                        <input type="text" name="reference" value="{{ $invoice->reference }}"
                                               class="form-control" required>
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Cliente</span>
                                        <select name="user_id" class="form-control select2">
                                            <option value="" selected>-</option>
                                            @foreach($clients as $client)
                                                <option
                                                    value="{{ $client->id }}" {{ ($invoice->invoiceable_id == $client->id  && $invoice->invoiceable_type == $client->getMorphClass() ? 'selected' : '') }}>{{ $client->name }}
                                                    ({{ $client->invoiceable_document }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Empresa</span>
                                        <select name="company_id" class="form-control select2">
                                            <option value="" selected>-</option>
                                            @foreach($companies as $company)
                                                <option
                                                    value="{{ $company->id }}" {{ ($invoice->invoiceable->id == $company->id ? 'selected' : '') }}>{{ $company->name }}
                                                    ({{ $company->invoiceable_document }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Projeto</span>
                                        <select name="project_id" class="form-control select2">
                                            <option value="" disabled selected>-</option>
                                            @if(!empty($companies))
                                                @foreach($companies as $client)
                                                    <optgroup label="{{ $client->social_name }}">
                                                        @foreach($client->projects as $project)
                                                            <option data-client="{{ $client->id }}"
                                                                    value="{{ $project->id }}" {{ ($invoice->project_id == $project->id ? 'selected' : '') }}>{{ $project->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            @endif

                                        </select>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Status</span>
                                        <select name="status" class="form-control select2">
                                            <option
                                                value="{{ config('invoices.invoice.status.open') }}" {{ ($invoice->status == config('invoices.invoice.status.open') ? 'selected' : '') }}>
                                                Aberto
                                            </option>
                                            <option
                                                value="{{ config('invoices.invoice.status.partial_paid') }}" {{ ($invoice->status == config('invoices.invoice.status.partial_paid') ? 'selected' : '') }}>
                                                Parcialmente Pago
                                            </option>
                                            <option
                                                value="{{ config('invoices.invoice.status.paid') }}" {{ ($invoice->status == config('invoices.invoice.status.paid') ? 'selected' : '') }}>
                                                Pago
                                            </option>
                                            <option
                                                value="{{ config('invoices.invoice.status.overdue') }}" {{ ($invoice->status == config('invoices.invoice.status.overdue') ? 'selected' : '') }}>
                                                Atrasado
                                            </option>
                                        </select>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Data de Emissão <span class="red">*</span></span>
                                        <input type="date" name="issue_date"
                                               value="{{ old('issue_date') ?? $invoice->original_issue_date }}"
                                               class="form-control" required>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Data de Vencimento <span class="red">*</span></span>
                                        <input type="date" name="due_date"
                                               value="{{ old('due_date') ?? $invoice->original_due_date }}"
                                               class="form-control" required>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Desconto (R$)</span>
                                        <input type="text" name="discount"
                                               value="{{ old('discount') ?? $invoice->discount }}"
                                               class="form-control mask-money">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Taxa (%)</span>
                                        <input type="number" name="tax" value="{{ $invoice->tax() }}"
                                               class="form-control">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Termos</span>
                                        <textarea name="terms" cols="30" rows="10"
                                                  class="form-control mce">{{ $invoice->terms }}</textarea>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-danger" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-success">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal editInvoice-->

        <!-- Modal editInvoiceItem-->
        <div class="modal fade text-left" id="editInvoiceItem" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Editar Item da Fatura</h4>
                        <button type="button" class="close grey-blue" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.invoiceItems.update') }}" name="editItem" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        Nome
                                        <input name="item_name" type="text" class="form-control" value="">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        Valor
                                        <input name="item_value" type="text" class="form-control mask-money" value="">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        Tipo
                                        <input name="item_type" type="text" class="form-control" value="">
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Quantidade/Horas</span>
                                        <input type="number" name="amount" value="1" class="form-control">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Descrição</span>
                                        <textarea name="description" cols="30" rows="10"
                                                  class="form-control"></textarea>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn grey btn-danger" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-success">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal editInvoiceItem-->


        @if(Auth::user()->hasRole('Super Admin'))
            @pagarmeform([
            'invoice' => $invoice
            ])
            @endpagarmeform
        @endif

    </div>
@endsection
@push('js')
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("input[name='nf']").change(function () {

                var data = new FormData($("form[name='sendnf']")[0]);
                var form = $("form[name='sendnf']");
                var btn = $(this).siblings('label');

                $.ajax({
                    url: '{{ route('admin.invoice.uploadNf', ['invoice' => $invoice->id]) }}',
                    data: data,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(btn).block({
                            message: '<div class="ft-refresh-cw icon-spin font-medium-2"></div>',
                            overlayCSS: {
                                backgroundColor: "#fff",
                                opacity: 0.8,
                                cursor: "wait"
                            },
                            css: {
                                border: 0,
                                padding: 0,
                                backgroundColor: "transparent"
                            }
                        });
                    },
                    success: function (response) {
                        if (!response.success) {
                            Swal.fire('Ops!', response.message, 'error');
                        } else {
                            form.find('span').html(response.nfBtn);
                            Swal.fire("Sucesso", "Nota fiscal anexada com sucesso.", "success");
                        }
                        btn.unblock();
                    }
                });
            });

            $("select[name='user_id']").change(function () {
                if ($(this).find('option:selected').val() != '') {
                    $("select[name='company_id']").val(null).trigger("change");
                    $("select[name='company_id']").attr('disabled', true);
                } else {
                    $("select[name='company_id']").attr('disabled', false);
                }
            });

            $("select[name='company_id']").change(function () {
                if ($(this).find('option:selected').val() != '') {
                    $("select[name='user_id']").val(null).trigger("change");
                    $("select[name='user_id']").attr('disabled', true);
                } else {
                    $("select[name='user_id']").attr('disabled', false);
                }
            });

            /** Reseta formulário de adição de item ao fechar o modal */
            $("#addItem").on('hidden.bs.modal', function () {
                $("html form[name='addItem']").trigger('reset');
            });

            $(".add-manual").click(function () {
                var selectItem = $(".select-item");

                selectItem.fadeOut();
                selectItem.remove();

                $(".manual-item").removeClass('hidden');

            });

            /** Envia fatura ao cliente **/
            $(".sendInvoice").click(function (e) {
                e.preventDefault();

                var btn = $(this);
                var action = btn.data('action');
                $(".sendInvoice").attr("disabled", true);
                $(".button-loading").addClass('spinner-border spinner-border-sm');

                $.post(action, {}, function (response) {
                    if (response.success) {
                        Swal.fire('Tudo certo!', 'Fatura enviada ao cliente.', 'success');
                        $(".sendInvoice").prop("disabled", false);
                        $(".button-loading").removeClass('spinner-border spinner-border-sm');
                    } else {
                        Swal.fire('Oops! Algo deu errado', response.message, 'error');
                        $(".sendInvoice").prop("disabled", false);
                        $(".button-loading").removeClass('spinner-border spinner-border-sm');
                    }
                }, 'json');

            });

            /**
             * Envia comprovante de pagamento ao cliente
             * */

            $(".sendReceipt").click(function (e) {
                e.preventDefault();

                var btn = $(this);
                var action = btn.data('action');
                $(".sendReceipt").attr("disabled", true);
                $(".button-loading").addClass('spinner-border spinner-border-sm');

                $.post(action, {}, function (response) {
                    if (response.success) {
                        Swal.fire('Tudo certo!', 'Recibo enviado ao cliente.', 'success');
                        $(".sendReceipt").prop("disabled", false);
                        $(".button-loading").removeClass('spinner-border spinner-border-sm');
                    } else {
                        Swal.fire('Oops! Algo deu errado', response.message, 'error');
                        $(".sendReceipt").prop("disabled", false);
                        $(".button-loading").removeClass('spinner-border spinner-border-sm');
                    }
                }, 'json');

            });

            $("html").on('click', '.getItemData', function (e) {
                e.preventDefault();

                var btn = $(this);
                var action = btn.data('action');
                var form = $("form[name='editItem']");

                $.post(action, {
                }, function (response) {
                    if (response.success) {
                        form.find("input[name='item_id']").val(response.data.id);
                        form.find("input[name='item_name']").val(response.data.name);
                        form.find("input[name='item_value']").val(response.data.value);
                        form.find("input[name='item_type']").val(response.data.type);
                        form.find("input[name='amount']").val(response.data.amount);
                        form.find("textarea[name='description']").val(response.data.description);

                        $("#editInvoiceItem").modal("show");
                    }
                }, 'json');
            });
            /**
             * Remove um item da fatura
             */
            $("html").on('click', '.itemRemove', function (e) {
                e.preventDefault();

                var btn = $(this);
                var action = btn.data('action');
                var item_id = btn.data('item-id');

                Swal.fire({
                    title: 'Você tem certeza disso?',
                    text: "Essa alteração não poderá ser desfeita",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#209847',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, deletar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {

                        $.post(action, {
                            item_id: item_id
                        }, function (response) {
                            if (response.success) {
                                $("html .invoiceTotal").text('R$ ' + response.subTotal);
                                btn.closest('tr').slideToggle().remove();
                            }
                        }, 'json');

                    } else {
                        return false;
                    }
                });
                return false;
            });

            /**
             * Remove um pagamento da fatura
             */
            $("html").on('click', '.paymentRemove', function (e) {
                e.preventDefault();

                var btn = $(this);
                var action = btn.data('action');

                Swal.fire({
                    title: 'Você tem certeza disso?',
                    text: "Essa alteração não poderá ser desfeita",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#209847',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, deletar!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.value) {

                        $.post(action, {}, function (response) {
                            if (response.success) {
                                $("html .paymentTotal").text('Pagamento Pendente: R$ ' + response.pendingPayment);
                                btn.closest('tr').slideToggle().remove();
                            }
                        }, 'json');

                    } else {
                        return false;
                    }
                });
                return false;
            });

            $("select[name='item_id']").change(function (e) {
                var form = $("form[name='addItem']");
                form.find("textarea[name='description']").val($(this).find('option:selected').data('description'));
            });

            $("form[name='addItem']").submit(function (e) {
                e.preventDefault();

                var form = $(this);
                var action = form.attr('action');

                $.ajax({
                    url: action,
                    data: form.serialize(),
                    dataType: 'json',
                    type: 'POST'
                }).done(function (response) {
                    if (response.success) {
                        $(".invoice-table-body").append(
                            '<tr>' +
                            '<td width="50" style="padding: 10px 20px;">' +
                            '<button class="btn shadow-none btn-sm p-0 font-size-small itemRemove" data-item-id="' + response.data.id + '" data-action="{{ route('admin.invoice.removeItem') }}"><i class="la la-times red"></i></button>' +
                            '<button class="btn shadow-none btn-sm p-0 font-size-small getItemData" data-action="' + response.data.editLink + '" data-item-id="' + response.data.id + '" data-action="#"><i class="la la-pencil-square"></i></button>' +
                            '</td>' +
                            '<td>' +
                            response.data.name +
                            '</td>' +
                            '<td>' +
                            response.data.description +
                            '</td>' +
                            '<td>' +
                            response.data.amount +
                            '</td>' +
                            '<td>R$ ' +
                            response.data.value +
                            '</td>' +
                            '<td>R$ ' +
                            parseFloat(response.data.value) * response.data.amount +
                            '</td>' +
                            '</tr>'
                        );
                        $(".invoiceTotal").text('Total: R$ ' + response.data.invoiceTotal);
                        $(".invoiceSubTotal").text('Subtotal: R$ ' + response.data.invoiceSubTotal);
                        $("form[name='addPayment']").find("input[name='amount']").val(response.data.invoiceTotal);

                        $("#addItem").modal('hide');
                    } else {
                        Swal.fire("Ops!", response.message, "error");
                    }
                });

                /*$.post(action, {
                    item_type: item_type,
                    item_id: item_id,
                    invoice_id: invoice_id,
                    amount: amount,
                    description: description
                }, function (response) {

                    if (response.success) {
                        $(".invoice-table-body").append(
                            '<tr>' +
                            '<td width="50" style="padding: 10px 20px;">' +
                            '<button class="btn btn-sm p-0 font-size-small itemRemove" data-item-id="' + response.data.id + '" data-action="{{ route('admin.invoice.removeItem') }}"><i class="la la-times red"></i></button>' +
                            '<button class="btn btn-sm p-0 font-size-small itemRemove" data-item-id="' + response.data.id + '" data-action="#"><i class="la la-pencil-square"></i></button>' +
                            '</td>' +
                            '<td>' +
                            response.data.name +
                            '</td>' +
                            '<td>' +
                            response.data.description +
                            '</td>' +
                            '<td>' +
                            response.data.amount +
                            '</td>' +
                            '<td>R$ ' +
                            response.data.value +
                            '</td>' +
                            '<td>R$ ' +
                            response.data.value * amount +
                            '</td>' +
                            '</tr>'
                        );
                        $("html .invoiceTotal").text('R$ ' + response.data.invoiceSubTotal);

                        $("#addItem").modal('hide');
                    }

                }, 'json');*/

                return false;
            });

        });
    </script>
    <script>
        // DATATABLES
        $('#dataTable').DataTable({
            responsive: true,
            "pageLength": 25,
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
        });
    </script>
@endpush
