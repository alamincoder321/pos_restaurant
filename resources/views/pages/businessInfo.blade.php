@extends('master')

@section('title', 'Business Info')
@section('breadcrumb', 'Business Info')

@push('style')
<style scoped>
    .card {
        border: 1px solid #959595;
        border-left: 5px solid #b79f2c;
    }

    .overView .card {
        transition: all 0.3s ease-in-out;
        cursor: pointer;
    }

    .overView .card:hover {
        transition: all 0.3s ease-in-out;
        box-shadow: 6px 3px 0px 1px #818181;
    }

    @media (min-width: 300px) and (max-width: 620px) {

        .overView strong,
        p {
            font-size: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="row" id="businessInfo">
    <div class="col-md-12 my-3">
        <div style="display: flex; align-items: center; text-align: center; margin: 0;">
            <div style="flex: 1; border-bottom: 1px solid #000;"></div>
            <div style="padding: 0 15px; font-size: 18px; font-weight: 700;">Welcome To Business Info</div>
            <div style="flex: 1; border-bottom: 1px solid #000;"></div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 50px;background: #dbd6d6;border-radius: 50%;">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Cash Balance</strong>
                            <p class="m-0">৳ {{number_format($cashBalance->cashbalance, 2)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 50px;background: #dbd6d6;border-radius: 50%;">
                            <i class="bi bi-bank2"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Bank Balance</strong>
                            <p class="m-0">৳ {{number_format($bankBalance, 2)}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 50px;background: #dbd6d6;border-radius: 50%;">
                            <img src="/taka.png" style="width: 50px;margin-top: -15px;">
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Total Balance</strong>
                            <p class="m-0">৳ {{number_format($cashBalance->cashbalance + $bankBalance, 2)}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row overView">
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-cart-plus"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Today Order</strong>
                            <p class="m-0">@{{todaySale | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-cart-plus"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Monthly Order</strong>
                            <p class="m-0">@{{monthlySale | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-cart-plus"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Yearly Order</strong>
                            <p class="m-0">@{{yearlySale | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-cart-plus"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Total Order</strong>
                            <p class="m-0">@{{totalSale | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Today Collection</strong>
                            <p class="m-0">@{{collection | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Customer Due</strong>
                            <p class="m-0">@{{customerDue | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-clipboard-minus"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Expense</strong>
                            <p class="m-0">@{{expense | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-duffle"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Income</strong>
                            <p class="m-0">@{{income | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-cash"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">Investment</strong>
                            <p class="m-0">@{{investBalance | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-3 mb-3">
                    <div class="card-body p-0 d-flex gap-3 align-items-center">
                        <div class="w-25 text-center" style="font-size: 30px;background: #ffeec7;border-radius: 50%;">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="w-75">
                            <strong class="m-0">ProfitLoss</strong>
                            <p class="m-0">@{{monthlyProfitLoss | formatCurrency}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="row m-0">
            <div class="col-12 col-md-7 px-0" style="border: 1px solid #959595; padding: 10px;">
                <h6 style="margin: 0;border-bottom: 2px solid #000;text-align:center;padding-bottom: 6px;">Monthly Sales Overview</h6>
                <apexchart v-if="showChart" type="area" height="200" :options="chartOptions" :series="series"></apexchart>
            </div>
            <div class="col-12 col-md-5 px-0" style="border: 1px solid #959595; padding: 10px;">
                <h6 style="margin: 0;border-bottom: 2px solid #000;text-align:center;padding-bottom: 6px;">Top Sale Products</h6>
                <apexchart v-if="showChart" type="pie" height="200" :options="piechartOptions" :series="pieseries"></apexchart>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-apexcharts"></script>
<script>
    Vue.component('apexchart', VueApexCharts);
    new Vue({
        el: "#businessInfo",
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

                todaySale: 0,
                monthlySale: 0,
                yearlySale: 0,
                totalSale: 0,
                collection: 0,
                customerDue: 0,
                supplierDue: 0,
                stockBalance: 0,
                expense: 0,
                income: 0,
                monthlyProfitLoss: 0,
                yearlyProfitLoss: 0,
                investBalance: 0,
            }
        },
        filters: {
            formatCurrency(value) {
                if (typeof value !== "number") {
                    value = Number(value);
                }
                if (isNaN(value)) return "৳ 0.00";
                return "৳ " + value.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        },
        created() {
            this.getBusinessInfo();
            this.getTopBusinessInfo();
        },
        methods: {
            getBusinessInfo() {
                axios.post("/get-business-info", {})
                    .then(response => {
                        const data = response.data;
                        this.todaySale = data.todaySale;
                        this.monthlySale = data.monthlySale;
                        this.yearlySale = data.yearlySale;
                        this.totalSale = data.totalSale;
                        this.collection = data.collection;
                        this.customerDue = data.customerDue;
                        this.supplierDue = data.supplierDue;
                        this.stockBalance = data.stockBalance;
                        this.expense = data.expense;
                        this.income = data.income;
                        this.monthlyProfitLoss = data.monthlyProfitLoss;
                        this.yearlyProfitLoss = data.yearlyProfitLoss;
                        this.investBalance = data.investBalance;
                    })
            },
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
        },
    });
</script>
@endpush