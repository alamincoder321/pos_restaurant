@extends('master')
@section('title', 'Bank Ledger')
@section('breadcrumb', 'Bank Ledger')
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
<div id="bankLedger">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showLedger" class="form-inline">
                        <div class="form-group">
                            <label for="bank_id">Bank</label>
                            <v-select :options="banks" v-model="selectedBank" label="display_name"></v-select>
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
                                    <th>Deposit</th>
                                    <th>Withdraw</th>                                    
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td colspan="3">Previous Balance</td>
                                    <td class="text-end" v-html="previousBalance"></td>
                                </tr>
                                <tr v-for="(item, index) in ledgers">
                                    <td>@{{ item.date | dateFormat('DD-MM-YYYY') }}</td>
                                    <td v-html="item.description"></td>
                                    <td v-html="item.deposit" class="text-end"></td>
                                    <td v-html="item.withdraw" class="text-end"></td>
                                    <td v-html="parseFloat(item.balance).toFixed(2)" class="text-end"></td>
                                </tr>
                                <tr :class="ledgers.length > 0 ? '' : 'd-none'" v-if="ledgers.length > 0">
                                    <td class="text-center bg-light" style="font-weight: 700;" colspan="2">Total</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.deposit)}, 0).toFixed(2) }}</td>
                                    <td class="text-end bg-light" style="font-weight: 700;">@{{ ledgers.reduce((pre, cur) => {return pre + parseFloat(cur.withdraw)}, 0).toFixed(2) }}</td>
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
        el: '#bankLedger',
        data: {
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            ledgers: [],
            previousBalance: 0,
            banks: [],
            selectedBank: null,
            isLoading: null
        },

        filters: {
            dateFormat(dt, format) {
                return dt == null ? '' : moment(dt).format(format);
            }
        },

        created() {
            this.getBank();
        },

        methods: {
            getBank() {
                axios.post('/get-bank')
                    .then(res => {
                        this.banks = res.data;
                    })
            },

            showLedger() {
                if (this.selectedBank == null) {
                    toastr.error('Please select a bank');
                    return;
                }
                let filter = {
                    bankId: this.selectedBank ? this.selectedBank.id : '',
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                this.isLoading = false;
                axios.post('/get-bank-ledger', filter)
                    .then(res => {
                        this.ledgers = res.data.ledgers;
                        this.previousBalance = res.data.previousBalance;
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Bank Ledger"
                let bankText = '';
                if (this.selectedBank != null) {
                    bankText = `
                        <strong>Bank ID: </strong>
                        <span>${this.selectedBank.code}</span><br>
                        <strong>Name: </strong>
                        <span>${this.selectedBank.name}</span><br>
                        <strong>Mobile: </strong>
                        <span>${this.selectedBank.phone}</span>
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
                                <h5 style="text-decoration:underline;">Bank Ledger</h5>
                            </div>
                            <div class="col-6">${bankText}</div>
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