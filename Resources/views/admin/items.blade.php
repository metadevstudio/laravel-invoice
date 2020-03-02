@extends('admin.master.master')
@section('content')
    <div class="content-body">
        <div class="row">
            @if($errors->all())
                <div class="col-12">
                    @foreach($errors->all() as $error)
                        @message(['type' => 'danger', 'icon' => 'la la-thumbs-down'])
                        <strong>Oops!</strong> {{ $error }}
                        @endmessage
                    @endforeach
                </div>
            @endif

            @if(session()->exists('message'))
                <div class="col-12">
                    @message(['type' => session()->get('color'), 'icon' => session()->get('icon')])
                    {{ session()->get('message') }}
                    @endmessage
                </div>
            @endif
        </div>
        <!-- Invoices -->
        <div class="d-flex justify-content-end">
            <div class="action">
                <div class="btn-group mr-1">
                    <button type="button" class="btn btn-icon mb-2 btn-success text-white" data-toggle="modal"
                            data-target="#addItem"><i class="fas fa-plus"></i> Cadastrar Novo Item
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card p-1">
                    <div class="card-content">
                        <table class="w-100 dataTable table table-striped table-bordered comma-decimal-place">
                            <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th width="30">Valor</th>
                                <th>Ação</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                    </td>

                                    <td>
                                        {{ $item->type }}
                                    </td>

                                    <td>
                                        {{ $item->value }}
                                    </td>

                                    <td class="text-center">
                                        <button type="button" class="btn shadow-none d-inline-block editItem"
                                                data-item-id="{{ $item->id }}"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('admin.items.destroy', ['id' => $item->id]) }}"
                                              class="d-inline-block" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn shadow-none btn-danger white deleteForm"><i
                                                    class="fas fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Invoices -->

        <!-- Modal -->
        <div class="modal fade text-left" id="addItem" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Adicionar Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="black">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.items.store') }}" name="addItem" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Nome</span>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Valor</span>
                                        <input type="text" name="value" class="form-control mask-money"
                                               value="{{ old('value') }}">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Tipo</span>
                                        <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Descrição</span>
                                        <textarea name="description" cols="30" rows="10"
                                                  class="form-control">{{ old('description') }}</textarea>
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

        <!-- Modal -->
        <div class="modal fade text-left" id="editItem" role="dialog"
             style="display: none; padding-right: 17px;" aria-modal="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel8">Adicionar Item</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="black">×</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.items.update') }}" name="editItem" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Nome</span>
                                        <input type="text" name="name" class="form-control" value="">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Valor</span>
                                        <input type="text" name="value" class="form-control mask-money" value="">
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="label w-100">
                                        <span class="text-bold-700">Tipo</span>
                                        <input type="text" name="type" class="form-control" value="">
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

            $(".editItem").click(function () {
                var button = $(this);
                var itemId = button.data('item-id');
                var form = $("form[name='editItem']");

                $.post('{{ route('admin.items.getData') }}', {
                    id: itemId
                }, function (response) {
                    if (response.success) {
                        form.find("input[name='item_id']").val(response.data.id);
                        form.find("input[name='name']").val(response.data.name);
                        form.find("input[name='value']").val(response.data.value);
                        form.find("input[name='type']").val(response.data.type);
                        form.find("textarea[name='description']").val(response.data.description);

                        $("#editItem").modal('show');
                    }
                }, 'json');
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
                responsive: false,
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
