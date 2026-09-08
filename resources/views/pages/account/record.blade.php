@extends('master')

@section('title', 'Expense & Income Record')
@section('breadcrumb', 'Expense & Income Record')
@push('style')
<style>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
    }

    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
@endpush
@section('content')
<div id="expenseIncomeRecord">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showReport" class="form-inline">
                        <div class="form-group">
                            <label for="searchType">Type</label>
                            <select id="searchType" class="form-select" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="expense">Expense</option>
                                <option value="income">Income</option>
                            </select>
                        </div>

                        <div class="form-group" :class="searchType == 'expense' ? '' : 'd-none'" v-if="searchType == 'expense'">
                            <label for="expense">Expense</label>
                            <v-select :options="expenses" v-model="selectedExpense" label="name"></v-select>
                        </div>
                        <div class="form-group" :class="searchType == 'income' ? '' : 'd-none'" v-if="searchType == 'income'">
                            <label for="income">Income</label>
                            <v-select :options="incomes" v-model="selectedIncome" label="name"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="dateFrom">From</label>
                            <input type="date" class="form-control" v-model="dateFrom">
                        </div>
                        <div class="form-group">
                            <label for="dateFrom">To</label>
                            <input type="date" class="form-control" v-model="dateTo">
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
                        <table class="table table-bordered table-hover record-table">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Note</th>
                                    <th>Expense</th>
                                    <th>Income</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in reports" :class="reports.length > 0 ? '' : 'd-none'" v-if="reports.length > 0">
                                    <td v-html="index + 1" class="text-center"></td>
                                    <td v-html="item.invoice" class="text-center"></td>
                                    <td v-html="item.date" class="text-center"></td>
                                    <td v-html="item.account.name" class="text-center"></td>
                                    <td v-html="item.note" class="text-center"></td>
                                    <td v-html="item.type == 'expense' ? item.amount : 0" class="text-end"></td>
                                    <td v-html="item.type == 'income' ? item.amount : 0" class="text-end"></td>
                                </tr>
                                <tr :class="reports.length > 0 ? '' : 'd-none'" v-show="reports.length > 0">
                                    <th v-html="`Total`" colspan="5" class="text-end"></th>
                                    <th v-html="reports.reduce((pr, cu) => {return pr + parseFloat(cu.type == 'expense' ? cu.amount : 0)}, 0).toFixed(2)" class="text-end"></th>
                                    <th v-html="reports.reduce((pr, cu) => {return pr + parseFloat(cu.type == 'income' ? cu.amount : 0)}, 0).toFixed(2)" class="text-end"></th>
                                </tr>
                                <tr :class="reports.length == 0 ? '' : 'd-none'" v-if="reports.length == 0">
                                    <td colspan="7" class="text-center">Not Found Data</td>
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
        el: '#expenseIncomeRecord',
        data: {
            searchType: '',
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            reports: [],
            expenses: [],
            selectedExpense: null,
            incomes: [],
            selectedIncome: null,
            isLoading: null
        },

        created() {
            this.showReport();
        },

        methods: {
            getIncomes() {
                axios.post('/get-accounthead', {
                    type: 'income'
                }).then(res => {
                    this.incomes = res.data;
                })
            },
            getExpenses() {
                axios.post('/get-accounthead', {
                    type: 'expense'
                }).then(res => {
                    this.expenses = res.data;
                })
            },

            onChangeSearchType() {
                this.reports = [];
                this.expenses = [];
                this.selectedExpense = null;
                this.incomes = [];
                this.selectedIncome = null;
                this.isLoading = null;
                if (this.searchType == 'expense') {
                    this.getExpenses();
                } else if (this.searchType == 'income') {
                    this.getIncomes();
                }
            },

            showReport() {
                let filter = {
                    accountId: this.searchType == '' ? '' : this.searchType === 'income' ? this.selectedIncome?.id : this.selectedExpense?.id,
                    type: this.searchType,
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                this.isLoading = false;
                axios.post('/get-transaction', filter)
                    .then(res => {
                        this.reports = res.data
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Order Record"
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
                                <h5>${this.searchType == '' ? 'Expense & Income' : this.searchType == 'income' ? 'Income' : 'Expense'} Record</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                ${document.getElementById('reportContent').innerHTML}
                            </div>
                        </div>
                    </div>
                `;
                printWindow.onload = async function() {
                    const rows = printWindow.contentDocument.querySelectorAll('.record-table tr');
                    rows.forEach(row => {
                        const lastCell = row.lastElementChild;
                        if (lastCell) {
                            lastCell.remove();
                        }
                    });

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