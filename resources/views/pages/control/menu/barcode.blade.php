@extends('master')

@section('title', 'Barcode Genrate')
@section('breadcrumb', 'Barcode Genrate')
@push('style')
<style scoped>
    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }
    }
</style>
@endpush
@section('content')
<div id="barcodeList">
    <div class="card m-0 mb-2">
        <div class="card-body p-2">
            <a href="/product" class="btn btn-danger px-4">Back To Product Page</a>
        </div>
    </div>
    <form @submit.prevent="barcodeGenerate">
        <div class="card m-0">
            <div class="card-body py-3 px-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row mb-1">
                            <label class="form-label col-4 col-md-4">Product Code:</label>
                            <div class=" col-8 col-md-7">
                                <input type="text" class="form-control" id="code" value="{{$product->code}}" readonly>
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <label class="form-label col-4 col-md-4">Sale Rate:</label>
                            <div class="col-8 col-md-7">
                                <input type="number" min="0" step="any" id="price" class="form-control" value="{{number_format($product->sale_rate, 2, '.', '')}}" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <label class="form-label col-4 col-md-4">Quantity:</label>
                            <div class="col-8 col-md-7">
                                <input type="number" min="0" step="1" id="quantity" class="form-control" autocomplete="off">
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">
                        <div class="form-group row mb-1">
                            <label class="form-label col-4 col-md-4">Product Name:</label>
                            <div class="col-8 col-md-7">
                                <input type="text" class="form-control" id="name" value="{{$product->name}}" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <label v-if="is_single" class="form-label col-4 col-md-4">Printer Settings:</label>                            
                            <div class="col-4 col-md-4 d-flex align-items-center gap-2" style="padding-right: 0;" v-if="is_single">
                                <span>X:</span>
                                <input type="number" step="0.01" min="0" class="form-control" v-model="xAxis" />
                            </div>
                            <div class="col-3 col-md-3 d-flex align-items-center gap-2" v-if="is_single">
                                <span>Y:</span>
                                <input type="number" step="0.01" min="0" class="form-control" v-model="yAxis" />
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <div class="col-5 col-md-5 d-flex align-items-center gap-1" style="padding-right: 0;">
                                <input type="checkbox" v-model="is_single" id="is_single" @change="products = []" style="width: 18px; height: 18px;"/>
                                <label for="is_single" style="margin: 0;cursor:pointer;">Single Barcode</label>
                            </div>
                            <div class="col-7 col-md-6 text-end">
                                @if(buttonAction('entry'))
                                <button :disabled="onProgress" type="submit" class="btn btn-primary btn-padding">Generate</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row mt-2" :class="isLoading == false ? '' : 'd-none'" v-if="isLoading == false">
        <div class="col-12 text-center">
            Loading...
        </div>
    </div>
    <div class="row mt-2" :class="isLoading ? '' : 'd-none'" v-if="isLoading">
        <div style="display:none;" v-bind:style="{display: products.length > 0 && isLoading ? '' : 'none'}">
            <div class="row" style="display: flex;justify-content:center;">
                <div class="col-md-10 text-end">
                    <a href="" style="cursor: pointer;" @click.prevent="print">
                        <i class="bi bi-printer"></i> Print
                    </a>
                </div>
            </div>
            <div class="row" style="display: flex;justify-content:center;">
                <div class="output col-md-8 page-break">
                    <div v-if="!is_single" style="padding:3px;float: left; height: 95px; width: 131px; border: 1px solid #ddd;" v-for="(item, sl) in products">
                        <div style="width: 131px; text-align: center; float: right;">
                            <!-- <p class="article" style="font-size: 12px;margin:0;" :style="{marginLeft: item.article != '' ? '14px !important': '5px !important'}">@{{item.article}}</p> -->
                            <p style="font-size: 10px;margin:0px 0px 2px 1px;padding:2px 0 0 0;font-weight: bolder;text-align: center;line-height: 1;">@{{item.name}}</p>
                            <img class="barcode" style="line-height: 0;" />
                            <p style="margin:0;font-size: 12px;margin-top:-3px; text-align: center;font-weight: 900;">@{{item.code}}</p>
                            <p style="margin:0;margin-top:-1px;text-align: center;font-size: 12px;font-weight: bolder;">BDT @{{item.sale_rate}}</p>
                        </div>
                    </div>
                    <div v-if="is_single" style="float:left;margin:0px;padding:0; overflow:hidden;border:1px solid #ccc;box-sizing:border-box;border-bottom:none" :style="[{width: xAxis+'in', height: yAxis+'in'}]" v-for="(item, sl) in products">
                        <div style="text-align: center;margin:0;padding:0px 0px 0px 0px;" :style="[{width: xAxis+'in', height: yAxis+'in'}]">
                            <!-- <p class="article" style="font-size: 12px;margin:0;" :style="{marginLeft: item.article != '' ? '13px !important': '5px !important'}">@{{item.article}}</p> -->
                            <p style="font-size: 10px;margin:0px 0px 2px 1px;padding:2px 0 0 0;font-weight: bolder;text-align: center;line-height: 1;">@{{item.name}}</p>
                            <img class="singlebarcode" style="line-height: 0;" />
                            <p style="margin:0;font-size: 12px;margin-top:-3px; text-align: center;font-weight: 900;">@{{item.code}}</p>
                            <p style="margin:0;margin-top:-1px;text-align: center;font-size: 12px;font-weight: bolder;">BDT @{{item.sale_rate}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('js')
<script src="{{asset('backend')}}/js/JsBarcode.all.min.js"></script>
<script>
    let code = "{{$product->code}}";
    JsBarcode(".barcode", code, {
        format: "CODE128",
        width: 1,
        height: 25,
        fontSize: 12,
        margin: 0,
        displayValue: false
    });
    JsBarcode(".singlebarcode", code, {
        format: "CODE128",
        width: 1,
        height: 40,
        fontSize: 12,
        margin: 0,
        displayValue: false
    });

    new Vue({
        el: '#barcodeList',
        data() {
            return {
                products: [],

                onProgress: false,
                isLoading: null,
                is_single: true,
                xAxis: 1.5,
                yAxis: 1,
            }
        },

        methods: {
            async barcodeGenerate(event) {
                this.products = [];
                this.onProgress = true;
                this.isLoading = false;
                await new Promise(resolve => setTimeout(resolve, 500));
                let code = $("#code").val();
                let name = $("#name").val();
                let qty = $("#quantity").val();
                let price = $("#price").val();
                if (qty == '' || qty == 0) {
                    toastr.error('Quantity is empty');
                    this.onProgress = false;
                    this.isLoading = null;
                    return;
                }
                var product = {
                    code: code,
                    name: name,
                    sale_rate: parseFloat(price).toFixed(2)
                }
                for (let index = 0; index < qty; index++) {
                    this.products.push(product);
                }

                this.onProgress = false;
                this.isLoading = true;
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Barcode Generate"
                const printWindow = document.createElement('iframe');
                document.body.appendChild(printWindow);
                printWindow.srcdoc = `
                    ${document.querySelector('.output').innerHTML}
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