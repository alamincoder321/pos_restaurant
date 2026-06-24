@extends('master')

@section('title', 'Bank Transaction Entry')
@section('breadcrumb', 'Bank Transaction Entry')
@section('content')
<div class="row" id="transaction">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Bank Transaction Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="invoice">Invoice:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="invoice" name="invoice" v-model="transaction.invoice" readonly />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="account_id">Account:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="banks" v-model="selectedBank" label="display_name" @input="onChangeBank"></v-select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="date">Date:</label>
                                <div class="col-8 col-md-9">
                                    <input type="date" class="form-control" autocomplete="off" id="date" name="date" v-model="transaction.date" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="previous_balance">Prev. Balance:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" readonly class="form-control" autocomplete="off" id="previous_balance" name="previous_balance" v-model="transaction.previous_balance" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="note">Type:</label>
                                <div class="col-8 col-md-9">
                                    <select name="type" class="form-select" v-model="transaction.type">
                                        <option value="debit">Withdraw</option>
                                        <option value="credit">Deposit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="note">Note:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="note" name="note" v-model="transaction.note" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="amount">Amount:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" class="form-control" autocomplete="off" id="amount" name="amount" v-model="transaction.amount" />
                                </div>
                            </div>
                            <div class="mt-1 row">
                                <div class="col-12 col-md-12 text-end">
                                    <button class="btn btn-danger" type="button">Reset</button>
                                    <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                        <span v-if="transaction.id == ''">Save</span>
                                        <span v-if="transaction.id != ''">Update</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 mt-1">
        <vue-good-table :columns="columns" :rows="transactions" :fixed-header="false" :pagination-options="{
                enabled: true,
                perPage: 100,
            }" :search-options="{ enabled: true }" :line-numbers="true" styleClass="vgt-table condensed" max-height="550px">
            <template #table-row="props">
                <span class="d-flex gap-2 justify-content-end" v-if="props.column.field == 'before'">
                    <a href="" title="edit" @click.prevent="editData(props.row)">
                        <i class="bi bi-pen text-info" style="font-size: 14px;"></i>
                    </a>
                    <a href="" title="delete" @click.prevent="deleteData(props.row.id)">
                        <i class="bi bi-trash text-danger" style="font-size: 14px;"></i>
                    </a>
                </span>
            </template>
        </vue-good-table>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#transaction",
        data() {
            return {
                columns: [{
                        label: "Invoice",
                        field: 'invoice'
                    },
                    {
                        label: "Date",
                        field: 'date'
                    },
                    {
                        label: "Bank",
                        field: 'name'
                    },
                    {
                        label: "AccountType",
                        field: 'type'
                    },
                    {
                        label: "Amount",
                        field: 'amount'
                    },
                    {
                        label: "Note",
                        field: 'note'
                    },
                    {
                        label: "Added_By",
                        field: 'ad_user.username'
                    },
                    {
                        label: "Updated_By",
                        field: 'up_user.username'
                    },
                    {
                        label: "Action",
                        field: "before"
                    }
                ],
                transaction: {
                    id: '',
                    invoice: "{{invoiceGenerate('Bank_Transaction', 'T', session('branch')->id)}}",
                    date: moment().format('YYYY-MM-DD'),
                    type: 'credit',
                    bank_id: '',
                    amount: 0,
                    previous_balance: 0,
                    note: ''
                },
                transactions: [],
                banks: [],
                selectedBank: null,

                role: "{{auth()->user()->role}}",
                loading: true,
                onProgress: false,
            }
        },

        created() {
            this.getBank();
            this.getTransaction();
        },

        methods: {
            getBank() {
                axios.post(`/get-bank`)
                    .then(res => {
                        this.banks = res.data;
                    })
            },
            getTransaction() {
                this.loading = true;
                let filter = {
                    dateFrom: this.transaction.date,
                    dateTo: this.transaction.date
                }
                axios.post(`/get-bankTransaction`, filter)
                    .then(res => {
                        this.transactions = res.data.map((item, index) => {
                            item.sl = index + 1;
                            item.name = `${item.bank?.name} - ${item.bank?.number} - ${item.bank?.bank_name}`;
                            return item;
                        });
                        this.loading = false;
                    })
            },
            async onChangeBank() {
                if (this.selectedBank == null) {
                    return;
                }
                if (this.selectedBank.id != '') {
                    await axios.post(`/get-bankBalance`, {
                        bankId: this.selectedBank.id
                    }).then(res => {
                        this.transaction.previous_balance = res.data[0].currentbalance;
                    })
                }
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.transaction.id);
                formdata.append('bank_id', this.selectedBank ? this.selectedBank.id : '');
                let url = this.transaction.id != '' ? `/update-bankTransaction` : `/bankTransaction`

                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.transaction.invoice = res.data.invoice;
                        this.getTransaction();
                    })
                    .catch(err => {
                        this.onProgress = false
                        var r = JSON.parse(err.request.response);
                        if (err.request.status == '422' && r.errors != undefined && typeof r.errors == 'object') {
                            $.each(r.errors, (index, value) => {
                                $.each(value, (ind, val) => {
                                    toastr.error(val)
                                })
                            })
                        } else {
                            if (r.errors != undefined) {
                                console.log(r.errors);
                            }
                            toastr.error(r.message)
                        }
                    })

            },

            editData(row) {
                let keys = Object.keys(this.transaction);
                keys.forEach(item => {
                    this.transaction[item] = row[item];
                })
                this.selectedBank = this.banks.find(item => item.id == row.bank_id);
                setTimeout(() => {
                    this.transaction.previous_balance = row.previous_balance;
                }, 1500);
            },

            deleteData(rowId) {
                if (!confirm("Are you sure ?")) {
                    return;
                }
                axios.post(`/delete-bankTransaction`, {
                        id: rowId,
                        type: 'transaction'
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getTransaction();
                        }
                    })
            },

            clearData() {
                this.transaction = {
                    id: '',
                    invoice: "{{invoiceGenerate('Bank_Transaction', 'T', session('branch')->id)}}",
                    date: moment().format('YYYY-MM-DD'),
                    type: 'credit',
                    bank_id: '',
                    amount: 0,
                    previous_balance: 0,
                    note: ''
                }
                this.onProgress = false;
                this.selectedBank = null;
            }
        },
    })
</script>
@endpush