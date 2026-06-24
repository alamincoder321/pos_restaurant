@extends('master')

@section('title', 'Investment Transaction Entry')
@section('breadcrumb', 'Investment Transaction Entry')
@section('content')
<div class="row" id="transaction">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Investment Transaction Entry Form</h5>
                <form @submit.prevent="{{ buttonAction('entry') || buttonAction('update') ? 'saveData($event)' : null }}">
                    <div class="row">
                        <div class="col-12 col-md-6 offset-md-3">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="date">Date:</label>
                                <div class="col-8 col-md-9">
                                    <input type="date" class="form-control" @change="getTransaction" autocomplete="off" id="date" name="date" v-model="transaction.date" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="account">Account:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="accounts" v-model="selectedAccount" label="name"></v-select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="type">Type:</label>
                                <div class="col-8 col-md-9">
                                    <select class="form-select" id="type" name="type" v-model="transaction.type">
                                        <option value="">Select Type</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="withdraw">Withdraw</option>
                                        <option value="profit">Profit</option>
                                        <option value="loss">Loss</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="amount">Amount:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" class="form-control" autocomplete="off" id="amount" name="amount" v-model="transaction.amount" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="note">Note:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="note" name="note" v-model="transaction.note" />
                                </div>
                            </div>
                            
                            <div class="mt-1 text-end">
                                @if (buttonAction('entry') || buttonAction('update'))
                                <button class="btn btn-danger" type="button">Reset</button>
                                <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                    <span v-if="transaction.id == ''">Save</span>
                                    <span v-if="transaction.id != ''">Update</span>
                                </button>
                                @endif
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
                    @if (buttonAction('update'))
                    <a href="" title="edit" @click.prevent="editData(props.row)">
                        <i class="bi bi-pen text-info" style="font-size: 14px;"></i>
                    </a>
                    @endif
                    @if (buttonAction('delete'))
                    <a href="" title="delete" @click.prevent="deleteData(props.row.id)">
                        <i class="bi bi-trash text-danger" style="font-size: 14px;"></i>
                    </a>
                    @endif
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
                        label: "Date",
                        field: 'date'
                    },
                    {
                        label: "Account",
                        field: 'invest_account.name'
                    },
                    {
                        label: "Type",
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
                    date: moment().format('YYYY-MM-DD'),
                    invest_account_id: '',
                    type: '',
                    amount: 0,
                    note: '',
                },
                transactions: [],
                accounts: [],
                selectedAccount: null,
                loading: true,
                onProgress: false,
            }
        },

        created() {
            this.getAccount();
            this.getTransaction();
        },

        methods: {
            getTransaction() {
                this.loading = true;
                let data = {
                    dateFrom: this.transaction.date,
                    dateTo: this.transaction.date,
                }
                axios.post(`/get-invest-transaction`, data)
                    .then(res => {
                        this.transactions = res.data.map((item, index) => {
                            item.sl = index + 1;
                            return item;
                        })
                        this.loading = false;
                    })
            },
            getAccount() {
                axios.post(`/get-invest`)
                    .then(res => {
                        this.accounts = res.data;
                    })
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.transaction.id);
                formdata.append('invest_account_id', this.selectedAccount ? this.selectedAccount.id : null);
                let url = this.transaction.id != '' ? `/update-invest-transaction` : `/invest-transaction`

                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
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

                this.selectedAccount = this.accounts.find(account => account.id == row.invest_account_id);
            },

            deleteData(rowId) {
                if (!confirm("Are you sure ?")) {
                    return;
                }
                axios.post(`/delete-invest-transaction`, {
                        id: rowId
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
                    date: moment().format('YYYY-MM-DD'),
                    invest_account_id: '',
                    type: '',
                    amount: 0,
                    note: '',
                }
                this.selectedAccount = null;
                this.onProgress = false;
            }

        },
    })
</script>
@endpush