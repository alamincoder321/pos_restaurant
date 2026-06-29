@extends('master')

@section('title', 'Pending Order Record')
@section('breadcrumb', 'Pending Order Record')
@push('style')
<style>
    .invoice-card {
        border: 1px solid #ddd;
        cursor: pointer;
        transition: box-shadow 0.3s ease-in-out;
    }

    .invoice-card:hover {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        transition: box-shadow 0.3s ease-in-out;
    }

    .invoice-card .card-body p {
        margin-bottom: 5px;
        font-size: 11px;
    }

    .invoice-card .card-body strong {
        display: inline-block;
        width: 70px;
    }

    .invoice-total {
        border-top: 1px dashed #999;
        margin-top: 5px;
        padding-top: 5px;
        font-size: 12px;
        font-weight: bold;
        color: #0d6efd;
    }
</style>
@endpush
@section('content')
<div id="orderList">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showReport" class="form-inline">
                        <div class="form-group">
                            <label for="searchType">SearchType</label>
                            <select id="searchType" class="form-select" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="customer">By Customer</option>
                                <option value="user">By User</option>
                            </select>
                        </div>

                        <div class="form-group" :class="searchType == 'customer' ? '' : 'd-none'" v-if="searchType == 'customer'">
                            <label for="customer">Customer</label>
                            <v-select :options="customers" v-model="selectedCustomer" label="display_name"></v-select>
                        </div>
                        <div class="form-group" :class="searchType == 'user' ? '' : 'd-none'" v-if="searchType == 'user'">
                            <label for="user">User</label>
                            <v-select :options="users" v-model="selectedUser" label="name"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="dateFrom">From</label>
                            <input type="date" class="form-control" v-model="dateFrom">
                        </div>
                        <div class="form-group">
                            <label for="dateFrom">To</label>
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
                <div class="card-body py-3 px-3">
                    <div class="row">
                        <div class="col-md-3" v-for="sale in sales" :key="sale.id">
                            <div class="card box-shadow invoice-card mb-2">
                                <div class="card-header">
                                    Invoice #@{{ sale.invoice }}
                                </div>
                                <div class="card-body">
                                    <p><strong><i class="bi bi-calendar3"></i> Date</strong> : @{{ sale.date | dateFormat('DD/MM/YYYY') }}</p>
                                    <p><strong><i class="bi bi-person"></i> Customer</strong> : @{{ sale.customer_name }}</p>
                                    <p><strong><i class="bi bi-telephone"></i> Phone</strong> : @{{ sale.customer_phone }}</p>
                                    <p><strong><i class="bi bi-geo-alt"></i> Address</strong> : @{{ sale.customer_address }}</p>

                                    <div class="invoice-total">
                                        Total : ৳ @{{ sale.total }}
                                    </div>
                                </div>
                                <div class="card-footer text-center px-2 py-1">
                                    <div class="input-group">
                                        <button type="button" @click="saleStatusChange(sale.id, 'cancelled')" class="btn btn-danger btn-sm w-50">Cancel</button>
                                        <button type="button" @click="saleStatusChange(sale.id, 'completed')" class="btn btn-success btn-sm w-50">Completed</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" v-if="sales.length == 0">
                            <div class="alert alert-info text-center mb-0">
                                No pending order found.
                            </div>
                        </div>
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
        el: '#orderList',
        data: {
            searchType: '',
            dateFrom: moment().format('YYYY-MM-DD'),
            dateTo: moment().format('YYYY-MM-DD'),
            sales: [],
            customers: [],
            selectedCustomer: null,
            users: [],
            selectedUser: null,
            isLoading: null
        },

        filters: {
            dateFormat(d, format) {
                return moment(d).format(format);
            }
        },

        created() {
            this.showReport();
        },

        methods: {
            getUser() {
                axios.post('/get-user')
                    .then(res => {
                        this.users = res.data;
                    })
            },
            getCustomer() {
                axios.post('/get-customer')
                    .then(res => {
                        this.customers = res.data;
                    })
            },

            onChangeSearchType() {
                this.sales = [];
                this.customers = [];
                this.selectedCustomer = null;
                this.users = [];
                this.selectedUser = null;
                this.isLoading = null;
                if (this.searchType == 'customer') {
                    this.getCustomer();
                } else if (this.searchType == 'user') {
                    this.getUser();
                }
            },

            showReport() {
                let filter = {
                    userId: this.selectedUser ? this.selectedUser.id : '',
                    customerId: this.selectedCustomer ? this.selectedCustomer.id : '',
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo,
                    orderStatus: ['pending']
                }
                this.isLoading = false;
                axios.post('/get-sale', filter)
                    .then(res => {
                        this.sales = res.data
                        this.isLoading = true;
                    })
            },
            saleStatusChange(saleId, status) {
                if (!confirm('Are you sure you want to ' + status + ' this sale?')) {
                    return;
                }
                axios.post('sale-status-change', {
                        id: saleId,
                        status: status
                    })
                    .then(async res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.showReport();
                            if (status == 'completed') {
                                if (confirm('Do you want to print the invoice?')) {
                                    location.href = `/possaleInvoice/${saleId}?print=1`;
                                    printWindow.focus();
                                }
                            }
                        }
                    })
            }
        },
    })
</script>
@endpush