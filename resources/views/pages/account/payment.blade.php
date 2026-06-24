@extends('master')

@section('title', 'Payment Amount')
@section('breadcrumb', 'Payment Amount')
@section('content')
<div class="row" id="payment">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Payment Amount Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="invoice">Invoice:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="invoice" name="invoice" v-model="payment.invoice" readonly />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="account_id">Type:</label>
                                <div class="col-8 col-md-9">
                                    <select name="type" @change="onChangeType" class="form-select" v-model="payment.type">
                                        <option :disabled="payment.id != '' ? true : false" value="customer">Customer</option>
                                        <option :disabled="payment.id != '' ? true : false" value="supplier">Supplier</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="account_id">Method:</label>
                                <div class="col-8 col-md-9">
                                    <select name="payment_method" class="form-select" v-model="payment.payment_method">
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row" v-if="payment.payment_method == 'bank'" :class="payment.payment_method == 'bank' ? '' : 'd-none'">
                                <label class="form-label col-4 col-md-3" for="account_id">Account:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="banks" v-model="selectedBank" label="display_name"></v-select>
                                </div>
                            </div>
                            <div class="mb-1 row" v-if="payment.type == 'customer'" :class="payment.type == 'customer' ? '' : 'd-none'">
                                <label class="form-label col-4 col-md-3" for="account_id">Customer:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="customers" v-model="selectedCustomer" label="display_name" @input="onChangeCustomer"></v-select>
                                </div>
                            </div>
                            <div class="mb-1 row" v-if="payment.type == 'supplier'" :class="payment.type == 'supplier' ? '' : 'd-none'">
                                <label class="form-label col-4 col-md-3" for="account_id">Supplier:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="suppliers" v-model="selectedSupplier" label="display_name" @input="onChangeSupplier"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="date">Date:</label>
                                <div class="col-8 col-md-9">
                                    <input type="date" class="form-control" @change="getPayment" autocomplete="off" id="date" name="date" v-model="payment.date" :readonly="role == 'Superadmin' || role=='admin' ? false : true" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="previous_due">Prev. Due:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" readonly class="form-control" autocomplete="off" id="previous_due" name="previous_due" v-model="payment.previous_due" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="note">Note:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="note" name="note" v-model="payment.note" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="amount">Amount:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" step="any" min="0" class="form-control" autocomplete="off" id="amount" name="amount" v-model="payment.amount" />
                                </div>
                            </div>
                            <div class="mt-1 row">
                                <div class="col-12 col-md-12 text-end">
                                    <button class="btn btn-danger" type="button">Reset</button>
                                    <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                        <span v-if="payment.id == ''">Save</span>
                                        <span v-if="payment.id != ''">Update</span>
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
        <vue-good-table :columns="columns" :rows="payments" :fixed-header="false" :pagination-options="{
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
        el: "#payment",
        data() {
            return {
                payment: {
                    id: '',
                    invoice: "",
                    date: moment().format('YYYY-MM-DD'),
                    type: 'supplier',
                    payment_method: 'cash',
                    amount: 0,
                    previous_due: 0,
                    note: ''
                },
                payments: [],
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
                return [
                    { label: "Invoice", field: 'invoice' },
                    { label: "Date", field: 'date' },
                    { label: this.payment.type === 'customer' ? 'Customer' : 'Supplier', field: "name" },
                    { label: "Payment Method", field: 'payment_method' },
                    { label: "Amount", field: 'amount' },
                    { label: "Note", field: 'note' },
                    { label: "Added_By", field: 'ad_user.username' },
                    { label: "Updated_By", field: 'up_user.username' },
                    { label: "Action", field: "before" }
                ];
            }
        },

        created() {
            this.onChangeType();
            this.getBank();
            this.getPayment();
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
            getPayment() {
                this.loading = true;
                let filter = {
                    dateFrom: this.payment.date,
                    dateTo: this.payment.date,
                    type: this.payment.type
                }
                axios.post(`/get-payment`, filter)
                    .then(res => {
                        this.payments = res.data.map((item, index) => {
                            item.sl = index + 1;
                            item.name = item.type == 'customer' ? `${item.customer?.name} - ${item.customer?.code}` : `${item.supplier?.name} - ${item.supplier?.code}`;
                            return item;
                        });
                        this.loading = false;
                    })
            },
            onChangeType() {
                if (this.payment.type == 'customer') {
                    this.payment.invoice = "{{transactionInvoice('Payment', 'P', session('branch')->id, 'customer')}}"
                    this.getCustomer();
                } else {
                    this.payment.invoice = "{{transactionInvoice('Payment', 'P', session('branch')->id, 'supplier')}}"
                    this.getSupplier();
                }
                this.getPayment();
            },
            async onChangeSupplier() {
                if (this.selectedSupplier == null) {
                    return;
                }
                if (this.selectedSupplier.id != '') {
                    await axios.post(`/get-supplierDue`, {
                        supplierId: this.selectedSupplier.id
                    }).then(res => {
                        this.payment.previous_due = res.data[0].due;
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
                        this.payment.previous_due = res.data[0].due;
                    })
                }
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.payment.id);
                formdata.append('type', this.payment.type);
                if (this.payment.type == 'customer') {
                    formdata.append('customer_id', this.selectedCustomer ? this.selectedCustomer.id : '');
                }
                if (this.payment.type == 'supplier') {
                    formdata.append('supplier_id', this.selectedSupplier ? this.selectedSupplier.id : '');
                }
                if (this.payment.payment_method == 'bank') {
                    formdata.append('bank_id', this.selectedBank ? this.selectedBank.id : '');
                }
                let url = this.payment.id != '' ? `/update-payment` : `/payment`

                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.payment.invoice = res.data.invoice;
                        this.getPayment();
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
                let keys = Object.keys(this.payment);
                keys.forEach(item => {
                    this.payment[item] = row[item];
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
                            this.getPayment();
                        }
                    })
            },

            clearData() {
                this.payment.id = '';
                this.payment.date = moment().format('YYYY-MM-DD');
                this.payment.amount = 0;
                this.payment.previous_due = 0;
                this.payment.note = ''
                this.selectedBank = null;
                this.selectedCustomer = null;
                this.selectedSupplier = null;
                this.onProgress = false;
            }
        },
    })
</script>
@endpush