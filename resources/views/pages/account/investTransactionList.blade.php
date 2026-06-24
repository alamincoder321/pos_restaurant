@extends('master')

@section('title', 'Investment Transaction List')
@section('breadcrumb', 'Investment Transaction List')
@push('style')
<style>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
    }
</style>
@endpush
@section('content')
<div id="investmentTransactionList">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showList" class="form-inline">
                        <div class="form-group">
                            <label for="searchType">SearchType</label>
                            <select id="searchType" class="form-select" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="account">By Account</option>
                            </select>
                        </div>
                        <div class="form-group" :class="searchType == 'account' ? '' : 'd-none'" v-if="searchType == 'account'">
                            <label for="invest_account_id">Account</label>
                            <v-select :options="accounts" v-model="selectedAccount" label="name"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="dateFrom">From</label>
                            <input type="date" class="form-control" v-model="dateFrom">
                        </div>
                        <div class="form-group">
                            <label for="dateTo">To</label>
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
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in transactions">
                                    <td style="text-align: center;" v-html="index + 1"></td>
                                    <td style="text-align: center;" v-html="item.date"></td>
                                    <td v-html="item.invest_account?.name"></td>
                                    <td style="text-align: center;" v-html="item.type"></td>
                                    <td style="text-align: center;" v-html="item.amount"></td>
                                    <td style="text-align: center;" v-html="item.note"></td>
                                </tr>
                                <tr :class="transactions.length == 0 ? '' : 'd-none'" v-if="transactions.length == 0">
                                    <td colspan="9" class="text-center">Not Found Data</td>
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
        el: '#investmentTransactionList',
        data: {
            searchType: '',
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            transactions: [],
            accounts: [],
            selectedAccount: null,
            isLoading: null
        },

        methods: {
            showModal(row){

            },

            getAccount() {
                axios.post('/get-invest')
                    .then(res => {
                        this.accounts = res.data;
                    })
            },

            onChangeSearchType() {
                this.selectedAccount = null;
                this.isLoading = null;
                if (this.searchType == 'account') {
                    this.getAccount();
                }
            },

            showList() {
                let filter = {
                    accountId: this.selectedAccount ? this.selectedAccount.id : '',
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                this.isLoading = false;
                axios.post('/get-invest-transaction', filter)
                    .then(res => {
                        this.transactions = res.data
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Investment Transaction List"
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
                                <h5>Investment Transaction List</h5>
                            </div>
                            <div class="col-12 text-end">
                                <span style="margin: 0 !important;">Statement From: ${moment(this.dateFrom).format('DD-MM-YYYY')}</span>
                                <span style="margin: 0 !important;">To: ${moment(this.dateTo).format('DD-MM-YYYY')}</span>
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