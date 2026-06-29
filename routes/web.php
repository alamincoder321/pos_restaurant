<?php

use App\Http\Controllers\AccountHeadController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationtController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\InvestController;
use App\Http\Controllers\InvestTransactionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiveController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::fallback(function () {
    return view('error.404');
})->middleware('auth');


// user login route
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login.show');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [DashboardController::class, 'Logout'])->middleware('auth')->name('logout');

//company profile update
Route::get('/companyProfile', [DashboardController::class, 'companyProfile'])->name('companyProfile');
Route::get('/get-companyProfile', [DashboardController::class, 'getcompanyProfile'])->name('getcompanyProfile');
Route::post('/update-companyProfile', [DashboardController::class, 'updatecompanyProfile'])->name('update.companyProfile');
Route::get('/get-headerInfo', [DashboardController::class, 'getHeaderInfo'])->name('get.headerInfo');

//panel and dashboard route
Route::group(['prefix' => 'panel'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/{panel}', [DashboardController::class, 'panel'])->name('panel.access');
});
Route::get('/business-info', [DashboardController::class, 'businessInfo'])->name('business.info');
Route::match(['get', 'post'], 'get-business-info', [DashboardController::class, 'getBusinessInfo'])->name('get.business.info');
Route::match(['get', 'post'], 'get-top-business-info', [DashboardController::class, 'getTopBusinessInfo'])->name('get.top.business.info');

// ============================= Control Panel Route ==============================
Route::get('/branchset/{id}', [DashboardController::class, 'branchset'])->name('set.branch');
// branch route
Route::get('/branch', [BranchController::class, 'create'])->name('branch.create');
Route::match(['get', 'post'], '/get-branch', [BranchController::class, 'index'])->name('branch.index');
Route::post('/branch', [BranchController::class, 'store'])->name('branch.store');
Route::post('/update-branch', [BranchController::class, 'update'])->name('branch.update');
Route::post('/delete-branch', [BranchController::class, 'destroy'])->name('branch.delete');

// user route
Route::get('/user', [UserController::class, 'create'])->name('user.create');
Route::get('/user-profile', [UserController::class, 'profile'])->name('user.profile');
Route::post('/get-user', [UserController::class, 'index'])->name('user.index');
Route::post('/user', [UserController::class, 'store'])->name('user.store');
Route::post('/update-user', [UserController::class, 'update'])->name('user.update');
Route::post('/delete-user', [UserController::class, 'destroy'])->name('user.delete');

// user access route
Route::get('/userAccess/{id}', [UserController::class, 'userAccess'])->name('userAccess.create');
Route::post('/get-userAccess', [UserController::class, 'getUserAccess'])->name('userAccess.index');
Route::post('/save-userAccess', [UserController::class, 'saveUserAccess'])->name('userAccess.index');

// unit route
Route::get('/unit', [UnitController::class, 'create'])->name('unit.create');
Route::post('/get-unit', [UnitController::class, 'index'])->name('unit.index');
Route::post('/unit', [UnitController::class, 'store'])->name('unit.store');
Route::post('/update-unit', [UnitController::class, 'update'])->name('unit.update');
Route::post('/delete-unit', [UnitController::class, 'destroy'])->name('unit.delete');

// category route
Route::get('/category', [CategoryController::class, 'create'])->name('category.create');
Route::match(['get', 'post'], '/get-category', [CategoryController::class, 'index'])->name('category.index');
Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
Route::post('/update-category', [CategoryController::class, 'update'])->name('category.update');
Route::post('/delete-category', [CategoryController::class, 'destroy'])->name('category.delete');

// floor route
Route::get('/floor', [FloorController::class, 'create'])->name('floor.create');
Route::match(['get', 'post'], '/get-floor', [FloorController::class, 'index'])->name('floor.index');
Route::post('/floor', [FloorController::class, 'store'])->name('floor.store');
Route::post('/update-floor', [FloorController::class, 'update'])->name('floor.update');
Route::post('/delete-floor', [FloorController::class, 'destroy'])->name('floor.delete');

