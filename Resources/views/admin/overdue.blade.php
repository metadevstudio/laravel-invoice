@extends('admin.master.master')
@section('content')
    <div class="content-body">

        <!-- Breadcrumb-->
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12">
                <div class="breadcrumb-wrapper col-12">
                    <h2 class="content-header-title mb-0"><i class="la la-search font-large-1"></i> Filtro</h2>

                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Faturas</a>
                        </li>
                        <li class="breadcrumb-item active"> Atrasadas</li>
                    </ol>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-6 col-12">
                <div class="btn-group">
                    <button type="button" class="btn btn-round btn-success text-white" data-toggle="modal"
                            data-target="#addInvoice"><i class="la la-user-plus"></i> Cadastrar Nova Fatura
                    </button>

                </div>
            </div>
        </div>
        <!-- Breadcrumb Fim-->

        <!-- Invoices -->
        <div class="d-flex justify-content-end">
            <div class="action">
                <div class="btn-group mr-1 mb-1">
                    <button type="button"
                            class="btn btn-info btn-icon dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">Todos
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:void(0)">Este mês</a>
                        <a class="dropdown-item" href="javascript:void(0)">Último mês</a>
                        <a class="dropdown-item" href="javascript:void(0)">últimos 3 meses</a>
                        <a class="dropdown-item" href="javascript:void(0)">Últimos 6 meses</a>
                        <a class="dropdown-item" href="javascript:void(0)">Este ano</a>
                        <a class="dropdown-item" href="javascript:void(0)">No ano passado</a>
                        <a class="dropdown-item" href="javascript:void(0)">Todos</a>
                    </div>
                </div>
            </div>
            <div class="action">
                <div class="btn-group mr-1 mb-1">
                    <button type="button"
                            class="btn btn-info btn-icon dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">Todos
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="javascript:void(0)">Todos</a>
                        <a class="dropdown-item" href="javascript:void(0)">Aberto</a>
                        <a class="dropdown-item" href="javascript:void(0)">Enviado</a>
                        <a class="dropdown-item" href="javascript:void(0)">Pago</a>
                        <a class="dropdown-item" href="javascript:void(0)">Cancelado</a>
                        <a class="dropdown-item" href="javascript:void(0)">Atrasado</a>
                        <a class="dropdown-item" href="javascript:void(0)">Parcialmente Pago</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card p-1">
                    <div class="card-content">
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-striped table-bordered comma-decimal-place">
                                <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Cliente</th>
                                    <th width="30">Emissão</th>
                                    <th>Vencimento</th>
                                    <th width="110">Valor</th>
                                    <th>Status</th>
                                    {{--<th>Ação</th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.invoices.edit', ['invoice' => $invoice->id]) }}">{{ $invoice->reference }}</a>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)">{{ (!empty($invoice->invoiceable->company->owner->name) ? $invoice->invoiceable->company->owner->name : '') }}</a>
                                        </td>
                                        <td>{{ $invoice->issue_date }}</td>
                                        <td><a href="javascript:void(0)"
                                               class="btn {{ ($invoice->status == config('invoice.invoice.status.paid') ? 'btn-outline-success' : ($invoice->original_due_date < now() ? 'btn-outline-red' : 'btn-outline-dark')) }} btn-sm">{{ $invoice->due_date }}</a>
                                        </td>
                                        <td>
                                            R$ {{ $invoice->total }}
                                        </td>
                                        <td>
                                            @if($invoice->status == config('invoice.invoice.status.paid'))
                                                <a href="javascript:void(0)" class="btn btn-outline-success btn-sm">
                                                    Pago
                                                </a>
                                            @endif

                                            @if($invoice->status == config('invoice.invoice.status.partial_paid'))
                                                <a href="javascript:void(0)" class="btn btn-outline-info btn-sm">
                                                    Parcialmente pago
                                                </a>
                                            @endif

                                            @if($invoice->status == config('invoice.invoice.status.overdue'))
                                                <a href="javascript:void(0)" class="btn btn-outline-red btn-sm">
                                                    Atrasado
                                                </a>
                                            @endif
                                            @if($invoice->status == config('invoice.invoice.status.open'))
                                                <a href="javascript:void(0)" class="btn btn-outline-dark btn-sm">
                                                    Aberto
                                                </a>
                                            @endif

                                            @if($invoice->status == 'waiting_payment')
                                                <a href="javascript:void(0)" class="btn btn-outline-warning btn-sm">
                                                    Aguardando pagamento
                                                </a>
                                            @endif

                                        </td>
                                        {{--<td>
                                            <div class="action">
                                                <div class="btn-group mr-1 mb-1">
                                                    <button type="button"
                                                            class="btn btn-info btn-icon dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false"><i class="la la-gears"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item"
                                                           href="{{ route('admin.invoices.edit', ['invoice' => $invoice->id]) }}"><i
                                                                    class="la la-pencil-square"></i> Ver Fatura</a>
                                                        <div class="dropdown-divider"></div>
                                                        <form action="{{ route('admin.invoices.destroy', ['id' => $invoice->id]) }}"
                                                              method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a class="dropdown-item deleteButton"
                                                               href="javascript:void(0);"><i
                                                                        class="la la-times red"></i> Deletar Fatura</a>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>--}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Invoices -->

        <!-- Modal -->
        <div class="modal fade text-left" id="addInvoice" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary white">
                        <h4 class="modal-title white" id="myModalLabel8">Adicionar Fatura</h4>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.invoices.store') }}" name="addInvoice" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Cliente</span>
                                        <select name="user" class="form-control select2">
                                            <option value="" disabled selected>-</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}
                                                    ({{ $client->document }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Data de Emissão <span class="red">*</span></span>
                                        <input type="date" name="issue_date" value="{{ old('issue_date') }}"
                                               class="form-control" required>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Data de Vencimento <span class="red">*</span></span>
                                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                                               class="form-control" required>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Desconto (R$)</span>
                                        <input type="text" name="discount" value="{{ old('discount') }}"
                                               class="mask-money form-control">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Termos</span>
                                        <textarea name="terms" cols="30" rows="10"
                                                  class="form-control mce">{{ old('terms') }}</textarea>
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
        <!-- Modal -->
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

            /** Reseta formulário de adição de fatura ao fechar o modal */
            $("#addInvoice").on('hidden.bs.modal', function () {
                $("html form[name='addInvoice']").trigger('reset');
            });

            /** Busca próximo ID de referência da fatura ao abrir modal de cadastro */
            $("#addInvoice").on('show.bs.modal', function () {
                var form = $("form[name='addInvoice']");
                console.log("modal");

                $.post("{{ route('admin.invoice.nextId') }}", {}, function (response) {
                    if (response.invoice_id) {
                        form.find("input[name='reference']").val('FAT00' + response.invoice_id);
                    }
                }, 'json');
            });

        });

        @if(session()->has('error'))
        Swal.fire('Oops!', '{{ session()->get('error') }}', 'error');
        @endif

    </script>
    <script>
        $(function () {
            // DATATABLES
            $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        filename: 'faturas',
                        text: 'Copiar',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        },
                        className: 'btn btn-info btn-md ml-1 mr-1 rounded'
                    },
                    {
                        extend: 'excel',
                        filename: 'faturas',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        },
                        className: 'btn btn-success btn-md ml-1 mr-1 rounded'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        filename: 'faturas',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        },
                        className: 'btn btn-danger btn-md ml-1 rounded'
                    },
                    {
                        extend: 'print',
                        text: 'Imprimir',
                        filename: 'faturas',
                        exportOptions: {
                            columns: [0,1,2,3,4,5]
                        },
                        className: 'btn btn-warning btn-md ml-1 mr-1 rounded'
                    }
                ],
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
        });
    </script>
@endpush
