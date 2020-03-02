<li class="{{ isActive('web.dashboard.invoices.index') }} {{ isActive('web.dashboard.invoices.overdue') }} {{ isActive('web.dashboard.showInvoice') }} nav-item">
    <a href="{{ route('web.dashboard.invoices.index') }}"><i class="fas fa-file-invoice"></i>
        <span class="menu-title" data-i18n="nav.templates.main">Faturas</span>
    </a>
    <ul class="menu-content">
        <li class="{{ isActive('web.dashboard.invoices.overdue') }}"><a class="menu-item"
                                                               href="{{ route('web.dashboard.invoices.overdue') }}"><i></i><span
                    data-i18n="nav.templates.vert.classic_menu">Atrasadas</span>
            </a>
        </li>
        <li class="{{ isActive('web.dashboard.invoices.index') }}"><a class="menu-item"
                                                              href="{{ route('web.dashboard.invoices.index') }}"><i></i><span
                    data-i18n="nav.templates.vert.classic_menu">Ver todas</span></a>
        </li>
    </ul>
</li>