// table route
Route::get('/table', [TableController::class, 'create'])->name('table.create');
Route::match(['get', 'post'], '/get-table', [TableController::class, 'index'])->name('table.index');
Route::post('/table', [TableController::class, 'store'])->name('table.store');
Route::post('/update-table', [TableController::class, 'update'])->name('table.update');
Route::post('/delete-table', [TableController::class, 'destroy'])->name('table.delete');

// brand route
Route::get('/brand', [BrandController::class, 'create'])->name('brand.create');
Route::match(['get', 'post'], '/get-brand', [BrandController::class, 'index'])->name('brand.index');
Route::post('/brand', [BrandController::class, 'store'])->name('brand.store');
Route::post('/update-brand', [BrandController::class, 'update'])->name('brand.update');
Route::post('/delete-brand', [BrandController::class, 'destroy'])->name('brand.delete');

// area route
Route::get('/area', [AreaController::class, 'create'])->name('area.create');
Route::match(['get', 'post'], '/get-area', [AreaController::class, 'index'])->name('area.index');
Route::post('/area', [AreaController::class, 'store'])->name('area.store');
Route::post('/update-area', [AreaController::class, 'update'])->name('area.update');
Route::post('/delete-area', [AreaController::class, 'destroy'])->name('area.delete');


// customer route
Route::get('/customer', [CustomerController::class, 'create'])->name('customer.create');
Route::get('/customerList', [CustomerController::class, 'customerList'])->name('customer.list');
Route::match(['get', 'post'], '/get-customer', [CustomerController::class, 'index'])->name('customer.index');
Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
Route::post('/update-customer', [CustomerController::class, 'update'])->name('customer.update');
Route::post('/delete-customer', [CustomerController::class, 'destroy'])->name('customer.delete');
Route::get('/customerDue', [CustomerController::class, 'customerDue'])->name('customer.due');
Route::post('/get-customerDue', [CustomerController::class, 'getCustomerDue'])->name('get.customer.due');
Route::get('/customerLedger', [CustomerController::class, 'customerLedger'])->name('customer.ledger');
Route::post('/get-customer-ledger', [CustomerController::class, 'getCustomerLedger'])->name('get.customer.ledger');

// menu route
Route::get('/menu', [MenuController::class, 'create'])->name('menu.create');
Route::get('/menuList', [MenuController::class, 'menuList'])->name('menu.list');
Route::get('/barcode/{id}', [MenuController::class, 'barcode'])->name('menu.barcode');
Route::match(['get', 'post'], '/get-menu', [MenuController::class, 'index'])->name('menu.index');
Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
Route::post('/update-menu', [MenuController::class, 'update'])->name('menu.update');
Route::post('/delete-menu', [MenuController::class, 'destroy'])->name('menu.delete');

// stock route
Route::get('/stock', [MenuController::class, 'stock'])->name('menu.stock');
Route::post('/get-currentStock', [MenuController::class, 'getMenuStock'])->name('get.menu.stock');
Route::get('/menuLedger', [MenuController::class, 'menuLedger'])->name('menu.ledger');
Route::post('/get-menu-ledger', [MenuController::class, 'getMenuLedger'])->name('get.menu.ledger');


// ======================================== HR Panel =====================================

// department route
Route::get('/department', [DepartmentController::class, 'create'])->name('department.create');
Route::match(['get', 'post'], '/get-department', [DepartmentController::class, 'index'])->name('department.index');
Route::post('/department', [DepartmentController::class, 'store'])->name('department.store');
Route::post('/update-department', [DepartmentController::class, 'update'])->name('department.update');
Route::post('/delete-department', [DepartmentController::class, 'destroy'])->name('department.delete');

// designation route
Route::get('/designation', [DesignationtController::class, 'create'])->name('designation.create');
Route::match(['get', 'post'], '/get-designation', [DesignationtController::class, 'index'])->name('designation.index');
Route::post('/designation', [DesignationtController::class, 'store'])->name('designation.store');
Route::post('/update-designation', [DesignationtController::class, 'update'])->name('designation.update');
Route::post('/delete-designation', [DesignationtController::class, 'destroy'])->name('designation.delete');

