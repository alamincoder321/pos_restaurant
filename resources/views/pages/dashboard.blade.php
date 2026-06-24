@extends('master')

@php
$panel = session('panel');
@endphp

@section('title')
{{ucfirst($panel)}}
@endsection
@section('breadcrumb')
{{ucfirst($panel)}}
@endsection

@push('style')
<style scoped>
    .displayFlex {
        transition: 1ms ease-in-out;
        height: 115px;
    }

    .displayFlex:hover {
        background: linear-gradient(45deg, #39bcf1, #bbabab70);
        color: #fff;
    }

    .displayFlex:hover .card-body i {
        border-color: #fff !important;
    }

    .displayFlex .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .displayFlex .card-body i {
        font-size: 25px;
        border: 1px solid gray;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 3px;
    }

    .textColor {
        padding: 0 15px;
        font-size: 50px;
        font-weight: 700;
        background: linear-gradient(90deg, #ff0080, #7928ca);
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endpush

@section('content')
<div class="row" id="dashboard">
    @if ($panel == 'dashboard' || $panel == '')
    <div class="col-md-12 my-5">
        <div style="display: flex; align-items: center; text-align: center; margin: 0;border: 1px solid #000;">
            <div style="flex: 1; border-bottom: 1px solid #000;"></div>
            <div class="textColor">
                Welcome To Dashboard
            </div>
            <div style="flex: 1; border-bottom: 1px solid #000;"></div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="row m-0">
            <div class="col-12 col-md-8 px-0" style="border: 1px solid #959595; padding: 10px;">
                <h6 style="margin: 0;border-bottom: 2px solid #000;text-align:center;padding-bottom: 6px;">Monthly Sales Overview</h6>
                <apexchart v-if="showChart" type="area" height="200" :options="chartOptions" :series="series"></apexchart>
            </div>
            <div class="col-12 col-md-4 px-0" style="border: 1px solid #959595; padding: 10px;">
                <h6 style="margin: 0;border-bottom: 2px solid #000;text-align:center;padding-bottom: 6px;">Top Sale Menus</h6>
                <apexchart v-if="showChart" type="pie" height="200" :options="piechartOptions" :series="pieseries"></apexchart>
            </div>
        </div>
    </div>

    @elseif($panel == 'OrderPanel')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-10 col-12 offset-md-1 mb-5">
                <div class="card mb-0" style="box-shadow: 0px 5px 1px 2px #058ed152;">
                    <div class="card-body p-3 text-center">
                        <h2 class="m-0">Order Panel</h2>
                    </div>
                </div>
            </div>
            @if(checkAccess('sale'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/pos">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-cart-dash"></i>
                            <span>Order Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('saleRecord'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/sale-record">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-file-text"></i>
                            <span>Order Record</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('saleReturn'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/sale-return">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-arrow-return-left"></i>
                            <span>Order Return Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(checkAccess('saleReturnRecord'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/sale-return-record">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-file-text"></i>
                            <span>Order Return Record</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(checkAccess('dailyReport'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/dailyReport">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-book"></i>
                            <span>Daily Report</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>

    @elseif($panel == 'AccountPanel')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-10 col-12 offset-md-1 mb-5">
                <div class="card mb-0" style="box-shadow: 0px 5px 1px 2px #058ed152;">
                    <div class="card-body p-3 text-center">
                        <h2 class="m-0">Account Panel</h2>
                    </div>
                </div>
            </div>
            @if(checkAccess('expense'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/expense">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-clipboard-minus"></i>
                            <span>Expense Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('income'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/income">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-duffle"></i>
                            <span>Income Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('receive'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/receive">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-cash-stack"></i>
                            <span>Receive</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('payment'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/payment">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-person-workspace"></i>
                            <span>Payment</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('bankTransaction'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/bankTransaction">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-bank"></i>
                            <span>Bank Transaction</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('bankTransactionRecord'))
            <div class="col-md-2 col-6 mb-3">
                <a href="">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list-ul"></i>
                            <span>Bank Transaction Record</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('accounthead'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/accounthead">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>AccountHead Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('bank'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/bank">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>Bank Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(checkAccess('investTransaction'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/invest-transaction">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>InvestTransaction</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(checkAccess('investTransactionList'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/invest-transaction-list">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list"></i>
                            <span>InvestTransaction List</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(checkAccess('invest'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/invest">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>InvestAccount Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>

    @elseif($panel == 'ReportPanel')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-10 col-12 offset-md-1 mb-5">
                <div class="card mb-0" style="box-shadow: 0px 5px 1px 2px #058ed152;">
                    <div class="card-body p-3 text-center">
                        <h2 class="m-0">Report Panel</h2>
                    </div>
                </div>
            </div>
            @if(checkAccess('profitLoss'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/profitLoss">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-journal-text"></i>
                            <span>Profit/Loss</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('cashLedger'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/cashLedger">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list"></i>
                            <span>Cash Ledger</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('bankLedger'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/bankLedger">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list"></i>
                            <span>Bank Ledger</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        
            @if(checkAccess('customerDue'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/customerDue">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-cash"></i>
                            <span>Customer Due</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('customerLedger'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/customerLedger">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list"></i>
                            <span>Customer Ledger</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(checkAccess('dailyReport'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/dailyReport">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-book"></i>
                            <span>Daily Report</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>

    @elseif($panel == 'HRPanel')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-10 col-12 offset-md-1 mb-5">
                <div class="card mb-0" style="box-shadow: 0px 5px 1px 2px #058ed152;">
                    <div class="card-body p-3 text-center">
                        <h2 class="m-0">HR Panel</h2>
                    </div>
                </div>
            </div>
            @if(checkAccess('salary'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/salary">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-receipt"></i>
                            <span>Salary Generate</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('salaryList'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/salaryList">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-file-text"></i>
                            <span>Salary Record</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('employee'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/employee">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-people"></i>
                            <span>Employee Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('employeeList'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/employeeList">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list-ul"></i>
                            <span>Employee List</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('department'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/department">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>Department Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('designation'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/designation">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>Designation Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>

    @elseif($panel == 'ControlPanel')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-10 col-12 offset-md-1 mb-5">
                <div class="card mb-0" style="box-shadow: 0px 5px 1px 2px #058ed152;">
                    <div class="card-body p-3 text-center">
                        <h2 class="m-0">Control Panel</h2>
                    </div>
                </div>
            </div>
            @if(checkAccess('menu'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/menu">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>Menu Entry</span>
                        </div>
                    </div>
                </a>
                @endif
            </div>
            @if(checkAccess('menuList'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/menuList">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list-ul"></i>
                            <span>Menu List</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            
            @if(checkAccess('customer'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/customer">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-person"></i>
                            <span>Customer Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('customerList'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/customerList">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-list-ul"></i>
                            <span>Customer List</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('area'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/area">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-globe"></i>
                            <span>Area Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('unit'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/unit">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-box"></i>
                            <span>Unit Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('category'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/category">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-tags"></i>
                            <span>Category Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            
            @if(checkAccess('floor'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/floor">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-plus-circle"></i>
                            <span>Floor Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            
            @if(checkAccess('table'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/table">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-table"></i>
                            <span>Table Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(checkAccess('user'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/user">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-person-fill-add"></i>
                            <span>User Entry</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            @if(checkAccess('companyProfile'))
            <div class="col-md-2 col-6 mb-3">
                <a href="/companyProfile">
                    <div class="card mb-0 displayFlex">
                        <div class="card-body p-3">
                            <i class="bi bi-house-fill"></i>
                            <span>Company Profile</span>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-apexcharts"></script>
<script>
    Vue.component('apexchart', VueApexCharts);
    new Vue({
        el: "#dashboard",
        data() {
            return {
                series: [{
                    name: 'Sales Amount',
                    data: []
                }],
                chartOptions: {
                    chart: {
                        height: 350,
                        type: 'area'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    xaxis: {
                        type: 'text',
                        categories: []
                    },
                },
                showChart: false,
                pieseries: [],
                piechartOptions: {
                    chart: {
                        width: 300,
                        height: 200,
                        type: 'pie',
                    },
                    labels: [],
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 400,
                                height: 250
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                },
            }
        },
        created() {
            this.getTopBusinessInfo();
        },
        methods: {
            getTopBusinessInfo() {
                axios.post("/get-top-business-info", {})
                    .then(response => {
                        const data = response.data;
                        let monthlySale = data.monthlySaleData;

                        this.series = [{
                            name: 'Sales',
                            data: monthlySale.map(s => Number(s.total))
                        }];

                        this.chartOptions = {
                            chart: {
                                type: 'line',
                                height: 200,
                                animations: {
                                    enabled: true
                                }
                            },
                            xaxis: {
                                type: 'category',
                                categories: monthlySale.map(s => s.date)
                            }
                        };

                        let topProducts = data.topProducts;
                        topProducts.forEach(product => {
                            this.pieseries.push(product.total_quantity);
                            this.piechartOptions.labels.push(product.name);
                        });

                        this.showChart = false;

                        this.$nextTick(() => {
                            this.showChart = true;
                        });
                    })
            }
        }
    });
</script>
@endpush