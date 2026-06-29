@extends('master')

@section('title', 'Kitchen Sale Invoice')
@section('breadcrumb', 'Kitchen Sale Invoice')
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
    <div class="col-md-10 offset-md-1">
        <kitchen-invoice-preview
            :visible="printInvoice"
            :showable="showInvoice"
            :cart="carts"
            :customer="selectedCustomer"
            :sale="sale"
            :username="username"
            @close="printInvoice = false"></kitchen-invoice-preview>
    </div>
</div>
@endsection

@push('js')
<script src="{{asset('component')}}/KitchenInvoicePreview.js"></script>
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
            isLoading: null,
            isPrint: null
        },

        created() {
            this.showReport();
        },

        methods: {
            async showReport() {
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