// employee route
Route::get('/employee', [EmployeeController::class, 'create'])->name('employee.create');
Route::get('/employeeList', [EmployeeController::class, 'employeeList'])->name('employee.list');
Route::match(['get', 'post'], '/get-employee', [EmployeeController::class, 'index'])->name('employee.index');
Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
Route::post('/update-employee', [EmployeeController::class, 'update'])->name('employee.update');
Route::post('/delete-employee', [EmployeeController::class, 'destroy'])->name('employee.delete');

//salary generate route
Route::get('/salary', [SalaryController::class, 'create'])->name('salary.create');
Route::get('/salaryList', [SalaryController::class, 'salaryList'])->name('salary.list');
Route::post('/check-salary', [SalaryController::class, 'checkSalary'])->name('salary.check');
Route::post('/get-salary', [SalaryController::class, 'index'])->name('salary.index');
Route::post('/salary', [SalaryController::class, 'store'])->name('salary.store');
Route::post('/update-salary', [SalaryController::class, 'update'])->name('salary.update');
Route::post('/delete-salary', [SalaryController::class, 'destroy'])->name('salary.delete');


// ============================= Account Panel Route ==============================
// account head route
Route::get('/accounthead', [AccountHeadController::class, 'create'])->name('accounthead.create');
Route::match(['get', 'post'], '/get-accounthead', [AccountHeadController::class, 'index'])->name('accounthead.index');
Route::post('/accounthead', [AccountHeadController::class, 'store'])->name('accounthead.store');
Route::post('/update-accounthead', [AccountHeadController::class, 'update'])->name('accounthead.update');
Route::post('/delete-accounthead', [AccountHeadController::class, 'destroy'])->name('accounthead.delete');

Route::get('/cashLedger', [AccountHeadController::class, 'cashLedger'])->name('cash.ledger');
Route::post('/get-cash-ledger', [AccountHeadController::class, 'getCashLedger'])->name('get.cash.ledger');

// bank route
Route::get('/bank', [BankController::class, 'create'])->name('bank.create');
Route::match(['get', 'post'], '/get-bank', [BankController::class, 'index'])->name('bank.index');
Route::post('/bank', [BankController::class, 'store'])->name('bank.store');
Route::post('/update-bank', [BankController::class, 'update'])->name('bank.update');
Route::post('/delete-bank', [BankController::class, 'destroy'])->name('bank.delete');
Route::get('/bankLedger', [BankController::class, 'bankLedger'])->name('bank.ledger');
Route::post('/get-bank-ledger', [BankController::class, 'getBankLedger'])->name('get.bank.ledger');
Route::post('/get-bankBalance', [BankController::class, 'getBankBalance'])->name('get.bank.balance');

// expense route
Route::get('/expense', [TransactionController::class, 'expense'])->name('expense.create');
Route::get('/income', [TransactionController::class, 'income'])->name('income.create');
Route::match(['get', 'post'], '/get-transaction', [TransactionController::class, 'index'])->name('transaction.index');
Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
Route::post('/update-transaction', [TransactionController::class, 'update'])->name('transaction.update');
Route::post('/delete-transaction', [TransactionController::class, 'destroy'])->name('transaction.delete');

// bankTransaction route
Route::get('/bankTransaction', [BankTransactionController::class, 'create'])->name('bankTransaction.create');
Route::match(['get', 'post'], '/get-bankTransaction', [BankTransactionController::class, 'index'])->name('bankTransaction.index');
Route::post('/bankTransaction', [BankTransactionController::class, 'store'])->name('bankTransaction.store');
Route::post('/update-bankTransaction', [BankTransactionController::class, 'update'])->name('bankTransaction.update');
Route::post('/delete-bankTransaction', [BankTransactionController::class, 'destroy'])->name('bankTransaction.delete');

// receive route
Route::get('/receive', [ReceiveController::class, 'create'])->name('receive.create');
Route::match(['get', 'post'], '/get-receive', [ReceiveController::class, 'index'])->name('receive.index');
Route::post('/receive', [ReceiveController::class, 'store'])->name('receive.store');
Route::post('/update-receive', [ReceiveController::class, 'update'])->name('receive.update');
Route::post('/delete-receive', [ReceiveController::class, 'destroy'])->name('receive.delete');

