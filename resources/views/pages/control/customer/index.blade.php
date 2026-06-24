@extends('master')

@section('title', 'Customer List')
@section('breadcrumb', 'Customer List')
@push('style')
<style>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
    }
</style>
@endpush
@section('content')
<div id="customerList">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showList" class="form-inline">
                        <div class="form-group">
                            <label for="searchType">SearchType</label>
                            <select id="searchType" class="form-select" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="area">By Area</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="searchType">Type</label>
                            <select id="searchType" class="form-select" v-model="customer_type">
                                <option value="">All</option>
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                        </div>
                        <div class="form-group" :class="searchType == 'area' ? '' : 'd-none'" v-if="searchType == 'area'">
                            <label for="searchType">Area</label>
                            <v-select :options="areas" v-model="selectedArea" label="name"></v-select>
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
                <div class="card-body pt-1 pb-3 px-2">
                    <div class="text-end">
                        <a href="" @click.prevent="print" title="Print"><i class="bi bi-printer"></i></a>
                    </div>
                    <div id="reportContent" style="overflow-x: auto;">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Owner</th>
                                    <th>Customer_Type</th>
                                    <th>Phone</th>
                                    <th>Area</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in customers">
                                    <td v-html="index + 1"></td>
                                    <td v-html="item.code"></td>
                                    <td v-html="item.name"></td>
                                    <td v-html="item.owner"></td>
                                    <td v-html="item.customer_type"></td>
                                    <td v-html="item.phone"></td>
                                    <td v-html="item.area?.name"></td>
                                    <td v-html="item.address"></td>
                                </tr>
                                <tr :class="customers.length == 0 ? '' : 'd-none'" v-if="customers.length == 0">
                                    <td colspan="8" class="text-center">Not Found Data</td>
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
        el: '#customerList',
        data: {
            searchType: '',
            customer_type: '',
            customers: [],
            areas: [],
            selectedArea: null,
            isLoading: null
        },

        created() {
            this.getArea();
        },

        methods: {
            getArea() {
                axios.post('/get-area')
                    .then(res => {
                        this.areas = res.data;
                    })
            },

            onChangeSearchType() {
                this.suppliers = [];
                this.selectedArea = null;
                this.customer_type = "";
                this.isLoading = null;
            },

            showList() {
                let filter = {
                    areaId: this.selectedArea ? this.selectedArea.id : '',
                    customer_type: this.customer_type
                }
                this.isLoading = false;
                axios.post('/get-customer', filter)
                    .then(res => {
                        this.customers = res.data
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Customer List"
                const printWindow = document.createElement('iframe');
                document.body.appendChild(printWindow);
                printWindow.srcdoc = `
                    <style>
                        .table>:not(caption)>*>* {
                            font-size: 11px !important;
                        }
                        address p{
                            margin: 0 !important;
                        }                                        
                    </style>

                    @include('layouts.headerInfo')
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h5>Customer List</h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                ${document.getElementById('reportContent').innerHTML}
                            </div>
                        </div>
                    </div>
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