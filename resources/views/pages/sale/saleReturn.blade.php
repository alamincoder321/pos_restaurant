@extends('master')

@section('title', 'Order Return')
@section('breadcrumb', 'Order Return')
@push('style')
<style>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
        vertical-align: middle !important;
    }

    .table>tbody>tr>td {
        vertical-align: middle !important;
    }

    .v-select {
        width: 300px !important;
    }
</style>
@endpush
@section('content')
<div id="orderreturnList">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showList" class="form-inline">
                        <div class="form-group">
                            <label for="customer_id">Customer</label>
                            <v-select :options="customers" v-model="selectedCustomer" label="display_name" @input="onChangeCustomer" @search="onSearchCustomer" placeholder="Search Customer"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="brand_id">Invoice</label>
                            <v-select :options="invoices" v-model="selectedInvoice" label="display_name" placeholder="Search Invoice"></v-select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm">Show Invoice</button>
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
                    <div id="reportContent" style="overflow-x: auto;">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>ProductName</th>
                                    <th>Sale Qty</th>
                                    <th>Sale Total</th>
                                    <th>Already Return Qty</th>
                                    <th>Already Return Rate</th>
                                    <th>Return Qty</th>
                                    <th>Return Rate</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in details">
                                    <td class="text-center" v-text="index + 1"></td>
                                    <td class="text-left" v-text="`${item.name} - ${item.code}`"></td>
                                    <td class="text-center" v-text="item.quantity"></td>
                                    <td class="text-center" v-text="item.total"></td>
                                    <td class="text-center" v-text="item.already_return_quantity"></td>
                                    <td class="text-center" v-text="item.already_return_amount"></td>
                                    <td class="text-center">
                                        <input type="number" class="text-center" min="0" step="any" v-model="item.return_quantity" @input="qtyRateChange(index)" />
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="text-center" min="0" step="any" v-model="item.sale_rate" @input="qtyRateChange(index)" />
                                    </td>
                                    <td class="text-center" v-text="item.returnTotal"></td>
                                </tr>
                                <tr>
                                    <td colspan="6">
                                        <textarea name="note" id="note" placeholder="Enter note here..." class="form-control" v-model="saleReturn.note"></textarea>
                                    </td>
                                    <td class="text-center">
                                        @{{ details.reduce((acc, item) => {
                                            return acc + (item.return_quantity !== "" && !isNaN(item.return_quantity) ? parseFloat(item.return_quantity) : 0);
                                        }, 0) }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" :disabled="onProgress" @click="saveData" class="btn btn-success btn-sm d-block w-100">Save Return</button>
                                    </td>
                                    <td class="text-center">
                                        @{{ details.reduce((acc, item) => {
                                            return acc + (item.returnTotal !== "" && !isNaN(item.returnTotal) ? parseFloat(item.returnTotal) : 0);
                                        }, 0).toFixed(2) }}
                                    </td>
                                </tr>
                                <tr :class="details.length == 0 ? '' : 'd-none'" v-if="details.length == 0">
                                    <td :colspan="5" class="text-center">Not Found Data</td>
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
        el: '#orderreturnList',
        data: {
            details: [],
            saleReturn: {
                date: moment().format('YYYY-MM-DD'),
                customer_id: '',
                sale_id: '',
                note: '',
            },
            customers: [],
            selectedCustomer: null,
            invoices: [],
            selectedInvoice: null,
            isLoading: null,
            onProgress: false,
        },

        created() {
            this.getCustomer();
        },

        methods: {
            getCustomer() {
                axios.post('/get-customer', {
                        forSearch: 'yes'
                    })
                    .then(res => {
                        this.customers = res.data;
                        this.customers.unshift({
                            id: '',
                            display_name: 'Walk In Customer',
                            name: 'Walk In Customer',
                            phone: '',
                            address: '',
                            type: 'general'
                        })
                    })
            },

            async onSearchCustomer(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get-customer", {
                            search: val,
                        })
                        .then(res => {
                            this.customers = res.data;
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getCustomer();
                }
            },

            onChangeCustomer() {
                this.selectedInvoice = null;
                if (this.selectedCustomer != null) {
                    this.getSale();
                }
            },

            getSale() {
                axios.post('/get-sale', {
                        forSearch: 'yes',
                        customerId: this.selectedCustomer ? this.selectedCustomer.id : ""
                    })
                    .then(res => {
                        this.invoices = res.data;
                    })
            },

            async qtyRateChange(ind) {
                let item = this.details[ind];                
                if (parseFloat(+item.return_quantity + + item.already_return_quantity) > parseFloat(item.quantity)) {
                    item.return_quantity = item.quantity - item.already_return_quantity;
                }
                if (item.sale_rate < 0) {
                    item.sale_rate = 0;
                }
                item.returnTotal = (item.return_quantity * item.sale_rate).toFixed(2);
            },

            showList() {
                let filter = {
                    customerId: this.selectedCustomer ? this.selectedCustomer.id : '',
                    saleId: this.selectedInvoice ? this.selectedInvoice.id : '',
                }
                this.isLoading = false;
                axios.post('/get-sale-detailforreturns', filter)
                    .then(res => {
                        this.details = res.data.map(detail => {
                                detail.sale_detail_id = detail.id;
                                return detail;
                        })
                        this.saleReturn.customer_id = this.selectedCustomer ? this.selectedCustomer.id : '';
                        this.saleReturn.sale_id = this.selectedInvoice ? this.selectedInvoice.id : '';
                        this.isLoading = true;
                    })
            },

            saveData() {
                let details = this.details.filter(item => item.return_quantity > 0).map(item => {
                    return {
                        sale_detail_id: item.sale_detail_id,
                        menu_id: item.menu_id,
                        code: item.code,
                        name: item.name,
                        return_quantity: item.return_quantity,
                        sale_rate: item.sale_rate,
                        discount: item.discount,
                        returnTotal: item.returnTotal,
                        is_service: item.is_service
                    }
                });

                this.saleReturn.total = details.reduce((pr, cu) => {return pr + parseFloat(cu.returnTotal)}, 0).toFixed(2);
                
                let filter = {
                    saleReturn: this.saleReturn,
                    carts: details
                }
                this.onProgress = true;
                axios.post('/sale-return', filter)
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.showList();
                        } else {
                            toastr.error(res.data.message);
                        }
                        this.onProgress = false;
                    })
                    .catch(err => {
                        this.onProgress = false
                        var r = JSON.parse(err.request.response);
                        console.log(r);

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
        },
    })
</script>
@endpush