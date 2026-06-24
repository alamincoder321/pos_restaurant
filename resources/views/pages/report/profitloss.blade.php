@extends('master')

@section('title', 'Profit & Loss Report')
@section('breadcrumb', 'Profit & Loss Report')
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
</style>
@endpush
@section('content')
<div id="profitLoss">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showList" class="form-inline">
                        <div class="form-group">
                            <label for="searchType">SearchType</label>
                            <select id="searchType" class="form-select" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="customer">By Customer</option>
                            </select>
                        </div>
                        <div class="form-group" :class="searchType == 'customer' ? '' : 'd-none'" v-if="searchType == 'customer'">
                            <label for="customer_id">Customer</label>
                            <v-select :options="customers" v-model="selectedCustomer" label="display_name"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="date">From</label>
                            <input type="date" class="form-control" id="dateFrom" v-model="dateFrom" />
                        </div>
                        <div class="form-group">
                            <label for="date">To</label>
                            <input type="date" class="form-control" id="dateTo" v-model="dateTo" />
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
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Name</th>
                                    <th>Purchase Rate</th>
                                    <th>Sale Rate</th>
                                    <th>Quantity</th>
                                    <th>PurchaseTotal</th>
                                    <th>SoldTotal</th>
                                    <th>Profit/Loss</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(sale, index) in sales">
                                    <tr>
                                        <td style="background: #9fe7dd47; padding: 5px !important;" colspan="8">
                                            <strong>Invoice No: </strong>
                                            <span v-html="sale.invoice"></span> |
                                            <strong>Date: </strong>
                                            <span>@{{sale.date | dateFormat('DD/MM/YYYY')}}</span> |
                                            <strong>Customer: </strong>
                                            <span v-html="sale.customer_name"></span>
                                        </td>
                                    </tr>
                                    <tr v-for="(item, sl) in sale.details">
                                        <td v-html="item.code"></td>
                                        <td v-html="item.name"></td>
                                        <td v-html="item.purchase_rate" class="text-end"></td>
                                        <td v-html="item.sale_rate" class="text-end"></td>
                                        <td v-html="item.quantity" class="text-center"></td>
                                        <td v-html="parseFloat(item.purchase_total).toFixed(2)" class="text-end"></td>
                                        <td v-html="item.total" class="text-end"></td>
                                        <td v-html="parseFloat(item.profitLoss).toFixed(2)" class="text-end"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="font-weight: 700;" class="text-end">Total</td>
                                        <td style="font-weight: 700;" class="text-center">@{{ sale.details.reduce((pre, cur) => {return pre + parseFloat(cur.quantity)}, 0) }}</td>
                                        <td style="font-weight: 700;" class="text-end">@{{ sale.details.reduce((pre, cur) => {return pre + parseFloat(cur.purchase_total)}, 0).toFixed(2) }}</td>
                                        <td style="font-weight: 700;" class="text-end">@{{ sale.details.reduce((pre, cur) => {return pre + parseFloat(cur.total)}, 0).toFixed(2) }}</td>
                                        <td style="font-weight: 700;" class="text-end">@{{ sale.details.reduce((pre, cur) => {return pre + parseFloat(cur.profitLoss)}, 0).toFixed(2) }}</td>
                                    </tr>
                                </template>
                                <tr v-if="sales.length > 0">
                                    <td colspan="8" style="padding: 5px !important;"></td>
                                </tr>
                                <tr v-if="sales.length > 0">
                                    <td style="font-weight: 700;padding: 3px !important;" colspan="5" class="text-end">Gross Profit</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end">@{{ totalPurchase }}</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end">@{{ totalSale }}</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end">@{{ totalProfitLoss }}</td>
                                </tr>
                                <tr :class="sales.length == 0 ? '' : 'd-none'" v-if="sales.length == 0">
                                    <td colspan="8" class="text-center">Not Found Data</td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Income(+)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="expenseincome.income"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Sale Vat(+)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="totalSaleVat"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Sale Transport Cost(+)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="totalSaleTransportcost"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Purchase Discount(+)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="expenseincome.purchase_discount"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Expense(-)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="expenseincome.expense"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Purchase Vat(-)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="expenseincome.purchase_vat"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Purchase Transport Cost(-)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="expenseincome.purchase_transport_cost"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Sale Discount(-)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="totalSaleDiscount"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Sale Return(-)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="expenseincome.sale_return_amount"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="5">Salary Payment(-)</td>
                                    <td style="font-weight: 700;padding: 3px !important;" class="text-end" colspan="3" v-text="expenseincome.salary_payment"></td>
                                </tr>
                                <tr :class="selectedCustomer != null ? 'd-none' : ''" v-if="selectedCustomer == null">
                                    <td style="font-weight: 700;padding: 3px !important;padding: 3px !important;background: #e1e1e1;" class="text-end" colspan="5">Net Profit</td>
                                    <td style="font-weight: 700;padding: 3px !important;padding: 3px !important;background: #e1e1e1;" class="text-end" colspan="3" v-text="netProfit"></td>
                                </tr>
                            </tbody>
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
        el: '#profitLoss',
        data: {
            searchType: '',
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            expenseincome: {},
            sales: [],
            customers: [],
            selectedCustomer: null,
            isLoading: null
        },

        computed: {
            totalPurchase() {
                return this.sales.reduce((pre, cur) => {
                    return pre + cur.details.reduce((p, c) => {
                        return p + parseFloat(c.purchase_total);
                    }, 0);
                }, 0).toFixed(2);
            },
            totalSale() {
                return this.sales.reduce((pre, cur) => {
                    return pre + cur.details.reduce((p, c) => {
                        return p + parseFloat(c.total);
                    }, 0);
                }, 0).toFixed(2);
            },
            totalProfitLoss() {
                return this.sales.reduce((pre, cur) => {
                    return pre + cur.details.reduce((p, c) => {
                        return p + parseFloat(c.profitLoss);
                    }, 0);
                }, 0).toFixed(2);
            },
            totalSaleDiscount() {
                return this.sales.reduce((pre, cur) => {
                    return pre + parseFloat(cur.discount);
                }, 0).toFixed(2);
            },
            totalSaleVat() {
                return this.sales.reduce((pre, cur) => {
                    return pre + parseFloat(cur.vat);
                }, 0).toFixed(2);
            },
            totalSaleTransportcost() {
                return this.sales.reduce((pre, cur) => {
                    return pre + parseFloat(cur.transport_cost);
                }, 0).toFixed(2);
            },
            netProfit() {
                let totalIncome = parseFloat(this.totalSale) + parseFloat(this.expenseincome.income) + parseFloat(this.totalSaleVat) + parseFloat(this.expenseincome.purchase_discount) + parseFloat(this.totalSaleTransportcost);
                let totalExpense = parseFloat(this.totalPurchase) + parseFloat(this.expenseincome.expense) + parseFloat(this.expenseincome.purchase_vat) + parseFloat(this.expenseincome.purchase_transport_cost) + parseFloat(this.totalSaleDiscount) + parseFloat(this.expenseincome.sale_return_amount) + parseFloat(this.expenseincome.salary_payment);
                return (totalIncome - totalExpense).toFixed(2);
            }
        },

        filters: {
            dateFormat(dt, format) {
                return dt == null || dt == '' ? '' : moment(dt).format(format);
            }
        },

        methods: {
            getCustomer() {
                axios.post('/get-customer')
                    .then(res => {
                        this.customers = res.data;
                    })
            },

            onChangeSearchType() {
                this.sales = [];
                this.selectedCustomer = null;
                this.isLoading = null;
                if (this.searchType == 'customer') {
                    this.getCustomer();
                }
            },

            async showList() {
                let filter = {
                    customerId: this.selectedCustomer ? this.selectedCustomer.id : '',
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                this.isLoading = false;
                await axios.post('/get-sale', filter)
                    .then(res => {
                        this.sales = res.data;
                        this.isLoading = true;
                    })
                await axios.post('/get-other-expense-income', filter)
                    .then(res => {
                        this.expenseincome = res.data;
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Profit & Loss Report"
                let customerText = '';
                if (this.selectedCustomer != null) {
                    customerText = `
                        <strong>Customer ID: </strong>
                        <span>${this.selectedCustomer.code}</span><br>
                        <strong>Name: </strong>
                        <span>${this.selectedCustomer.name}</span><br>
                        <strong>Mobile: </strong>
                        <span>${this.selectedCustomer.phone}</span>
                    `;
                }
                let dateText = '';
                if (this.dateFrom && this.dateTo) {
                    dateText = `
                        <strong>Statement From: </strong>
                        <span>${moment(this.dateFrom).format('DD-MM-YYYY')} to ${moment(this.dateTo).format('DD-MM-YYYY')}</span>
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
                                <h5 style="text-decoration:underline;">Profit & Loss Report</h5>
                            </div>
                            <div class="col-6">${customerText}</div>
                            <div class="col-6 text-end">${dateText}</div>
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