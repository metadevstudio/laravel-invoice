@extends('admin.master.master')
@section('content')
    <div class="content-body">
        <!--/ eCommerce statistic -->
        <div class="row">
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="info">{{ (!empty($monthInvoices) ? $monthInvoices->get()->count() : 'N/D') }}</h3>
                                    <span>Total de Faturas  / mês</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="la la-user info font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="success">{{ (!empty($paidInvoices) ? $paidInvoices->get()->count() : 'N/D') }}</h3>
                                    <span>Faturas pagas</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="ft ft-trending-up success font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="media d-flex">
                                <div class="media-body text-left">
                                    <h3 class="danger">{{ (!empty($overdueInvoices) ? $overdueInvoices->get()->count() : 'N/D') }}</h3>
                                    <span>Faturas atrasadas</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="la la-user-times danger font-large-2 float-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices -->
        <div class="d-flex justify-content-end">
            <div class="action">
                <div class="btn-group mr-1">
                    <button type="button" class="btn btn-icon btn-success text-white" data-toggle="modal"
                            data-target="#addInvoice"><i class="fas fa-file-invoice-dollar"></i> Cadastrar Nova Fatura
                    </button>
                </div>
            </div>
            <div class="action">
                <div class="btn-group mr-1 mb-1">
                    <button type="button"
                            class="btn btn-info btn-icon dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        @if(!empty($filter['period']) && $filter['status'] == 'all')
                            Todos
                        @endif
                        @if(!empty($filter['period']) && $filter['period'] == 'thisMonth')
                            Este mês
                        @endif
                        @if(!empty($filter['period']) && $filter['period'] == 'lastMonth')
                            Último mês
                        @endif
                        @if(!empty($filter['period']) && $filter['period'] == 'last3Month')
                            Últimos 3 meses
                        @endif
                        @if(!empty($filter['period']) && $filter['period'] == 'last6Month')
                            Últimos 6 meses
                        @endif
                        @if(!empty($filter['period']) && $filter['period'] == 'thisYear')
                            Este ano
                        @endif
                        @if(!empty($filter['period']) && $filter['period'] == 'lastYear')
                            No ano passado
                        @endif
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => 'thisMonth', 'status' => (!empty($filter['status']) ? $filter['status'] : 'all')]) }}">Este
                            mês</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => 'lastMonth', 'status' => (!empty($filter['status']) ? $filter['status'] : 'all')]) }}">Último
                            mês</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => 'last3Month', 'status' => (!empty($filter['status']) ? $filter['status'] : 'all')]) }}">últimos
                            3 meses</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => 'last6Month', 'status' => (!empty($filter['status']) ? $filter['status'] : 'all')]) }}">Últimos
                            6 meses</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => 'thisYear', 'status' => (!empty($filter['status']) ? $filter['status'] : 'all')]) }}">Este
                            ano</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => 'lastYear', 'status' => (!empty($filter['status']) ? $filter['status'] : 'all')]) }}">No
                            ano passado</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => 'all', 'status' => (!empty($filter['status']) ? $filter['status'] : 'all')]) }}">Todos</a>
                    </div>
                </div>
            </div>
            <div class="action">
                <div class="btn-group mr-1 mb-1">
                    <button type="button"
                            class="btn btn-info btn-icon dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        @if(!empty($filter['status']) && $filter['status'] == 'all')
                            Todos
                        @endif
                        @if(!empty($filter['status']) && $filter['status'] == config('invoices.invoice.status.open'))
                            Aberto
                        @endif
                        @if(!empty($filter['status']) && $filter['status'] == 'sent')
                            Enviado
                        @endif
                        @if(!empty($filter['status']) && $filter['status'] == config('invoices.invoice.status.paid'))
                            Pago
                        @endif
                        @if(!empty($filter['status']) && $filter['status'] == config('invoices.invoice.status.overdue'))
                            Atrasado
                        @endif
                        @if(!empty($filter['status']) && $filter['status'] == config('invoices.invoice.status.partial_paid'))
                            Parcialmente Pago
                        @endif
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => (!empty($filter['period']) ? $filter['period'] : 'all'), 'status' => 'all']) }}">Todos</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => (!empty($filter['period']) ? $filter['period'] : 'all'), 'status' => config('invoices.invoice.status.open')]) }}">Aberto</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => (!empty($filter['period']) ? $filter['period'] : 'all'), 'status' => 'sent']) }}">Enviado</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => (!empty($filter['period']) ? $filter['period'] : 'all'), 'status' => config('invoices.invoice.status.paid')]) }}">Pago</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => (!empty($filter['period']) ? $filter['period'] : 'all'), 'status' => config('invoices.invoice.status.overdue')]) }}">Atrasado</a>
                        <a class="dropdown-item"
                           href="{{ route('admin.invoice.filter', ['period' => (!empty($filter['period']) ? $filter['period'] : 'all'), 'status' => config('invoices.invoice.status.partial_paid')]) }}">Parcialmente
                            Pago</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card p-1">
                    <div class="card-content">
                        <div class="table-responsive">
                            @include("invoices::admin.layouts.invoices.invoice-table")
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
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Adicionar Fatura</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="black">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.invoices.store') }}" name="addInvoice" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Cliente</span>
                                        <select name="user_id" class="form-control select2">
                                            <option value="" selected>-</option>
                                            @if(!empty($clients))
                                                @foreach($clients as $client)
                                                    <option
                                                        value="{{ $client->id }}">{{ $client->name }}
                                                        ({{ $client->invoiceable_document }})
                                                    </option>
                                                @endforeach
                                            @endif

                                        </select>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Empresa</span>
                                        <select name="company_id" class="form-control select2">
                                            <option value="" selected>-</option>
                                            @if(!empty($companies))
                                                @foreach($companies as $client)
                                                    <option value="{{ $client->id }}">{{ $client->social_name }}
                                                        ({{ $client->document_company }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Projeto</span>
                                        <select name="project" class="form-control select2">
                                            <option value="" disabled selected>-</option>
                                            @if(!empty($companies))
                                                @foreach($companies as $client)
                                                    <optgroup label="{{ $client->social_name }}">
                                                        @foreach($client->projects as $project)
                                                            <option data-client="{{ $client->id }}"
                                                                    value="{{ $project->id }}">{{ $project->name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            @endif

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
                                        <textarea name="terms" cols="30" rows="10" class="form-control mce">{{ old('terms') }}</textarea>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Salvar</button>
                            <button type="button" class="btn grey btn-danger" data-dismiss="modal">Fechar</button>
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

            $("select[name='company_id']").change(function () {
                var userId = $(this).find("option:selected").val();
                $("select[name='project']").prop('selectedIndex', 0);

                $("optgroup option").each(function (index, ele) {

                    if ($(ele).data('client') != userId) {
                        $("optgroup option").attr('disabled', false);
                        $(ele).attr('disabled', true);
                        $("select[name='project']").select2();
                    }

                });
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
            $('.dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'copy',
                        filename: 'faturas',
                        text: 'Copiar',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        className: 'btn btn-info btn-md ml-1 mr-1 rounded'
                    },
                    {
                        extend: 'excel',
                        filename: 'faturas',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        className: 'btn btn-success btn-md ml-1 mr-1 rounded'
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        filename: 'faturas',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        className: 'btn btn-danger btn-md ml-1 rounded'
                    },
                    {
                        extend: 'print',
                        text: 'Imprimir',
                        filename: 'faturas',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        className: 'btn btn-warning btn-md ml-1 mr-1 rounded'
                    }
                ],
                "aaSorting": [],
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
