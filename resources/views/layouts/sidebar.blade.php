@php
$panel = session('panel');
@endphp

<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        @if ($panel == 'dashboard' || $panel == '')
        <li class="nav-item">
            <a class="nav-link {{Request::is('panel/dashboard') ? 'active' : ''}}" href="/">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/panel/OrderPanel">
                <i class="bi bi-cart-dash"></i>
                <span>Order Panel</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/panel/AccountPanel">
                <i class="bi bi-cash"></i>
                <span>Accounts Panel</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/panel/HRPanel">
                <i class="bi bi-people"></i>
                <span>HR Panel</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/panel/ReportPanel">
                <i class="bi bi-calendar-check"></i>
                <span>Reports Panel</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/panel/ControlPanel">
                <i class="bi bi-bank2"></i>
                <span>Control Panel</span>
            </a>
        </li>
        @if(checkAccess('businessInfo'))
        <li class="nav-item">
            <a class="nav-link" href="/business-info">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Business Info</span>
            </a>
        </li>
        @endif

        @elseif($panel == 'OrderPanel')
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link panel-link" href="/panel/OrderPanel">
                <span>Order Panel</span>
            </a>
        </li>
        @if(checkAccess('pos'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('pos') ? 'active' : ''}}" href="/pos">
                <i class="bi bi-cart-dash"></i>
                <span>Order Entry</span>
            </a>
        </li>
        @endif
        @if(checkAccess('pendingsaleRecord'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('pending-sale-record') ? 'active' : ''}}" href="/pending-sale-record">
                <i class="bi bi-file-text"></i>
                <span>Pending Order Record</span>
            </a>
        </li>
        @endif
        @if(checkAccess('saleRecord'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('sale-record') ? 'active' : ''}}" href="/sale-record">
                <i class="bi bi-file-text"></i>
                <span>Order Record</span>
            </a>
        </li>
        @endif
        @if(checkAccess('saleReturn'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('sale-return') ? 'active' : ''}}" href="/sale-return">
                <i class="bi bi-arrow-return-left"></i>
                <span>Order Return Entry</span>
            </a>
        </li>
        @endif
        @if(checkAccess('saleReturnRecord'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('sale-return-record') ? 'active' : ''}}" href="/sale-return-record">
                <i class="bi bi-file-text"></i>
                <span>Order Return Record</span>
            </a>
        </li>
        @endif
        
        @if(checkAccess('dailyReport'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('dailyReport') ? 'active' : ''}}" href="/dailyReport">
                <i class="bi bi-book"></i>
                <span>Daily Report</span>
            </a>
        </li>
        @endif

        @elseif($panel == 'AccountPanel')
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link panel-link" href="/panel/AccountPanel">
                <span>Account Panel</span>
            </a>
        </li>
        @if(checkAccess('expense'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('expense') ? 'active' : ''}}" href="/expense">
                <i class="bi bi-clipboard-minus"></i>
                <span>Expense Entry</span>
            </a>
        </li>
        @endif
        @if(checkAccess('income'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('income') ? 'active' : ''}}" href="/income">
                <i class="bi bi-duffle"></i>
                <span>Income Entry</span>
            </a>
        </li>
        @endif
        @if(checkAccess('receive'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('receive') ? 'active' : ''}}" href="/receive">
                <i class="bi bi-cash-stack"></i>
                <span>Receive</span>
            </a>
        </li>
        @endif
        @if(checkAccess('payment'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('payment') ? 'active' : ''}}" href="/payment">
                <i class="bi bi-person-workspace"></i>
                <span>Payment</span>
            </a>
        </li>
        @endif

        @if(checkAccess('bankTransaction'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('bankTransaction') ? 'active' : ''}}" href="/bankTransaction">
                <i class="bi bi-bank"></i>
                <span>Bank Transaction</span>
            </a>
        </li>
        @endif

        @if(checkAccess('bankTransactionRecord'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('bankTransactionRecord') ? 'active' : ''}}" href="">
                <i class="bi bi-list-ul"></i>
                <span>Bank Transaction Record</span>
            </a>
        </li>
        @endif

        @if(checkAccess('accounthead'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('accounthead') ? 'active' : ''}}" href="/accounthead">
                <i class="bi bi-plus-circle"></i>
                <span>AccountHead Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('bank'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('bank') ? 'active' : ''}}" href="/bank">
                <i class="bi bi-plus-circle"></i>
                <span>Bank Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('investTransaction'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('invest-transaction') ? 'active' : ''}}" href="/invest-transaction">
                <i class="bi bi-plus-circle"></i>
                <span>InvestTransaction</span>
            </a>
        </li>
        @endif
        
        @if(checkAccess('investTransactionList'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('invest-transaction-list') ? 'active' : ''}}" href="/invest-transaction-list">
                <i class="bi bi-list"></i>
                <span>InvestTransaction List</span>
            </a>
        </li>
        @endif

        @if(checkAccess('invest'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('invest') ? 'active' : ''}}" href="/invest">
                <i class="bi bi-plus-circle"></i>
                <span>InvestAccount Entry</span>
            </a>
        </li>
        @endif

        @elseif($panel == 'ReportPanel')
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link panel-link" href="/panel/ReportPanel">
                <span>Report Panel</span>
            </a>
        </li>

        @if(checkAccess('profitLoss'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('profitLoss') ? 'active' : ''}}" href="/profitLoss">
                <i class="bi bi-journal-text"></i>
                <span>Profit/Loss</span>
            </a>
        </li>
        @endif

        @if(checkAccess('cashLedger'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('cashLedger') ? 'active' : ''}}" href="/cashLedger">
                <i class="bi bi-list"></i>
                <span>Cash Ledger</span>
            </a>
        </li>
        @endif

        @if(checkAccess('bankLedger'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('bankLedger') ? 'active' : ''}}" href="/bankLedger">
                <i class="bi bi-list"></i>
                <span>Bank Ledger</span>
            </a>
        </li>
        @endif

        @if(checkAccess('customerDue'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('customerDue') ? 'active' : ''}}" href="/customerDue">
                <i class="bi bi-cash"></i>
                <span>Customer Due</span>
            </a>
        </li>
        @endif

        @if(checkAccess('customerLedger'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('customerLedger') ? 'active' : ''}}" href="/customerLedger">
                <i class="bi bi-list"></i>
                <span>Customer Ledger</span>
            </a>
        </li>
        @endif

        @if(checkAccess('dailyReport'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('dailyReport') ? 'active' : ''}}" href="/dailyReport">
                <i class="bi bi-book"></i>
                <span>Daily Report</span>
            </a>
        </li>
        @endif

        @elseif($panel == 'HRPanel')
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link panel-link" href="/panel/HRPanel">
                <span>HR Panel</span>
            </a>
        </li>
        @if(checkAccess('salary'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('salary') ? 'active' : ''}}" href="/salary">
                <i class="bi bi-receipt"></i>
                <span>Salary Generate</span>
            </a>
        </li>
        @endif
        @if(checkAccess('salaryList'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('salaryList') ? 'active' : ''}}" href="/salaryList">
                <i class="bi bi-file-text"></i>
                <span>Salary Record</span>
            </a>
        </li>
        @endif

        @if(checkAccess('employee'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('employee') ? 'active' : ''}}" href="/employee">
                <i class="bi bi-people"></i>
                <span>Employee Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('employeeList'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('employeeList') ? 'active' : ''}}" href="/employeeList">
                <i class="bi bi-list-ul"></i>
                <span>Employee List</span>
            </a>
        </li>
        @endif

        @if(checkAccess('department'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('department') ? 'active' : ''}}" href="/department">
                <i class="bi bi-plus-circle"></i>
                <span>Department Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('designation'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('designation') ? 'active' : ''}}" href="/designation">
                <i class="bi bi-plus-circle"></i>
                <span>Designation Entry</span>
            </a>
        </li>
        @endif

        @elseif($panel == 'ControlPanel')
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link panel-link" href="/panel/ControlPanel">
                <span>Control Panel</span>
            </a>
        </li>
        @if(checkAccess('menu'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('menu') ? 'active' : ''}}" href="/menu">
                <i class="bi bi-plus-circle"></i>
                <span>Menu Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('menuList'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('menuList') ? 'active' : ''}}" href="/menuList">
                <i class="bi bi-list-ul"></i>
                <span>Menu List</span>
            </a>
        </li>
        @endif

        @if(checkAccess('customer'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('customer') ? 'active' : ''}}" href="/customer">
                <i class="bi bi-person"></i>
                <span>Customer Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('customerList'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('customerList') ? 'active' : ''}}" href="/customerList">
                <i class="bi bi-list-ul"></i>
                <span>Customer List</span>
            </a>
        </li>
        @endif

        @if(checkAccess('area'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('area') ? 'active' : ''}}" href="/area">
                <i class="bi bi-globe"></i>
                <span>Area Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('unit'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('unit') ? 'active' : ''}}" href="/unit">
                <i class="bi bi-box"></i>
                <span>Unit Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('category'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('category') ? 'active' : ''}}" href="/category">
                <i class="bi bi-tags"></i>
                <span>Category Entry</span>
            </a>
        </li>
        @endif
        
        @if(checkAccess('floor'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('floor') ? 'active' : ''}}" href="/floor">
                <i class="bi bi-plus-circle"></i>
                <span>Floor Entry</span>
            </a>
        </li>
        @endif
        
        @if(checkAccess('table'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('table') ? 'active' : ''}}" href="/table">
                <i class="bi bi-table"></i>
                <span>Table Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('user'))
        <li class="nav-item">
            <a class="nav-link {{Request::is('user') ? 'active' : ''}}" href="/user">
                <i class="bi bi-person-fill-add"></i>
                <span>User Entry</span>
            </a>
        </li>
        @endif

        @if(checkAccess('companyProfile'))
        <li class="nav-item">
            <a class="nav-link" href="/companyProfile">
                <i class="bi bi-house-fill"></i>
                <span>Company Profile</span>
            </a>
        </li>
        @endif
        @endif
    </ul>

</aside>