@extends('master')
@section('title', 'Daybook Report')
@section('breadcrumb', 'Daybook Report')
@push('style')
<style scoped>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
    }

    .v-select .dropdown-toggle {
        width: 250px !important;
    }

    .v-select .dropdown-menu {
        width: 350px !important;
        overflow-y: hidden !important;
    }

    tr th,
    tr td {
        vertical-align: top !important;
    }
</style>
@endpush
@section('content')
<div id="daybook">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showReport" class="form-inline">
                        <div class="form-group">
                            <label for="dateFrom">From</label>
                            <input type="date" class="form-control" id="dateFrom" v-model="filter.dateFrom" />
                        </div>
                        <div class="form-group">
                            <label for="dateTo">To</label>
                            <input type="date" class="form-control" id="dateTo" v-model="filter.dateTo" />
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm">Show</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2" :class="isLoading == false ? '' : 'd-none'" v-if="isLoading == false">
        <div class="col-12 text-center">
            Loading...
        </div>
    </div>
    <div class="row mt-2" :class="isLoading ? '' : 'd-none'" v-if="isLoading">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body pt-1 pb-3 px-2">
                    <div class="text-end">
                        <a href="" @click.prevent="print" title="Print"><i class="bi bi-printer"></i></a>
                    </div>
                    <div id="reportContent" style="overflow-x: auto;">
                        <table class="table table-bordered table-hover">
                            <tr>
                                <th class="text-center" style="padding: 8px 5px !important;" colspan="2">In</th>
                                <th></th>
                                <th class="text-center" style="padding: 8px 5px !important;" colspan="2">Out</th>
                            </tr>
                            <tr>
                                <td><strong>Opening Cash Bank Balance</strong></td>
                                <td class="text-center"><strong>@{{openingBalance | formatCurrency }}</strong></td>

                                <td></td>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td><strong>Sales Receipt</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalSale | formatCurrency }}</strong></td>

                                <td></td>

                                <td><strong>Purchase Paid</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalPurchase | formatCurrency }}</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table table-bordered" :class="sales.length > 0 ? '' : 'd-none'" v-if="sales.length > 0">
                                        <tr>
                                            <td class="text-center">Customer</td>
                                            <td class="text-center">Received</td>
                                        </tr>
                                        <tr v-for="sale in sales" :key="sale.id">
                                            <td class="text-center">@{{ sale.customer_name }}</td>
                                            <td class="text-end">@{{ parseFloat(sale.cashPaid - sale.returnAmount).toFixed(2) }}</td>
                                        </tr>
                                    </table>
                                </td>

                                <td></td>

                                <td>
                                    <table class="table table-bordered" :class="purchases.length > 0 ? '' : 'd-none'" v-if="purchases.length > 0">
                                        <tr>
                                            <td class="text-center">Supplier</td>
                                            <td class="text-center">Paid</td>
                                        </tr>
                                        <tr v-for="purchase in purchases" :key="purchase.id">
                                            <td class="text-center">@{{ purchase.supplier_name }}</td>
                                            <td class="text-end">@{{ purchase.paid }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- receive customer and payment supplier section -->
                            <tr>
                                <td><strong>Receipts From Customers</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalCustomerReceipts | formatCurrency }}</strong></td>

                                <td></td>

                                <td><strong>Payments To Suppliers</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalSupplierPayments | formatCurrency }}</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table table-bordered" :class="customerReceipts.length > 0 ? '' : 'd-none'" v-if="customerReceipts.length > 0">
                                        <tr>
                                            <td class="text-center">Customer</td>
                                            <td class="text-center">Received</td>
                                        </tr>
                                        <tr v-for="receipt in customerReceipts" :key="receipt.id">
                                            <td class="text-center">@{{ receipt.customer?.name }}</td>
                                            <td class="text-end">@{{ receipt.amount }}</td>
                                        </tr>
                                    </table>
                                </td>

                                <td></td>

                                <td>
                                    <table class="table table-bordered" :class="supplierPayments.length > 0 ? '' : 'd-none'" v-if="supplierPayments.length > 0">
                                        <tr>
                                            <td class="text-center">Supplier</td>
                                            <td class="text-center">Paid</td>
                                        </tr>
                                        <tr v-for="payment in supplierPayments" :key="payment.id">
                                            <td class="text-center">@{{ payment.supplier?.name }}</td>
                                            <td class="text-end">@{{ payment.amount }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- receive supplier and payment customer section -->
                            <tr>
                                <td><strong>Receipts From Suppliers</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalSupplierReceipts | formatCurrency }}</strong></td>

                                <td></td>

                                <td><strong>Payments To Customers</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalCustomerPayments | formatCurrency }}</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table table-bordered" :class="supplierReceipts.length > 0 ? '' : 'd-none'" v-if="supplierReceipts.length > 0">
                                        <tr>
                                            <td class="text-center">Supplier</td>
                                            <td class="text-center">Received</td>
                                        </tr>
                                        <tr v-for="receipt in supplierReceipts" :key="receipt.id">
                                            <td class="text-center">@{{ receipt.supplier?.name }}</td>
                                            <td class="text-end">@{{ receipt.amount }}</td>
                                        </tr>
                                    </table>
                                </td>

                                <td></td>

                                <td>
                                    <table class="table table-bordered" :class="customerPayments.length > 0 ? '' : 'd-none'" v-if="customerPayments.length > 0">
                                        <tr>
                                            <td class="text-center">Customer</td>
                                            <td class="text-center">Paid</td>
                                        </tr>
                                        <tr v-for="payment in customerPayments" :key="payment.id">
                                            <td class="text-center">@{{ payment.customer?.name }}</td>
                                            <td class="text-end">@{{ payment.amount }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- expense and income section -->
                            <tr>
                                <td><strong>Income Total</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalIncome | formatCurrency }}</strong></td>

                                <td></td>

                                <td><strong>Expense Total</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalExpense | formatCurrency }}</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table table-bordered" :class="incomes.length > 0 ? '' : 'd-none'" v-if="incomes.length > 0">
                                        <tr>
                                            <td class="text-center">Account</td>
                                            <td class="text-center">Received</td>
                                        </tr>
                                        <tr v-for="income in incomes" :key="income.id">
                                            <td class="text-center">@{{ income.account?.name }}</td>
                                            <td class="text-end">@{{ income.amount }}</td>
                                        </tr>
                                    </table>
                                </td>

                                <td></td>

                                <td>
                                    <table class="table table-bordered" :class="expenses.length > 0 ? '' : 'd-none'" v-if="expenses.length > 0">
                                        <tr>
                                            <td class="text-center">Account</td>
                                            <td class="text-center">Amount</td>
                                        </tr>
                                        <tr v-for="expense in expenses" :key="expense.id">
                                            <td class="text-center">@{{ expense.account?.name }}</td>
                                            <td class="text-end">@{{ expense.amount }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Investment receipts and payments section -->
                            <tr>
                                <td><strong>Investment Receipts Total</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalInvestmentReceipts | formatCurrency }}</strong></td>

                                <td></td>

                                <td><strong>Investment Payments Total</strong></td>
                                <td rowspan="2" class="text-center"><strong>@{{totalInvestmentPayments | formatCurrency }}</strong></td>
                            </tr>                            
                            <tr>
                                <td>
                                    <table class="table table-bordered" :class="investmentReceipts.length > 0 ? '' : 'd-none'" v-if="investmentReceipts.length > 0">
                                        <tr>
                                            <td class="text-center">Account</td>
                                            <td class="text-center">Received</td>
                                        </tr>
                                        <tr v-for="receipt in investmentReceipts" :key="receipt.id">
                                            <td class="text-center">@{{ receipt.account?.name }}</td>
                                            <td class="text-end">@{{ receipt.amount }}</td>
                                        </tr>
                                    </table>
                                </td>

                                <td></td>

                                <td>
                                    <table class="table table-bordered" :class="investmentPayments.length > 0 ? '' : 'd-none'" v-if="investmentPayments.length > 0">
                                        <tr>
                                            <td class="text-center">Account</td>
                                            <td class="text-center">Amount</td>
                                        </tr>
                                        <tr v-for="payment in investmentPayments" :key="payment.id">
                                            <td class="text-center">@{{ payment.account?.name }}</td>
                                            <td class="text-end">@{{ payment.amount }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>




















                            <tr>
                                <td><strong>Purchase Return Total</strong></td>
                                <td class="text-center"><strong>@{{totalPurchaseReturn | formatCurrency }}</strong></td>

                                <td></td>

                                <td><strong>Sale Return Total</strong></td>
                                <td class="text-center"><strong>@{{totalSaleReturn | formatCurrency }}</strong></td>
                            </tr>

                            <tr>
                                <td><strong>Damage Total</strong></td>
                                <td class="text-center"><strong>@{{totalDamage | formatCurrency }}</strong></td>

                                <td></td>

                                <td><strong>Salary Payment Total</strong></td>
                                <td class="text-center"><strong>@{{totalSalaryPayment | formatCurrency }}</strong></td>
                            </tr>

                            <tr>
                                <td colspan="2"></td>

                                <td></td>

                                <td><strong>Closing Cash Bank Balance</strong></td>
                                <td class="text-center"><strong>@{{closingBalance | formatCurrency }}</strong></td>
                            </tr>

                            <tr>
                                <td class="text-center"><strong>Total</strong></td>
                                <td class="text-center"><strong>@{{totalIn | formatCurrency }}</strong></td>

                                <td></td>

                                <td class="text-center"><strong>Total</strong></td>
                                <td class="text-center"><strong>@{{totalOut | formatCurrency }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script>
    new Vue({
        el: '#daybook',
        data: {
            filter: {
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD')
            },
            openingBalance: 0,
            closingBalance: 0,
            sales: [],
            purchases: [],
            customerReceipts: [],
            supplierReceipts: [],
            supplierPayments: [],
            customerPayments: [],
            incomes: [],
            expenses: [],
            investmentReceipts: [],
            investmentPayments: [],
            damages: [],
            purchaseReturns: [],
            saleReturns: [],
            salaryPayments: [],
            isLoading: true
        },

        computed: {
            totalSale() {
                return this.sales.reduce((pr, cu) => pr + parseFloat(cu.cashPaid - cu.returnAmount), 0);
            },
            totalPurchase() {
                return this.purchases.reduce((pr, cu) => pr + parseFloat(cu.paid), 0);
            },
            totalCustomerReceipts() {
                return this.customerReceipts.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },
            totalSupplierPayments() {
                return this.supplierPayments.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },
            totalSupplierReceipts() {
                return this.supplierReceipts.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },
            totalCustomerPayments() {
                return this.customerPayments.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },
            totalInvestmentReceipts() {
                return this.investmentReceipts.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },
            totalInvestmentPayments() {
                return this.investmentPayments.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },

            totalIncome() {
                return this.incomes.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },

            totalExpense() {
                return this.expenses.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },
            totalPurchaseReturn() {
                return this.purchaseReturns.reduce((pr, cu) => pr + parseFloat(cu.total), 0);
            },
            totalSaleReturn() {
                return this.saleReturns.reduce((pr, cu) => pr + parseFloat(cu.total), 0);
            },
            totalDamage() {
                return this.damages.reduce((pr, cu) => pr + parseFloat(cu.total), 0);
            },
            totalSalaryPayment() {
                return this.salaryPayments.reduce((pr, cu) => pr + parseFloat(cu.amount), 0);
            },

            totalIn() {
                return this.openingBalance + this.totalSale + this.totalCustomerReceipts + this.totalSupplierReceipts + this.totalIncome + this.totalInvestmentReceipts + this.totalDamage + this.totalPurchaseReturn;
            },
            totalOut() {
                return this.closingBalance + this.totalPurchase + this.totalSupplierPayments + this.totalCustomerPayments + this.totalExpense + this.totalInvestmentPayments + this.totalSaleReturn + this.totalSalaryPayment;
            }
        },

        filters: {
            formatCurrency(value) {
                if (typeof value !== "number") {
                    value = parseFloat(value);
                }
                if (isNaN(value)) return "৳ 0.00";
                return "৳ " + value.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        },

        created() {
            this.showReport();
        },

        methods: {
            async showReport() {
                this.isLoading = true;
                await this.getSale();
                await this.getPurchase();
                await this.getCustomerReceipts();
                await this.getSupplierPayments();
                await this.getCustomerPayments();
                await this.getSupplierReceipts();
                await this.getOpeningBalance();
                await this.getClosingBalance();
                await this.getIncomes();
                await this.getExpenses();
                await this.getInvestmentReceipts();
                await this.getInvestmentPayments();
                await this.getDamage();
                await this.getSaleReturn();
                await this.getPurchaseReturn();
                await this.getSalaryPayment();
            },

            async getOpeningBalance() {
                let date = moment(this.filter.dateFrom).subtract(1, 'days').format('YYYY-MM-DD');
                const response = await axios.post("/get-opening-closing-balance", {
                    date: date
                });
                this.openingBalance = response.data.balance;
            },
            async getClosingBalance() {
                const response = await axios.post("/get-opening-closing-balance", {
                    date: this.filter.dateTo
                });
                this.closingBalance = response.data.balance;
            },

            async getPurchase() {
                const {
                    data
                } = await axios.post("/get-purchase", this.filter);

                this.purchases = Object.values(
                    data.reduce((groups, purchase) => {

                        const id = purchase.supplier_id;

                        groups[id] ??= {
                            ...purchase,
                            paid: 0
                        };

                        groups[id].paid += parseFloat(purchase.paid);

                        return groups;

                    }, {})
                );
            },

            async getSale() {
                const {
                    data
                } = await axios.post("/get-sale", this.filter);

                this.sales = Object.values(
                    data.reduce((groups, sale) => {
                        if (sale.cashPaid <= 0) return groups;
                        const id = sale.customer_id;
                        groups[id] ??= {
                            ...sale,
                            cashPaid: 0
                        };
                        groups[id].cashPaid += parseFloat(sale.cashPaid);
                        return groups;
                    }, {})
                );
            },

            async getCustomerReceipts() {
                const filter = {
                    ...this.filter,
                    type: 'customer',
                    payment_method: 'cash'
                };

                const {
                    data
                } = await axios.post("/get-receive", filter);

                this.customerReceipts = Object.values(
                    data.reduce((groups, receipt) => {
                        if (receipt.payment_method != 'cash') return groups;
                        const id = receipt.customer_id;

                        groups[id] ??= {
                            ...receipt,
                            amount: 0
                        };

                        groups[id].amount += parseFloat(receipt.amount);

                        return groups;

                    }, {})
                );
            },

            async getSupplierPayments() {
                const filter = {
                    ...this.filter,
                    type: 'supplier',
                    payment_method: 'cash'
                };
                const {
                    data
                } = await axios.post("/get-payment", filter);
                this.supplierPayments = Object.values(
                    data.reduce((groups, payment) => {
                        if (payment.payment_method != 'cash') return groups;
                        const id = payment.supplier_id;
                        groups[id] ??= {
                            ...payment,
                            amount: 0
                        };
                        groups[id].amount += parseFloat(payment.amount);
                        return groups;
                    }, {})
                );
            },
            async getCustomerPayments() {
                const filter = {
                    ...this.filter,
                    type: 'customer',
                    payment_method: 'cash'
                };
                const {
                    data
                } = await axios.post("/get-payment", filter);
                this.customerPayments = Object.values(
                    data.reduce((groups, payment) => {
                        if (payment.payment_method != 'cash') return groups;
                        const id = payment.customer_id;
                        groups[id] ??= {
                            ...payment,
                            amount: 0
                        };
                        groups[id].amount += parseFloat(payment.amount);
                        return groups;
                    }, {})
                );
            },
            async getSupplierReceipts() {
                const filter = {
                    ...this.filter,
                    type: 'supplier',
                    payment_method: 'cash'
                };
                const {
                    data
                } = await axios.post("/get-receive", filter);
                this.supplierReceipts = Object.values(
                    data.reduce((groups, receipt) => {
                        if (receipt.payment_method != 'cash') return groups;
                        const id = receipt.supplier_id;
                        groups[id] ??= {
                            ...receipt,
                            amount: 0
                        };
                        groups[id].amount += parseFloat(receipt.amount);
                        return groups;
                    }, {})
                );
            },

            async getIncomes() {
                const filter = {
                    ...this.filter,
                    type: 'income'
                };

                const {
                    data
                } = await axios.post("/get-transaction", filter);

                this.incomes = Object.values(
                    data.reduce((groups, income) => {
                        const id = income.account_id;
                        groups[id] ??= {
                            ...income,
                            amount: 0
                        };

                        groups[id].amount += parseFloat(income.amount);
                        return groups;

                    }, {})
                );
            },

            async getExpenses() {
                const filter = {
                    ...this.filter,
                    type: 'expense'
                };
                const {
                    data
                } = await axios.post("/get-transaction", filter);
                this.expenses = Object.values(
                    data.reduce((groups, expense) => {
                        const id = expense.account_id;
                        groups[id] ??= {
                            ...expense,
                            amount: 0
                        };
                        groups[id].amount += parseFloat(expense.amount);
                        return groups;
                    }, {})
                );
            },

            getInvestmentReceipts() {
                const filter = {
                    ...this.filter,
                    type: 'deposit'
                };

                return axios.post("/get-invest-transaction", filter).then(response => {
                    const data = response.data;
                    this.investmentReceipts = Object.values(
                        data.reduce((groups, receipt) => {
                            const id = receipt.invest_account_id;
                            groups[id] ??= {
                                ...receipt,
                                amount: 0
                            };

                            groups[id].amount += parseFloat(receipt.amount);
                            return groups;

                        }, {})
                    );
                });
            },

            getInvestmentPayments() {
                const filter = {
                    ...this.filter,
                    type: 'withdraw'
                };

                return axios.post("/get-invest-transaction", filter).then(response => {
                    const data = response.data;
                    this.investmentPayments = Object.values(
                        data.reduce((groups, payment) => {
                            const id = payment.invest_account_id;
                            groups[id] ??= {
                                ...payment,
                                amount: 0
                            };

                            groups[id].amount += parseFloat(payment.amount);
                            return groups;

                        }, {})
                    );
                });
            },
            
            getPurchaseReturn() {
                return axios.post("/get-purchase-return", this.filter).then(response => {
                    const data = response.data;
                    this.purchaseReturns = data.filter(pr => pr.supplier_id == null)
                });
            },
            
            getSaleReturn() {
                return axios.post("/get-sale-return", this.filter).then(response => {
                    const data = response.data;
                    this.saleReturns = data.filter(pr => pr.customer_id == null)
                });
            },
            
            getSalaryPayment() {
                return axios.post("/get-salary", this.filter).then(response => {
                    const data = response.data;
                    this.salaryPayments = data
                });
            },

            getDamage() {
                const filter = {
                    ...this.filter,
                    supplierType: 'regular'
                };

                return axios.post("/get-damage", filter).then(response => {
                    const data = response.data;
                    this.damages = data
                });
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Daybook Report";
                let dateText = '';
                if (this.filter.dateFrom && this.filter.dateTo) {
                    dateText = `
                        <strong>Statement From: </strong>
                        <span>${moment(this.filter.dateFrom).format('DD-MM-YYYY')} to ${moment(this.filter.dateTo).format('DD-MM-YYYY')}</span>
                    `;
                }

                const printWindow = document.createElement('iframe');
                document.body.appendChild(printWindow);
                printWindow.srcdoc = `
                    <style>
                        .table>:not(caption)>*>* {
                            font-size: 11px !important;
                        }
                        address p{
                            margin: 0 !important;
                        }                                        
                    </style>

                    @include('layouts.headerInfo')
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h5 style="text-decoration:underline;">Daybook Report</h5>
                            </div>
                            <div class="col-6"></div>
                            <div class="col-6">${dateText}</div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                ${document.getElementById('reportContent').innerHTML}
                            </div>
                        </div>
                    </div>
                `;
                printWindow.onload = async function() {
                    printWindow.contentWindow.focus();
                    await new Promise(resolve => setTimeout(resolve, 500));
                    printWindow.contentWindow.print();
                    document.body.removeChild(printWindow);
                    window.document.title = oldTitle;
                };
            }
        },
    })
</script>
@endpush