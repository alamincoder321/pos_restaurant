@extends('master')

@section('title', 'Receive Amount')
@section('breadcrumb', 'Receive Amount')
@section('content')
<div class="row" id="receive">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Receive Amount Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="invoice">Invoice:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="invoice" name="invoice" v-model="receive.invoice" readonly />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="account_id">Type:</label>
                                <div class="col-8 col-md-9">
                                    <select name="type" @change="onChangeType" class="form-select" v-model="receive.type">
                                        <option :disabled="receive.id != '' ? true : false" value="customer">Customer</option>
                                        <option :disabled="receive.id != '' ? true : false" value="supplier">Supplier</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="account_id">Method:</label>
                                <div class="col-8 col-md-9">
                                    <select name="payment_method" class="form-select" v-model="receive.payment_method">
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row" v-if="receive.payment_method == 'bank'" :class="receive.payment_method == 'bank' ? '' : 'd-none'">
                                <label class="form-label col-4 col-md-3" for="account_id">Account:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="banks" v-model="selectedBank" label="display_name"></v-select>
                                </div>
                            </div>
                            <div class="mb-1 row" v-if="receive.type == 'customer'" :class="receive.type == 'customer' ? '' : 'd-none'">
                                <label class="form-label col-4 col-md-3" for="customer_id">Customer:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="customers" v-model="selectedCustomer" label="display_name" @input="onChangeCustomer"></v-select>
                                </div>
                            </div>
                            <div class="mb-1 row" v-if="receive.type == 'supplier'" :class="receive.type == 'supplier' ? '' : 'd-none'">
                                <label class="form-label col-4 col-md-3" for="supplier_id">Supplier:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="suppliers" v-model="selectedSupplier" label="display_name" @input="onChangeSupplier"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="date">Date:</label>
                                <div class="col-8 col-md-9">
                                    <input type="date" class="form-control" @change="getReceive" autocomplete="off" id="date" name="date" v-model="receive.date" :readonly="role == 'Superadmin' || role=='admin' ? false : true" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="previous_due">Prev. Due:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" readonly class="form-control" autocomplete="off" id="previous_due" name="previous_due" v-model="receive.previous_due" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="note">Note:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="note" name="note" v-model="receive.note" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="amount">Amount:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" class="form-control" autocomplete="off" id="amount" name="amount" v-model="receive.amount" />
                                </div>
                            </div>
                            <div class="mt-1 row">
                                <div class="col-12 col-md-12 text-end">
                                    <button class="btn btn-danger" type="button">Reset</button>
                                    <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                        <span v-if="receive.id == ''">Save</span>
                                        <span v-if="receive.id != ''">Update</span>
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
        <vue-good-table :columns="columns" :rows="receives" :fixed-header="false" :pagination-options="{
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
        el: "#receive",
        data() {
            return {
                receive: {
                    id: '',
                    invoice: "",
                    date: moment().format('YYYY-MM-DD'),
                    type: 'customer',
                    payment_method: 'cash',
                    amount: 0,
                    previous_due: 0,
                    note: ''
                },
                receives: [],
                banks: [],
                selectedBank: null,
                customers: [],
                selectedCustomer: null,
                suppliers: [],
                selectedSupplier: null,

                role: "{{auth()->user()->role}}",
                loading: true,
                onProgress: false,
            }
        },

        computed: {
            columns() {
                return [{
                        label: "Invoice",
                        field: 'invoice'
                    },
                    {
                        label: "Date",
                        field: 'date'
                    },
                    {
                        label: this.receive.type === 'customer' ? 'Customer' : 'Supplier',
                        field: "name"
                    },
                    {
                        label: "Payment Method",
                        field: 'payment_method'
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
                ];
            }
        },

        created() {
            this.onChangeType();
            this.getBank();
            this.getReceive();
        },

        methods: {
            getBank() {
                axios.post(`/get-bank`)
                    .then(res => {
                        this.banks = res.data;
                    })
            },
            getCustomer() {
                axios.post(`/get-customer`)
                    .then(res => {
                        this.customers = res.data;
                    })
            },
            getSupplier() {
                axios.post(`/get-supplier`)
                    .then(res => {
                        this.suppliers = res.data;
                    })
            },
            getReceive() {
                this.loading = true;
                let filter = {
                    dateFrom: this.receive.date,
                    dateTo: this.receive.date,
                    type: this.receive.type
                }
                axios.post(`/get-receive`, filter)
                    .then(res => {
                        this.receives = res.data.map((item, index) => {
                            item.sl = index + 1;
                            item.name = item.type == 'customer' ? `${item.customer?.name} - ${item.customer?.code}` : `${item.supplier?.name} - ${item.supplier?.code}`;
                            return item;
                        });
                        this.loading = false;
                    })
            },
            onChangeType() {
                if (this.receive.type == 'customer') {
                    this.receive.invoice = "{{transactionInvoice('Receive', 'R', session('branch')->id, 'customer')}}"
                    this.getCustomer();
                } else {
                    this.receive.invoice = "{{transactionInvoice('Receive', 'R', session('branch')->id, 'supplier')}}"
                    this.getSupplier();
                }
                this.getReceive();
            },
            async onChangeSupplier() {
                if (this.selectedSupplier == null) {
                    return;
                }
                if (this.selectedSupplier.id != '') {
                    await axios.post(`/get-supplierDue`, {
                        supplierId: this.selectedSupplier.id
                    }).then(res => {
                        this.receive.previous_due = res.data[0].due;
                    })
                }
            },
            async onChangeCustomer() {
                if (this.selectedCustomer == null) {
                    return;
                }
                if (this.selectedCustomer.id != '') {
                    await axios.post(`/get-customerDue`, {
                        customerId: this.selectedCustomer.id
                    }).then(res => {
                        this.receive.previous_due = res.data[0].due;
                    })
                }
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.receive.id);
                formdata.append('type', this.receive.type);
                if (this.receive.type == 'customer') {
                    formdata.append('customer_id', this.selectedCustomer ? this.selectedCustomer.id : '');
                }
                if (this.receive.type == 'supplier') {
                    formdata.append('supplier_id', this.selectedSupplier ? this.selectedSupplier.id : '');
                }
                if (this.receive.payment_method == 'bank') {
                    formdata.append('bank_id', this.selectedBank ? this.selectedBank.id : '');
                }
                let url = this.receive.id != '' ? `/update-receive` : `/receive`

                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.receive.invoice = res.data.invoice;
                        this.getReceive();
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
                let keys = Object.keys(this.receive);
                keys.forEach(item => {
                    this.receive[item] = row[item];
                })
                this.selectedCustomer = this.customers.find(item => item.id == row.customer_id);
                this.selectedSupplier = this.suppliers.find(item => item.id == row.supplier_id);
                this.selectedBank = this.banks.find(item => item.id == row.bank_id);
                setTimeout(() => {
                    this.payment.previous_due = row.previous_due;
                }, 1500);
            },

            deleteData(row) {
                if (!confirm("Are you sure ?")) {
                    return;
                }
                axios.post(`/delete-transaction`, {
                        id: row.id,
                        type: row.type
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getReceive();
                        }
                    })
            },

            clearData() {
                this.receive.id = '';
                this.receive.date = moment().format('YYYY-MM-DD');
                this.receive.amount = 0;
                this.receive.previous_due = 0;
                this.receive.note = ''
                this.selectedBank = null;
                this.selectedCustomer = null;
                this.selectedSupplier = null;
                this.onProgress = false;
            }
        },
    })
</script>
@endpush