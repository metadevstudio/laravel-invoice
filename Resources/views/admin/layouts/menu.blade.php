<li class="{{ isActive('admin.invoices.index') }} {{ isActive('admin.invoices.edit') }} nav-item">
    <a href="#"><i class="fas fa-file-invoice"></i>
        <span class="menu-title" data-i18n="nav.templates.main">Faturas</span>
        {{--@if($invoicesTotalCount > 0)
            <span
                class="badge badge badge-warning badge-pill float-right mr-2">{{ $invoicesTotalCount }}</span>
        @endif--}}
    </a>
    <ul class="menu-content">
        <li class="{{ isActive('admin.items') }}"><a class="menu-item"
                                                               href="{{ route('admin.items') }}"><i></i><span
                    data-i18n="nav.templates.vert.classic_menu">Itens</span>
                {{--@if($overdueInvoices > 0)
                    <span class="badge badge badge-warning badge-pill float-right mr-2"
                          data-toggle="tooltip" data-trigger="hover"
                          data-original-title="Faturas atrasadas">{{ $overdueInvoices }}</span>
                @endif--}}
            </a>
        </li>
        <li class="{{ isActive('admin.invoices.index') }}"><a class="menu-item"
                                                              href="{{ route('admin.invoices.index') }}"><i></i><span
                    data-i18n="nav.templates.vert.classic_menu">Ver faturas</span></a>
        </li>
    </ul>
</li>
