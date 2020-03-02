<?php

return [
    'invoiceModel' => \Modules\Invoices\Entities\Invoice::class,
    'invoiceItemModel' => \Modules\Invoices\Entities\InvoiceItem::class,
    'paymentModel' => \Modules\Invoices\Entities\Payment::class,

    'invoice' => [
        'status' => [
            'open' => 'open',
            'paid' => 'paid',
            'overdue' => 'overdue',
            'partial_paid' => 'partial_paid',
        ]
    ],

    'payment' => [
        'types' => [
            'cash' => 'Dinheiro',
            'bank_transfer' => 'Transferência Bancária',
            'paypal' => 'PayPal',
            'instant' => 'PayPal'
        ]
    ]
];
