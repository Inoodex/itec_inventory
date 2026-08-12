@php
    $canView = function($permission) {
        if (!auth()->check()) {
            return true;
        }
        $user = auth()->user();
        return $user->hasRole(['Super Admin', 'Admin', 'admin']) || $user->can($permission);
    };
@endphp

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <!-- 1. Dashboard -->
                <li>
                    <a href="{{ route('index') }}" class="{{ request()->routeIs('index') ? 'active' : '' }}">
                        <i class="fe fe-grid"></i><span> Dashboard</span>
                    </a>
                </li>

                <!-- 2. Sales Management -->
                @if($canView('Sales Management'))
                    <li class="menu-title"><span>Sales Management</span></li>
                    <li>
                        <a href="{{ route('sales.create') }}" class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                            <i class="fe fe-shopping-cart"></i> <span> Add Sales</span>
                        </a>
                        <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.index') || request()->routeIs('sales.show') ? 'active' : '' }}">
                            <i class="fe fe-shopping-bag"></i> <span>Sales List</span>
                        </a>
                        <a href="{{ route('returns.index') }}" class="{{ request()->routeIs('returns.*') ? 'active' : '' }}">
                            <i class="fe fe-refresh-cw"></i> <span> Return List</span>
                        </a>
                    </li>
                @endif

                <!-- 3. Warranty Management -->
                @if($canView('Warranty Management'))
                    <li class="menu-title"><span>Warranty Management</span></li>
                    <li>
                        <a href="{{ route('warranties.create') }}" class="{{ request()->routeIs('warranties.create') ? 'active' : '' }}">
                            <i class="fe fe-plus-circle"></i> <span> Add Claim</span>
                        </a>
                        <a href="{{ route('warranties.index') }}" class="{{ request()->routeIs('warranties.index') || request()->routeIs('warranties.show') ? 'active' : '' }}">
                            <i class="fe fe-shield"></i> <span> Warranty Claims</span>
                        </a>
                    </li>
                @endif

                <!-- 4. Service Management -->
                @if($canView('Service Management'))
                    <li class="menu-title"><span>Services</span></li>
                    <li>
                        <a href="{{ route('service.create') }}" class="{{ request()->routeIs('service.create') ? 'active' : '' }}">
                            <i class="fe fe-plus-circle"></i> <span> Add Service</span>
                        </a>
                        <a href="{{ route('service.index') }}" class="{{ request()->routeIs('service.index') ? 'active' : '' }}">
                            <i class="fe fe-layers"></i> <span>Service List</span>
                        </a>
                    </li>
                @endif

                <!-- 5. Customer Management -->
                @if($canView('Customer Management'))
                    <li class="menu-title"><span>Customer Management</span></li>
                    <li>
                        <a href="{{ route('customers.create') }}" class="{{ request()->routeIs('customers.create') ? 'active' : '' }}">
                            <i class="fe fe-user-plus"></i> <span> Add Customer</span>
                        </a>
                        <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.index') ? 'active' : '' }}">
                            <i class="fe fe-users"></i> <span>Customers List</span>
                        </a>
                    </li>
                @endif

                <!-- 6. Product Catalog -->
                @if($canView('Product Management'))
                    <li class="menu-title"><span>Product Catalog</span></li>
                    <li>
                        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="fe fe-box"></i> <span> Product List</span>
                        </a>
                        <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="fe fe-layers"></i> <span> Category List</span>
                        </a>
                        <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
                            <i class="fe fe-tag"></i> <span> Brand List</span>
                        </a>
                    </li>
                @endif

                <!-- 7. Inventory & Stock -->
                @if($canView('Inventory Management'))
                    <li class="menu-title"><span>Inventory & Stock</span></li>
                    <li>
                        <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                            <i class="fe fe-database"></i> <span> Inventory Stock List</span>
                        </a>
                    </li>
                @endif

                <!-- 8. Purchase & Procurement -->
                @if($canView('Purchase Management'))
                    <li class="menu-title"><span>Purchases</span></li>
                    <li>
                        <a href="{{ route('purchase.index') }}" class="{{ request()->routeIs('purchase.index') ? 'active' : '' }}">
                            <i class="fe fe-shopping-cart"></i> <span> Purchase List</span>
                        </a>
                    </li>
                @endif

                <!-- 9. Vendor Management -->
                @if($canView('Vendor Management'))
                    <li class="menu-title"><span>Vendor Management</span></li>
                    <li>
                        <a href="{{ route('vendors.create') }}" class="{{ request()->routeIs('vendors.create') ? 'active' : '' }}">
                            <i class="fe fe-user-plus"></i> <span> Add Vendor</span>
                        </a>
                        <a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.index') ? 'active' : '' }}">
                            <i class="fe fe-users"></i> <span>Vendor List</span>
                        </a>
                    </li>
                @endif

                <!-- 10. Billing & Documents -->
                @if($canView('Payment Management'))
                    <li class="menu-title"><span>Billing & Payments</span></li>
                    <li>
                        <a href="{{ route('due-payments.index') }}" class="{{ request()->routeIs('due-payments.*') ? 'active' : '' }}">
                            <i class="fe fe-dollar-sign"></i> <span>Due Payments</span>
                        </a>
                        <a href="{{ route('bills.index') }}" class="{{ request()->routeIs('bills.*') ? 'active' : '' }}">
                            <i class="fe fe-file-text"></i> <span>Bill Generator</span>
                        </a>
                        <a href="{{ route('challans.index') }}" class="{{ request()->routeIs('challans.*') ? 'active' : '' }}">
                            <i class="fe fe-truck"></i> <span>Challan Generator</span>
                        </a>
                        <a href="{{ route('quotations.index') }}" class="{{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                            <i class="fe fe-clipboard"></i> <span>Quotation Generator</span>
                        </a>
                    </li>
                @endif

                <!-- 11. Project & Client Management -->
                @if($canView('Project Management'))
                    <li class="menu-title"><span>Project Management</span></li>
                    <li>
                        <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'active' : '' }}">
                            <i class="fe fe-briefcase"></i> <span>Projects List</span>
                        </a>
                        <a href="{{ route('project-items.index') }}" class="{{ request()->routeIs('project-items.*') ? 'active' : '' }}">
                            <i class="fe fe-plus-circle"></i> <span>Add Projects Items</span>
                        </a>
                        <a href="{{ route('project-costs.index') }}" class="{{ request()->routeIs('project-costs.*') ? 'active' : '' }}">
                            <i class="fe fe-dollar-sign"></i> <span>Projects Cost List</span>
                        </a>
                        @if($canView('Client Management'))
                            <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                                <i class="fe fe-users"></i> <span> Clients List</span>
                            </a>
                        @endif
                    </li>
                @endif

                <!-- 12. Accounts & Expenses -->
                @if($canView('Accounts Management'))
                    <li class="menu-title"><span>Accounts & Expenses</span></li>
                    <li>
                        <a href="{{ route('expense-categories.index') }}" class="{{ request()->routeIs('expense-categories.*') ? 'active' : '' }}">
                            <i class="fe fe-tag"></i> <span>Expense Categories</span>
                        </a>
                        <a href="{{ route('dailyExpenses.index') }}" class="{{ request()->routeIs('dailyExpenses.*') ? 'active' : '' }}">
                            <i class="fe fe-list"></i> <span>Daily Expense List</span>
                        </a>
                        <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                            <i class="fe fe-users"></i> <span>Employees</span>
                        </a>
                        <a href="{{ route('ta-da.index') }}" class="{{ request()->routeIs('ta-da.*') ? 'active' : '' }}">
                            <i class="fe fe-list"></i> <span>TA/DA Management</span>
                        </a>
                        <a href="{{ route('salary.index') }}" class="{{ request()->routeIs('salary.*') ? 'active' : '' }}">
                            <i class="fe fe-dollar-sign"></i> <span>Salary Manage</span>
                        </a>
                        <a href="{{ route('bank-details.index') }}" class="{{ request()->routeIs('bank-details.*') ? 'active' : '' }}">
                            <i class="fe fe-layers"></i> <span>Bank Details</span>
                        </a>
                    </li>
                @endif

                <!-- Double-Entry Accounting & Bookkeeping -->
                @if(auth()->check() && auth()->user()->hasRole(['Super Admin', 'Admin', 'admin']))
                    <li class="menu-title"><span>Accounting & Bookkeeping</span></li>
                    <li>
                        <a href="{{ route('chart-of-accounts.index') }}" class="{{ request()->routeIs('chart-of-accounts.*') ? 'active' : '' }}">
                            <i class="fe fe-folder"></i> <span>Chart of Accounts</span>
                        </a>
                        <a href="{{ route('journal-entries.index') }}" class="{{ request()->routeIs('journal-entries.*') ? 'active' : '' }}">
                            <i class="fe fe-file-text"></i> <span>Journal Vouchers</span>
                        </a>
                        <a href="{{ route('ledger.index') }}" class="{{ request()->routeIs('ledger.*') ? 'active' : '' }}">
                            <i class="fe fe-book"></i> <span>General Ledger</span>
                        </a>
                        <a href="{{ route('trial-balance.index') }}" class="{{ request()->routeIs('trial-balance.*') ? 'active' : '' }}">
                            <i class="fe fe-check-square"></i> <span>Trial Balance</span>
                        </a>
                        <a href="{{ route('reports.profit-loss') }}" class="{{ request()->routeIs('reports.profit-loss*') ? 'active' : '' }}">
                            <i class="fe fe-trending-up"></i> <span>Profit & Loss (P&L)</span>
                        </a>
                        <a href="{{ route('reports.balance-sheet') }}" class="{{ request()->routeIs('reports.balance-sheet*') ? 'active' : '' }}">
                            <i class="fe fe-bar-chart-2"></i> <span>Balance Sheet</span>
                        </a>
                        <a href="{{ route('reports.cash-flow') }}" class="{{ request()->routeIs('reports.cash-flow*') ? 'active' : '' }}">
                            <i class="fe fe-dollar-sign"></i> <span>Cash Flow Statement</span>
                        </a>
                        <a href="{{ route('contra-entries.index') }}" class="{{ request()->routeIs('contra-entries.*') ? 'active' : '' }}">
                            <i class="fe fe-repeat"></i> <span>Contra Transfers</span>
                        </a>
                        <a href="{{ route('reconciliation.index') }}" class="{{ request()->routeIs('reconciliation.*') ? 'active' : '' }}">
                            <i class="fe fe-check-circle"></i> <span>Bank Reconciliation</span>
                        </a>
                        <a href="{{ route('fiscal-years.index') }}" class="{{ request()->routeIs('fiscal-years.*') ? 'active' : '' }}">
                            <i class="fe fe-calendar"></i> <span>Fiscal Years & Closing</span>
                        </a>
                    </li>
                @endif

                <!-- 13. Employee Portal -->
                @if(auth()->check() && (auth()->user()->hasRole(['Employee', 'employee']) || auth()->user()->employee))
                    <li class="menu-title"><span>Employee Portal</span></li>
                    <li>
                        <a href="{{ route('employee.tada.index') }}" class="{{ request()->routeIs('employee.tada.index') ? 'active' : '' }}">
                            <i class="fe fe-list"></i> <span>My TA/DA List</span>
                        </a>
                        <a href="{{ route('employee.tada.create') }}" class="{{ request()->routeIs('employee.tada.create') ? 'active' : '' }}">
                            <i class="fe fe-upload"></i> <span>Submit TA/DA</span>
                        </a>
                    </li>
                @endif

                <!-- 14. Cost & Company Details -->
                @if($canView('Cost Management') || $canView('Company Management'))
                    <li class="menu-title"><span>Company & Cost Config</span></li>
                    <li>
                        @if($canView('Company Management'))
                            <a href="{{ route('company-details.index') }}" class="{{ request()->routeIs('company-details.*') ? 'active' : '' }}">
                                <i class="fe fe-briefcase"></i> <span> Company Details</span>
                            </a>
                        @endif
                        @if($canView('Cost Management'))
                            <a href="{{ route('cost-categories.index') }}" class="{{ request()->routeIs('cost-categories.*') ? 'active' : '' }}">
                                <i class="fe fe-tag"></i> <span> Cost Categories</span>
                            </a>
                        @endif
                    </li>
                @endif

                <!-- 15. Reports & Analytics -->
                @if($canView('Report Management'))
                    <li class="menu-title"><span>Reports & Analytics</span></li>
                    <li>
                        <a href="{{ route('sales.report') }}" class="{{ request()->routeIs('sales.report') ? 'active' : '' }}">
                            <i class="fe fe-file-text"></i> <span> Sales Report</span>
                        </a>
                        <a href="{{ route('purchase.report') }}" class="{{ request()->routeIs('purchase.report') ? 'active' : '' }}">
                            <i class="fe fe-file-text"></i> <span> Purchase Report</span>
                        </a>
                        <a href="{{ route('revenues.index') }}" class="{{ request()->routeIs('revenues.*') ? 'active' : '' }}">
                            <i class="fe fe-bar-chart-2"></i> <span> Revenue Report</span>
                        </a>
                    </li>
                @endif

                <!-- 16. System Authorization & Users -->
                @if($canView('Administration'))
                    <li class="menu-title"><span>System & Security</span></li>
                    <li>
                        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fe fe-user"></i> <span> Users</span>
                        </a>
                        <a href="{{ route('role.index') }}" class="{{ request()->routeIs('role.*') ? 'active' : '' }}">
                            <i class="fe fe-shield"></i> <span> Roles</span>
                        </a>
                        <a href="{{ route('permission.index') }}" class="{{ request()->routeIs('permission.*') ? 'active' : '' }}">
                            <i class="fe fe-lock"></i> <span> Permissions</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function scrollToActiveSidebar() {
        var activeLink = document.querySelector('#sidebar .sidebar-inner a.active') || document.querySelector('#sidebar a.active');
        if (activeLink) {
            var sidebarInner = document.querySelector('#sidebar .sidebar-inner');
            if (sidebarInner) {
                var activeRect = activeLink.getBoundingClientRect();
                var sidebarRect = sidebarInner.getBoundingClientRect();
                var currentScroll = sidebarInner.scrollTop;
                var targetScroll = Math.max(0, (activeRect.top - sidebarRect.top + currentScroll) - (sidebarInner.clientHeight / 2) + (activeLink.clientHeight / 2));
                
                if (window.jQuery && typeof jQuery.fn.slimScroll !== 'undefined') {
                    jQuery('#sidebar .sidebar-inner').slimScroll({ scrollTo: targetScroll + 'px' });
                }
                sidebarInner.scrollTop = targetScroll;
            }
            
            // Expand parent submenu if nested
            var parentSubmenu = activeLink.closest('li.submenu');
            if (parentSubmenu) {
                parentSubmenu.classList.add('active');
                var subUl = parentSubmenu.querySelector('ul');
                if (subUl) {
                    subUl.style.display = 'block';
                }
            }
        }
    }

    setTimeout(scrollToActiveSidebar, 100);
    setTimeout(scrollToActiveSidebar, 350);
});
</script>