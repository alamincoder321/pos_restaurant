@extends('master')
@section('title', 'Customer Ledger')
@section('breadcrumb', 'Customer Ledger')
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
<div id="customerLedger">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showLedger" class="form-inline">
                        <div class="form-group">
                            <label for="customer_id">Customer</label>
                            <v-select :options="customers" v-model="selectedCustomer" label="display_name"></v-select>
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
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Bill</th>
                                    <th>Inv.Paid</th>
                                    <th>Inv.Due</th>
                                    <th>Payment</th>
                                    <th>Receive</th>
                                    <th>Returned</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td colspan="7">Previous Balance</td>
                                    <td class="text-end" v-html="previousBalance"></td>
                                </tr>
                                <tr v-for="(item, index) in ledgers">
                                    <td>@{{ item.date | dateFormat('DD-MM-YYYY') }}</td>
                                    <td v-html="item.description"></td>
                                    <td v-html="item.bill" class="text-end"></td>
                                    <td v-html="item.paid" class="text-end"></td>
                                    <td v-html="item.due" class="text-end"></td>
                                    <td v-html="item.cash_payment" class="text-end"></td>
                                    <td v-html="item.cash_receive" class="text-end"></td>
                                    <td v-html="item.return_amount" class="text-end"></td>
                                    <td v-html="parseFloat(item.balance).toFixed(2)" class="text-end"></td>
                                </tr>
                                <tr :class="ledgers.length > 0 ? '' : 'd-none'" v-if="ledgers.length > 0">
                                    <td class="text-center bg-light" style="font-weight: 700;" colspan="2">Total</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.bill)}, 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.paid)}, 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.due)}, 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.cash_payment)}, 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.cash_receive)}, 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.return_amount)}, 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ parseFloat(ledgers[ledgers.length - 1].balance).toFixed(2) }}</td>
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
        el: '#customerLedger',
        data: {
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            ledgers: [],
            previousBalance: 0,
            customers: [],
            selectedCustomer: null,
            isLoading: null
        },

        filters: {
            dateFormat(dt, format){
                return dt == null ? '' : moment(dt).format(format);
            }
        },

        created() {
            this.getCustomer();
        },

        methods: {
            getCustomer() {
                axios.post('/get-customer')
                    .then(res => {
                        this.customers = res.data;
                    })
            },

            showLedger() {
                if (this.selectedCustomer == null) {
                    toastr.error('Please select a customer');
                    return;
                }
                let filter = {
                    customerId: this.selectedCustomer ? this.selectedCustomer.id : '',
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                this.isLoading = false;
                axios.post('/get-customer-ledger', filter)
                    .then(res => {
                        this.ledgers = res.data.ledgers;
                        this.previousBalance = res.data.previousBalance;
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Customer Ledger"
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
                                <h5 style="text-decoration:underline;">Customer Ledger</h5>
                            </div>
                            <div class="col-6">${customerText}</div>
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