// payment route
Route::get('/payment', [PaymentController::class, 'create'])->name('payment.create');
Route::match(['get', 'post'], '/get-payment', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');
Route::post('/update-payment', [PaymentController::class, 'update'])->name('payment.update');
Route::post('/delete-payment', [PaymentController::class, 'destroy'])->name('payment.delete');

// ================================ Investment Panel Route =================================
// invest route
Route::get('/invest', [InvestController::class, 'create'])->name('invest.create');
Route::match(['get', 'post'], '/get-invest', [InvestController::class, 'index'])->name('invest.index');
Route::post('/invest', [InvestController::class, 'store'])->name('invest.store');
Route::post('/update-invest', [InvestController::class, 'update'])->name('invest.update');
Route::post('/delete-invest', [InvestController::class, 'destroy'])->name('invest.delete');

// invest transaction route
Route::get('/invest-transaction', [InvestTransactionController::class, 'create'])->name('invest.transaction.create');
Route::get('/invest-transaction-list', [InvestTransactionController::class, 'list'])->name('invest.transaction.list');
Route::match(['get', 'post'], '/get-invest-transaction', [InvestTransactionController::class, 'index'])->name('invest.transaction.index');
Route::post('/invest-transaction', [InvestTransactionController::class, 'store'])->name('invest.transaction.store');
Route::post('/update-invest-transaction', [InvestTransactionController::class, 'update'])->name('invest.transaction.update');
Route::post('/delete-invest-transaction', [InvestTransactionController::class, 'destroy'])->name('invest.transaction.delete');


// ============================= Sale Panel Route ==============================
// sale route
Route::get('/pos/{id?}', [SaleController::class, 'pos'])->name('pos.sale.create');
Route::get('/sale/{id?}', [SaleController::class, 'create'])->name('sale.create');
Route::get('/sale-record', [SaleController::class, 'saleRecord'])->name('sale.record');
Route::get('/pending-sale-record', [SaleController::class, 'pendingSaleRecord'])->name('sale.pending.record');
Route::match(['get', 'post'], '/get-sale', [SaleController::class, 'index'])->name('sale.index');
Route::match(['get', 'post'], '/sale-status-change', [SaleController::class, 'statusChange'])->name('sale.status.change');
Route::post('/sale', [SaleController::class, 'store'])->name('sale.store');
Route::post('/update-sale', [SaleController::class, 'update'])->name('sale.update');
Route::post('/delete-sale', [SaleController::class, 'destroy'])->name('sale.delete');
Route::get('/saleInvoice/{id}', [SaleController::class, 'saleInvoice'])->name('sale.invoice');
Route::get('/possaleInvoice/{id}', [SaleController::class, 'possaleInvoice'])->name('possale.invoice');
Route::get('/kitchenInvoice/{id}', [SaleController::class, 'kitchenInvoice'])->name('kitchen.invoice');

// sale return route
Route::post('/get-sale-detailforreturns', [SaleReturnController::class, 'getDetailForReturns'])->name('get.sale.detailforreturns');
Route::get('/sale-return', [SaleReturnController::class, 'create'])->name('sale.return.create');
Route::match(['get', 'post'], '/get-sale-return', [SaleReturnController::class, 'index'])->name('sale.return.index');
Route::post('/sale-return', [SaleReturnController::class, 'store'])->name('sale.return.store');
Route::get('/sale-return-record', [SaleReturnController::class, 'saleReturnRecord'])->name('sale.return.record');
Route::post('/delete-sale-return', [SaleReturnController::class, 'destroy'])->name('sale.return.delete');

// ============================= Report Panel Route ==============================
Route::get('/profitLoss', [ReportController::class, 'profitLoss'])->name('profitLoss');
Route::post('/get-other-expense-income', [ReportController::class, 'getOtherExpenseIncome'])->name('get.other.expense.income');

Route::get('/dailyReport', [ReportController::class, 'dailyReport'])->name('dailyReport');
Route::post('/get-dailyReport', [ReportController::class, 'getDailyReport'])->name('get.dailyReport');

// daybook route
Route::post('/get-opening-closing-balance', [ReportController::class, 'openingClosingBalance'])->name('opening.closing.balance');
Route::get('/daybook', [ReportController::class, 'daybook'])->name('daybook');


Route::get('/pageSet/{page}', function ($page) {
    Session::put('sale_page', $page);
    return redirect()->route('sale.create');
});
