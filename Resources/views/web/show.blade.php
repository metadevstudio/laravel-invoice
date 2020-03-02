@extends("web.dashboard.master.master")
@section("content")
    <div class="content-body">
        <!-- Client Data -->
        <div class="row">
            @if(!Auth::user()->profileIsCompleted())
                <div class="col-12">
                    <div class="alert alert-icon-left alert-danger mb-2" role="alert">
                        <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <strong>Atualização de dados pendente!</strong> Você precisa completar seu cadastro para usufruir de
                        todos os
                        recursos da @setting('company_name'). <a href="{{ route('web.dashboardProfile') }}"
                                                                 class="alert-link text-bold-700">Clique aqui</a> para
                        preencher as
                        informações pendentes.
                    </div>
                </div>
            @endif

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

            @if(!empty($invoice->invoiceable_type) && !empty($invoice->invoiceable_id) && !empty($invoice->contract) && $invoice->invoiceable_type == \App\Contract::class)
                @if(!$invoice->contract()->first()->isAccepted())
                    <div class="col-12">
                        <div class="alert alert-icon-left alert-warning mb-2" role="alert">
                            <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <strong>Termos de contrato pendente!</strong> Aceite os termos do contrato para poder
                            realizar o
                            pagamento da fatura. <a
                                href="{{ route('web.dashboard.contract.show', ['contract' => $invoice->contract()->first()->id]) }}"
                                class="alert-link">Clique aqui</a> para verificar os termos.
                        </div>
                    </div>
                @endif
            @endif

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="text-uppercase">Detalhes da Fatura</h6>
                        <div class="heading-elements">
                            <div class="action">
                                <div class="btn-group">
                                    @if($invoice->pendingPayment() > 0)
                                        <button class="btn btn-success btn-sm text-uppercase dropdown-toggle"
                                                style="margin-right: 5px;" data-toggle="dropdown">
                                            Pagar Fatura
                                        </button>
                                        <!-- start dropdown menu -->
                                        <div class="dropdown-menu">

                                            @if(setting('payment_pagarme_billet'))

                                                @if(!Auth::user()->profileIsCompleted())
                                                    <button type="button"
                                                            onclick="Swal.fire('Ops!', 'Antes de utilizar essa forma de pagamento você deve completar as informações do seu cadastro.', 'error');"
                                                            class="dropdown-item cursor-pointer text-black-50"><i
                                                            class="fas fa-file-invoice"></i> Boleto Bancário
                                                    </button>
                                                @else
                                                    <form target="_blank"
                                                          action="{{ route('pagarme.submit.billet', ['invoice_id' => $invoice->id, 'user' => $invoice->invoiceable->id ]) }}"
                                                          method="post">
                                                        @csrf
                                                        <button type="submit" name="billet" value="Boleto"
                                                                class="dropdown-item cursor-pointer"><i
                                                                class="fas fa-file-invoice"></i> Boleto Bancário
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            @if(setting('payment_paypal_active'))
                                                @switch(setting('payment_paypal_environment'))
                                                    @case('production')
                                                    @if(!empty(setting('payment_paypal_account_production')))
                                                        <form target="_blank"
                                                              action="https://www.paypal.com/cgi-bin/webscr"
                                                              method="post">
                                                            <input type="hidden" name="cmd" value="_xclick">
                                                            <input type="hidden" name="business"
                                                                   value="{{ setting('payment_paypal_account_production') }}">
                                                            <input type="hidden" name="item_name"
                                                                   value="{{ $invoice->reference }}">
                                                            <input type="hidden" name="item_number"
                                                                   value="{{ $invoice->reference }}">
                                                            <input type="hidden" name="invoice"
                                                                   value="{{ $invoice->id }}">

                                                            <input type="hidden" name="image_url"
                                                                   value="{{ url(\Illuminate\Support\Facades\Storage::url(setting('company_logo'))) }}">
                                                            <input type="hidden" name="amount"
                                                                   value="{{ $invoice->pendingPayment() }}">
                                                            <input type="hidden" name="no_shipping"
                                                                   value="1">
                                                            <input type="hidden" name="no_note" value="1">

                                                            <input type="hidden" name="currency_code"
                                                                   value="BRL">
                                                            <input type="hidden" name="charset"
                                                                   value="utf-8"/>
                                                            <input type="hidden" name="lc" value="BR"/>
                                                            <input type="hidden" name="country_code"
                                                                   value="BR"/>

                                                            <input type="hidden" name="bn"
                                                                   value="FC-BuyNow">
                                                            <input type="hidden" name="return"
                                                                   value="{{ route('paypal.paypalipn', ['invoice_id' => $invoice->id, 'env' => 'production']) }}">
                                                            <input type="hidden" name="cancel_return"
                                                                   value="{{ route('web.dashboard.invoices.index') }}">
                                                            <input type="hidden" name="rm" value="2">
                                                            <input type="hidden" name="notify_url"
                                                                   value="{{ route('paypal.paypalipn', ['invoice_id' => $invoice->id, 'env' => 'production']) }}">
                                                            <input type="hidden" name="custom"
                                                                   value="FAT{{ $invoice->reference }}">

                                                            <button class="dropdown-item cursor-pointer"
                                                                    type="submit"><i
                                                                    class="fab fa-cc-paypal"></i> PayPal (Cartão de
                                                                Crédito)
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @break

                                                    @case('sandbox')
                                                    @if(!empty(setting('payment_paypal_account_sandbox')))
                                                        <form target="_blank"
                                                              action="https://www.sandbox.paypal.com/cgi-bin/webscr"
                                                              method="post">
                                                            <input type="hidden" name="cmd" value="_xclick">
                                                            <input type="hidden" name="business"
                                                                   value="{{ setting('payment_paypal_account_sandbox') }}">
                                                            <input type="hidden" name="item_name"
                                                                   value="{{ $invoice->reference }}">
                                                            <input type="hidden" name="item_number"
                                                                   value="{{ $invoice->reference }}">
                                                            <input type="hidden" name="invoice"
                                                                   value="{{ $invoice->id }}">

                                                            <input type="hidden" name="image_url"
                                                                   value="{{ url(asset(setting('company_logo'))) }}">
                                                            <input type="hidden" name="amount"
                                                                   value="{{ $invoice->pendingPayment() }}">
                                                            <input type="hidden" name="no_shipping"
                                                                   value="1">
                                                            <input type="hidden" name="no_note" value="1">

                                                            <input type="hidden" name="currency_code"
                                                                   value="BRL">
                                                            <input type="hidden" name="charset"
                                                                   value="utf-8"/>
                                                            <input type="hidden" name="lc" value="BR"/>
                                                            <input type="hidden" name="country_code"
                                                                   value="BR"/>


                                                            <input type="hidden" name="bn"
                                                                   value="FC-BuyNow">
                                                            <input type="hidden" name="return"
                                                                   value="{{ route('paypal.paypalipn', ['invoice_id' => $invoice->id, 'env' => 'sandbox']) }}">
                                                            <input type="hidden" name="cancel_return"
                                                                   value="{{ route('web.dashboard.invoices.index') }}">
                                                            <input type="hidden" name="rm" value="2">
                                                            <input type="hidden" name="notify_url"
                                                                   value="{{ route('paypal.paypalipn', ['invoice_id' => $invoice->id, 'env' => 'sandbox']) }}">
                                                            <input type="hidden" name="custom"
                                                                   value="FAT{{ $invoice->reference }}">

                                                            <button class="dropdown-item cursor-pointer" type="submit">
                                                                <i class="fab fa-cc-paypal"></i> PayPal (Cartão de
                                                                Crédito)
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @break

                                                    @default
                                                    @if(!empty(setting('payment_paypal_account_sandbox')))
                                                        <div class="j_section_paypal">
                                                            <div class="col-12">
                                                                <form target="_blank" target="_blank"
                                                                      action="https://www.sandbox.paypal.com/cgi-bin/webscr"
                                                                      method="post">
                                                                    <input type="hidden" name="cmd" value="_xclick">
                                                                    <input type="hidden" name="business"
                                                                           value="{{ setting('payment_paypal_account_sandbox') }}">
                                                                    <input type="hidden" name="item_name"
                                                                           value="{{ $invoice->reference }}">
                                                                    <input type="hidden" name="item_number"
                                                                           value="{{ $invoice->reference }}">
                                                                    <input type="hidden" name="invoice"
                                                                           value="{{ $invoice->id }}">

                                                                    <input type="hidden" name="image_url"
                                                                           value="{{ url(asset(setting('company_logo'))) }}">
                                                                    <input type="hidden" name="amount"
                                                                           value="{{ $invoice->pendingPayment() }}">
                                                                    <input type="hidden" name="no_shipping"
                                                                           value="1">
                                                                    <input type="hidden" name="no_note" value="1">

                                                                    <input type="hidden" name="currency_code"
                                                                           value="BRL">
                                                                    <input type="hidden" name="charset"
                                                                           value="utf-8"/>
                                                                    <input type="hidden" name="lc" value="BR"/>
                                                                    <input type="hidden" name="country_code"
                                                                           value="BR"/>


                                                                    <input type="hidden" name="bn"
                                                                           value="FC-BuyNow">
                                                                    <input type="hidden" name="return"
                                                                           value="{{ route('paypal.paypalipn', ['invoice_id' => $invoice->id, 'env' => 'sandbox']) }}">
                                                                    <input type="hidden" name="cancel_return"
                                                                           value="{{ route('web.dashboard.invoices.index') }}">
                                                                    <input type="hidden" name="rm" value="2">
                                                                    <input type="hidden" name="notify_url"
                                                                           value="{{ route('paypal.paypalipn', ['invoice_id' => $invoice->id, 'env' => 'sandbox']) }}">
                                                                    <input type="hidden" name="custom"
                                                                           value="FAT{{ $invoice->reference }}">

                                                                    <button class="btn btn-lg btn-success"
                                                                            type="submit"><i
                                                                            class="fab fa-cc-paypal"></i> PayPal (Cartão
                                                                        de Crédito)
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endswitch
                                            @endif

                                            <button class="dropdown-item cursor-pointer" data-toggle="modal"
                                                    data-target="#bankTransfer"><i class="fas fa-receipt"></i>
                                                Transferência Bancária
                                            </button>
                                        </div>
                                        <!-- end dropdown menu -->
                                    @endif
                                    @if($invoice->paid())
                                        <a href="{{ route('web.dashboard.invoice.getReceipt', ['invoice' => $invoice->id ])  }}"
                                           class="btn btn-success btn-icon btn-sm text-uppercase" aria-haspopup="true"
                                           aria-expanded="false" target="_blank">Comprovante de pagamento
                                        </a>
                                    @endif
                                </div>

                                <div class="btn-group ">
                                    <button type="button"
                                            class="btn btn-success btn-icon btn-sm dropdown-toggle text-uppercase"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">PDF
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="{{ route('web.dashboard.invoice.getPdfDownload', ['id' => $invoice->id]) }}"
                                           target="_blank"><i
                                                class="la la-edit"></i> Download</a>
                                        <a class="dropdown-item"
                                           href="{{ route('web.dashboard.invoice.getPdfPreview', ['id' => $invoice->id]) }}"
                                           target="_blank"><i
                                                class="la la-eye"></i> Pré-Visualizar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <strong class="d-inline-block w-100">Status:</strong>
                                    @if($invoice->status == config('invoice.invoices.status.paid'))
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm">
                                            Pago
                                        </a>
                                    @endif

                                    @if($invoice->status == config('invoices.invoice.status.partial_paid'))
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm">
                                            Parcialmente pago
                                        </a>
                                    @endif

                                    @if($invoice->status == config('invoices.invoice.status.overdue'))
                                        <a href="javascript:void(0)" class="btn btn-red btn-sm">
                                            Atrasado
                                        </a>
                                    @endif
                                    @if($invoice->status == config('invoices.invoice.status.open'))
                                        <a href="javascript:void(0)" class="btn btn-info btn-sm">
                                            Aberto
                                        </a>
                                    @endif

                                    @if($invoice->status == 'waiting_payment')
                                        <a href="javascript:void(0)" class="btn btn-warning btn-sm">
                                            Aguardando pagamento
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Referência da Fatura:</strong>
                                    <span>{{ $invoice->reference }}</span>
                                </div>
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Cliente:</strong>
                                    <a href="{{ route('web.dashboardProfile') }}"
                                       class="btn btn-outline-info btn-sm">{{ $invoice->invoiceable->name }}</a>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Contato:</strong>
                                    <span>{{ $invoice->invoiceable->formatted_telephone }}</span>
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-md-6 col-12">
                                    <strong class="d-inline-block w-100">Emissão:</strong>
                                    <span
                                        class="btn btn-success btn-sm">{{ $invoice->formatted_issue_date }}</span>
                                </div>
                                <div class="col-md-6 col-12 mt-1">
                                    <strong class="d-inline-block w-100">Vencimento:</strong>
                                    <span
                                        class="btn {{ ($invoice->original_due_date < now() ? 'btn-danger' : 'btn-outline-dark') }} btn-sm">{{ $invoice->formatted_due_date }}</span>
                                </div>
                                <div class="col-12 mt-1">
                                    @if(!empty($invoice->getNfLink()))
                                        <strong class="d-inline-block w-100">Nota Fiscal:</strong>
                                        <span class="ml-1"><i class="fas fa-file-pdf"></i> <a
                                                href="{{ $invoice->getNfLink() }}"
                                                target="_blank">{{ $invoice->getMedia('invoice_nf')[0]->name }}</a></span>
                                    @endif
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
                    <div class="card-header bg-white">
                        <h6 class="text-uppercase">Itens da Fatura</h6>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                <tr>
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
                                @if($invoice->tax)
                                    <tr class="text-right">
                                        <td colspan="6">Imposto ({{ $invoice->tax() }}%):
                                            R$ {{ $invoice->tax_value }}</td>
                                    </tr>
                                @endif
                                <tr class="text-right">
                                    <td colspan="6">Subtotal: R$ {{ $invoice->sub_total }}</td>
                                </tr>
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
                        <div class="card-header bg-white">
                            <h6 class="card-title text-uppercase">Pagamentos</h6>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Notas</th>
                                        <th>Id do Pagamento</th>
                                        <th>Data de Pagamento</th>
                                        <th>Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody class="payment-table-body">
                                    @foreach($invoice->payments()->get() as $payment)
                                        <td>{{ $payment->notes }}</td>
                                        <td>#{{ $payment->reference }}</td>
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

        <!-- Modal editInvoice-->
        <div class="modal fade text-left" id="bankTransfer" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Pagamento via Transferência Bancária</h4>
                        <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="form-group">
                        <p style="text-align: center; ">Para realizar o pagamento via Depósito Bancário, utilize os
                            seguintes dados:</p>
                        <p style="text-align: center; "><br></p>
                        <p style="text-align: center;"><b>Banco Inter S.A</b></p>
                        <p style="text-align: center;"><b>Agência: 0001</b></p>
                        <p style="text-align: center;"><b>Conta corrente: 2211376-2</b></p>
                        <p style="text-align: center;"><b>Meta Dev Studio Tecnologia</b></p>
                        <p style="text-align: center; "><b>CNPJ:&nbsp;32.430.711/0001-92</b></p>
                        <p style="text-align: center; "><b><br></b></p>
                        <p style="text-align: left; margin-left: 50px;">Após realizar a transferência, envie o
                            comprovante para: contato@metadevstudio.com.br.</p></div>
                </div>
            </div>
        </div>
        <!-- Modal editInvoice-->

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
        });
    </script>
    <script>
        // DATATABLES
        $('.dataTable').DataTable({
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
    {{--<script type="text/javascript" src="{{ url('pagseguro/javascript') }}"></script>
    <script>
        $(function () {
            $(".doPaymentPagseguroBillet").click(function () {
                PagSeguroDirectPayment.setSessionId('{{ \Artistas\PagSeguro\PagSeguroFacade::startSession() }}');
                $("#senderHash").val(PagSeguroDirectPayment.getSenderHash());
                $("form[name='pagseguroBillet']").submit();
            });
        });
    </script>--}}
@endpush
