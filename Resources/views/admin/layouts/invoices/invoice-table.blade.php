<table class="w-100 dataTable table table-striped table-bordered comma-decimal-place">
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
                <a href="javascript:void(0)">{{ (!empty($invoice->invoiceable->name) ? $invoice->invoiceable->name : '') }}</a>
            </td>

            <td>{{ $invoice->formatted_issue_date }}</td>

            <td><a href="javascript:void(0)"
                   class="btn {{ ($invoice->status == config('invoices.invoice.status.paid') ? 'btn-success' : ($invoice->original_due_date < now() ? 'btn-danger' : 'btn-info')) }} btn-sm">{{ $invoice->formatted_due_date }}</a>
            </td>
            <td>
                R$ {{ $invoice->total }}
            </td>
            <td>
                @if($invoice->status == config('invoices.invoice.status.paid'))
                    <a href="javascript:void(0)" class="btn btn-success btn-sm">
                        Pago
                    </a>
                @endif

                @if($invoice->status == config('invoices.invoice.status.partial_paid'))
                    <a href="javascript:void(0)" class="btn btn-outline-info btn-sm">
                        Parcialmente pago
                    </a>
                @endif

                @if($invoice->status == config('invoices.invoice.status.overdue'))
                    <a href="javascript:void(0)" class="btn btn-danger btn-sm">
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

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
