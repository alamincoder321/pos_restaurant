@extends('master')

@section('title', 'Sale Invoice')
@section('breadcrumb', 'Sale Invoice')
@push('style')
<style>
    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
@endpush
@section('content')
<div id="saleInvoice">
    <div class="card py-5">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center">
                <a class="btn btn-danger btn-sm" onclick="window.close();"><i class="bi bi-backspace"></i> Back</a>
                <a class="btn btn-success btn-sm" href="" @click.prevent="printInvoice = true" title="Print"><i class="bi bi-printer"></i> Print</a>
            </div>
        </div>
        <div class="col-md-10 offset-md-1">
            <invoice-preview
                :visible="printInvoice"
                :showable="showInvoice"
                :cart="carts"
                :customer="selectedCustomer"
                :sale="sale"
                :username="username"
                @close="printInvoice = false"></invoice-preview>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{asset('component')}}/SaleInvoicePreview.js"></script>
<script>
    new Vue({
        el: '#saleInvoice',
        data: {
            saleId: "{{$id}}",
            sale: {},
            selectedCustomer: {},
            carts: [],
            username: "",
            showInvoice: false,
            printInvoice: 1,
            isLoading: null
        },

        created() {
            this.showReport();
        },

        methods: {
            showReport() {
                axios.post('/get-sale', {
                        saleId: this.saleId
                    })
                    .then(res => {
                        this.sale = res.data[0];
                        this.username = this.sale?.ad_user?.username;
                        this.carts = this.sale.details;
                        this.selectedCustomer = {
                            id: this.sale.customer_id ?? '',
                            code: this.sale.customer_code,
                            name: this.sale.customer_name,
                            phone: this.sale.customer_phone,
                            address: this.sale.customer_address,
                            type: this.sale.customer_type
                        }
                        this.showInvoice = true;
                    })
            }
        }
    })
</script>
@endpush