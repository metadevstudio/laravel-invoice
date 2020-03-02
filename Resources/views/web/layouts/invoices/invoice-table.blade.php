<table class="w-100 dataTable table table-striped table-bordered comma-decimal-place">
    <thead>
    <tr>
        <th width="110">Valor</th>
        <th>Vencimento</th>
        <th>Status</th>
        <th>Link</th>
        {{--<th>Ação</th>--}}
    </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td>
                R$ {{ $invoice->total }}
            </td>

            <td><a href="{{ route('web.dashboard.showInvoice', ['invoice' => $invoice->id]) }}"
                   class="btn {{ ($invoice->status == config('invoices.invoice.status.paid') ? 'btn-success' : ($invoice->original_due_date < now() ? 'btn-red' : 'btn-outline-dark')) }} btn-sm">{{ $invoice->formatted_due_date }}</a>
            </td>

            <td>
                @if($invoice->status == config('invoices.invoice.status.paid'))
                    <a href="{{ route('web.dashboard.showInvoice', ['invoice' => $invoice->id]) }}" class="btn btn-success btn-sm">
                        Pago
                    </a>
                @endif

                @if($invoice->status == config('invoices.invoice.status.partial_paid'))
                    <a href="{{ route('web.dashboard.showInvoice', ['invoice' => $invoice->id]) }}" class="btn btn-info btn-sm">
                        Parcialmente pago
                    </a>
                @endif

                @if($invoice->status == config('invoices.invoice.status.overdue'))
                    <a href="{{ route('web.dashboard.showInvoice', ['invoice' => $invoice->id]) }}" class="btn btn-red btn-sm">
                        Atrasado
                    </a>
                @endif
                @if($invoice->status == config('invoices.invoice.status.open'))
                    <a href="{{ route('web.dashboard.showInvoice', ['invoice' => $invoice->id]) }}" class="btn btn-info btn-sm">
                        Aberto
                    </a>
                @endif

                @if($invoice->status == 'waiting_payment')
                    <a href="{{ route('web.dashboard.showInvoice', ['invoice' => $invoice->id]) }}" class="btn btn-warning btn-sm">
                        Aguardando pagamento
                    </a>
                @endif
            </td>

            <td class="text-center">
                <a href="{{ route('web.dashboard.showInvoice', ['invoice' => $invoice->id]) }}"><i class="fas fa-link"></i> Acessar Fatura</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
