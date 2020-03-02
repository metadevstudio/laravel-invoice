@extends('web.dashboard.master.master')
@section('content-header')
    <div class="content-header row">
        <div class="content-header-light col-12">
            <div class="row">
                <div class="content-header-left col-12 mb-2">
                    <h3 class="content-header-title"><i class="fas fa-file-invoice-dollar"></i> Faturas</h3>
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('web.dashboardIndex') }}">Resumo</a>
                                </li>
                                <li class="breadcrumb-item active">Faturas
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="content-body">

        {{--<!-- Breadcrumb-->
        <div class="content-header row">
        <div class="content-header-left col-md-6 col-12">
            <div class="breadcrumb-wrapper col-12">
                <h2 class="content-header-title mb-0"><i class="la la-search font-large-1"></i> Minhas Faturas</h2>

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('web.dashboardIndex') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Faturas
                    </li>
                </ol>
            </div>
        </div>
    </div>
    <!-- Breadcrumb Fim-->--}}
        <div class="row">
            <div class="col-12">
                <div class="card p-1">
                    <div class="card-content">
                        <div class="table-responsive">
                            @invoiceClient(['invoices' => $invoices])
                            @endinvoiceClient
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Invoices -->
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
                responsive: true,
                "aaSorting": [],
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
