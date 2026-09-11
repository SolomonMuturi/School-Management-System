<div class="row">
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified" style="font-size: 13px;">
                    @php
                        $tabs = [
                            'dashboard'    => ['label' => 'Dashboard',  'icon' => 'icon-stats-bars3'],
                            'fee_types'    => ['label' => 'Fee Types',  'icon' => 'icon-price-tag2'],
                            'fee_structures' => ['label' => 'Fee Structures', 'icon' => 'icon-stack3'],
                            'billing'      => ['label' => 'Billing',    'icon' => 'icon-file-text3'],
                            'payments'     => ['label' => 'Payments',   'icon' => 'icon-cash3'],
                            'mpesa'        => ['label' => 'M-Pesa',     'icon' => 'icon-phone'],
                            'receipts'     => ['label' => 'Receipts',   'icon' => 'icon-file-text'],
                            'discounts'    => ['label' => 'Discounts',  'icon' => 'icon-percent'],
                            'refunds'      => ['label' => 'Refunds',    'icon' => 'icon-undo2'],
                            'expenses'     => ['label' => 'Expenses',   'icon' => 'icon-cart5'],
                            'suppliers'    => ['label' => 'Suppliers',  'icon' => 'icon-truck'],
                            'accounts'     => ['label' => 'Cash & Bank','icon' => 'icon-wallet'],
                            'reports'      => ['label' => 'Reports',    'icon' => 'icon-stats-growth'],
                            'settings'     => ['label' => 'Settings',   'icon' => 'icon-gear'],
                        ];
                        $current = request()->route() ? request()->route()->getName() : '';
                    @endphp
                    @foreach($tabs as $key => $tab)
                        @php
                            $active = $current === 'finance.' . $key || str_starts_with($current, 'finance.' . $key . '.');
                        @endphp
                        <li class="nav-item">
                            <a class="nav-link {{ $active ? 'active' : '' }}" href="{{ route('finance.' . $key) }}">
                                <i class="{{ $tab['icon'] }} mr-1"></i><span class="d-none d-md-inline">{{ $tab['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>