@extends('master')

@section('title', 'Daily Report')
@section('breadcrumb', 'Daily Report')
@push('style')
<style scoped>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
        vertical-align: middle !important;
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
<div id="dailyReport">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showList" class="form-inline">
                        <div class="form-group">
                            <label for="searchType">SearchType</label>
                            <select id="searchType" class="form-select" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="user">By User</option>
                            </select>
                        </div>
                        <div class="form-group" :class="searchType == 'user' ? '' : 'd-none'" v-if="searchType == 'user'">
                            <label for="user_id">User</label>
                            <v-select :options="users" v-model="selectedUser" label="name"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="dateFrom">From</label>
                            <input type="date" class="form-control" id="dateFrom" v-model="dateFrom" />
                        </div>
                        <div class="form-group">
                            <label for="dateTo">To</label>
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
                                    <th rowspan="2">Sl</th>
                                    <th rowspan="2">Code</th>
                                    <th rowspan="2">Name</th>
                                    <th rowspan="2">Mobile</th>
                                    <th rowspan="2">TotalOrder</th>
                                    <th colspan="2">Cash Received</th>
                                    <th colspan="2">Bank Received</th>
                                    <th rowspan="2">ChangeAmount</th>
                                    <th rowspan="2">ReturnAmount</th>
                                    <th rowspan="2">Last Balance</th>
                                </tr>
                                <tr>
                                    <th>Order Received</th>
                                    <th>Customer Received</th>
                                    <th>Order Received</th>
                                    <th>Customer Received</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in reports">
                                    <td class="text-center" v-html="index + 1"></td>
                                    <td class="text-center" v-html="item.code"></td>
                                    <td v-html="item.name"></td>
                                    <td class="text-center" v-html="item.phone"></td>
                                    <td class="text-end" v-html="item.sale_total"></td>
                                    <td class="text-end" v-html="item.cash_sale_receive"></td>
                                    <td class="text-end" v-html="item.cash_customer_receive"></td>
                                    <td class="text-end" v-html="item.bank_sale_receive"></td>
                                    <td class="text-end" v-html="item.bank_customer_receive"></td>
                                    <td class="text-end" v-html="item.changeAmount"></td>
                                    <td class="text-end" v-html="item.returnAmount"></td>
                                    <td class="text-end" v-html="item.netBalance"></td>
                                </tr>
                                <tr :class="reports.length > 0 && selectedUser == null ? '' : 'd-none'" v-if="reports.length > 0 && selectedUser == null">
                                    <td class="text-center bg-light" style="font-weight: 700;" colspan="4">Total</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.sale_total), 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.cash_sale_receive), 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.cash_customer_receive), 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.bank_sale_receive), 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.bank_customer_receive), 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.changeAmount), 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.returnAmount), 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ reports.reduce((pre, cur) => pre + parseFloat(cur.netBalance), 0).toFixed(2) }}</td>
                                </tr>
                                <tr :class="reports.length == 0 ? '' : 'd-none'" v-if="reports.length == 0">
                                    <td colspan="12" class="text-center">Not Found Data</td>
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
        el: '#dailyReport',
        data: {
            searchType: '',
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            reports: [],
            users: [],
            selectedUser: null,
            isLoading: null
        },

        methods: {
            getUsers() {
                axios.post('/get-user')
                    .then(res => {
                        this.users = res.data;
                    })
            },

            onChangeSearchType() {
                this.reports = [];
                this.selectedUser = null;
                this.isLoading = null;
                if (this.searchType == 'user') {
                    this.getUsers();
                }
            },

            showList() {
                let filter = {
                    userId: this.selectedUser ? this.selectedUser.id : '',
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                this.isLoading = false;
                axios.post('/get-dailyReport', filter)
                    .then(res => {
                        this.reports = res.data;
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Daily Report"
                let userText = '';
                if (this.selectedUser != null) {
                    userText = `
                        <strong>User ID: </strong>
                        <span>${this.selectedUser.code}</span><br>
                        <strong>Name: </strong>
                        <span>${this.selectedUser.name}</span><br>
                        <strong>Mobile: </strong>
                        <span>${this.selectedUser.phone}</span>
                    `;
                }
                
                const printWindow = document.createElement('iframe');
                document.body.appendChild(printWindow);
                printWindow.srcdoc = `
                    <style>
                        .table>:not(caption)>*>* {
                            font-size: 11px !important;
                        }
                        tr th, tr td{
                            vertical-align: middle !important;
                        }
                        address p{
                            margin: 0 !important;
                        }                                        
                    </style>

                    @include('layouts.headerInfo')
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h5 style="text-decoration:underline;">Daily Report</h5>
                            </div>
                            <div class="col-12">${userText}</div>
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