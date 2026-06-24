@extends('master')

@section('title', 'Expense Entry')
@section('breadcrumb', 'Expense Entry')
@section('content')
<div class="row" id="expense">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Expense Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="invoice">Invoice:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="invoice" name="invoice" v-model="expense.invoice" readonly/>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="account_id">Account:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="accounts" v-model="selectedAccount" label="name"></v-select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="date">Date:</label>
                                <div class="col-8 col-md-9">
                                    <input type="date" class="form-control" autocomplete="off" id="date" name="date" v-model="expense.date" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="note">Note:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="note" name="note" v-model="expense.note" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="amount">Amount:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" class="form-control" autocomplete="off" id="amount" name="amount" v-model="expense.amount" />
                                </div>
                            </div>
                            <div class="mt-1 row">
                                <div class="col-12 col-md-12 text-end">
                                    <button class="btn btn-danger" type="button">Reset</button>
                                    <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                        <span v-if="expense.id == ''">Save</span>
                                        <span v-if="expense.id != ''">Update</span>
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
        <vue-good-table :columns="columns" :rows="expenses" :fixed-header="false" :pagination-options="{
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
        el: "#expense",
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
                        label: "Account Name",
                        field: 'account.name'
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
                expense: {
                    id: '',
                    invoice: "{{transactionInvoice('Transaction', 'T', session('branch')->id, 'expense')}}",
                    date: moment().format('YYYY-MM-DD'),
                    account_id: '',
                    amount: 0,
                    note: ''
                },
                expenses: [],
                accounts: [],
                selectedAccount: null,

                role: "{{auth()->user()->role}}",
                loading: true,
                onProgress: false,
            }
        },

        created() {
            this.getAccount();
            this.getExpense();
        },

        methods: {
            getAccount() {
                axios.post(`/get-accounthead`, {
                        type: 'expense'
                    })
                    .then(res => {
                        this.accounts = res.data;
                    })
            },
            getExpense() {
                this.loading = true;
                let filter = {
                    dateFrom: this.expense.date,
                    dateTo: this.expense.date,
                    type: 'expense'
                }
                axios.post(`/get-transaction`, filter)
                    .then(res => {
                        this.expenses = res.data.map((item, index) => {
                            item.sl = index + 1;
                            return item;
                        });
                        this.loading = false;
                    })
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.expense.id);
                formdata.append('type', 'expense');
                formdata.append('account_id', this.selectedAccount ? this.selectedAccount.id : '');
                let url = this.expense.id != '' ? `/update-transaction` : `/transaction`

                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.expense.invoice = res.data.invoice;
                        this.getExpense();
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
                let keys = Object.keys(this.expense);
                keys.forEach(item => {
                    this.expense[item] = row[item];
                })
                this.selectedAccount = this.accounts.find(item => item.id == row.account_id);
            },

            deleteData(rowId) {
                if (!confirm("Are you sure ?")) {
                    return;
                }
                axios.post(`/delete-transaction`, {
                        id: rowId,
                        type: 'expense'
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getExpense();
                        }
                    })
            },

            clearData() {
                this.expense = {
                    id: '',
                    invoice: "{{transactionInvoice('Transaction', 'T', session('branch')->id, 'expense')}}",
                    date: moment().format('YYYY-MM-DD'),
                    account_id: '',
                    amount: 0,
                    note: ''
                }
                this.onProgress = false;
            }
        },
    })
</script>
@endpush