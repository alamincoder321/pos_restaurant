@extends('master')

@section('title', 'Sale Entry')
@section('breadcrumb', 'Sale Entry')
@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<style scoped>
    .purTable thead tr th {
        background: #64747ca3;
        padding: 7px 5px !important;
        color: #fff;
        text-align: center !important;
    }

    table tr td,
    table tr th {
        vertical-align: middle !important;
    }

    tr td {
        padding: 6px !important;
    }

    .CustomerCard {
        height: 210px;
    }

    .btnCart,
    .btnCart:hover,
    .btnCart:focus {
        background: #228dc1;
        color: #fff;
    }

    .bankBtn:focus {
        background: #db9696 !important;
    }

    .sale-type-box.active {
        background: #228dc1 !important;
        color: #fff !important;
        border-color: #228dc1 !important;
    }

    .sale_type_label {
        cursor: pointer;
        font-size: 13px;
        width: 80px;
        text-align: center;
        border-radius: 6px;
        border: 1px solid #228dc1;
        background: #fff;
        color: #228dc1;
        font-weight: 500;
        transition: all .2s;
    }
</style>
@endpush
@section('content')
<div class="row" id="sale">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body p-2">
                <div class="row d-flex align-items-center">
                    <label class="form-label col-4 col-md-1 mb-md-0" for="name">Invoice:</label>
                    <div class="col-8 col-md-2 mb-1 mb-md-0">
                        <input type="text" readonly class="form-control" autocomplete="off" id="invoice" name="invoice" v-model="sale.invoice" />
                    </div>
                    <label class="form-label col-4 col-md-1 mb-md-0" for="name">Employee:</label>
                    <div class="col-8 col-md-2 mb-1 mb-md-0">
                        <v-select :options="employees" v-model="selectedEmployee" label="display_name"></v-select>
                    </div>
                    <label class="form-label col-4 col-md-1 mb-md-0" for="name">AddBy:</label>
                    <div class="col-8 col-md-2 mb-1 mb-md-0">
                        <input type="text" readonly class="form-control" autocomplete="off" id="name" value="{{auth()->user()->name}}" />
                    </div>
                    <label class="form-label col-4 col-md-1 mb-md-0" for="name">Date:</label>
                    <div class="col-8 col-md-2 mb-1 mb-md-0">
                        <input type="date" class="form-control" autocomplete="off" id="date" name="date" v-model="sale.date" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12 mt-2">
        <div class="card mb-0">
            <div class="card-body p-2">
                <div class="input-group mb-1">
                    <input v-if="!isBarcode" type="text" class="form-control" style="padding: 10px;" placeholder="Search Product" v-model="searchProductText"
                        @input="onSearchProduct($event)"
                        @keydown.down.prevent="moveHighlight(1)"
                        @keydown.up.prevent="moveHighlight(-1)"
                        @keydown.enter.prevent="addToCart(products[highlightedIndex])"
                        autocomplete="off"
                        ref="searchInput" />
                    <input v-if="isBarcode" @keydown.enter.prevent="getProduct" type="text" class="form-control" style="padding: 10px;" placeholder="Scan Barcode" v-model="searchProductText">
                    <button @click="isBarcode = !isBarcode" class="btn btn-primary" type="button" style="font-size: 22px;"><i :class="isBarcode ? 'fa fa-product-hunt' : 'bi bi-upc'"></i></button>
                </div>
                <div class="position-relative" @click.stop>
                    <ul
                        v-if="searchProductText && !isBarcode"
                        class="list-group position-absolute w-100 shadow"
                        style="z-index: 1000; max-height: 250px; overflow-y: auto;"
                        tabindex="0"
                        ref="productList">
                        <li
                            v-for="(product, idx) in products"
                            :key="product.id"
                            :class="['list-group-item', 'd-flex', 'justify-content-between', 'align-items-center', 'list-group-item-action', {active: idx === highlightedIndex}]"
                            style="cursor: pointer;"
                            @click="addToCart(product)"
                            @mouseenter="highlightedIndex = idx">
                            <div>
                                <strong v-text="product.name"></strong>
                                <span class="text-muted small" v-if="product.code"> - <span v-text="product.code"></span></span>
                                <span class="badge bg-secondary ms-2" v-if="product.stock == undefined">
                                    Stock: <span v-text="0"></span>
                                </span>
                            </div>
                            <span class="badge bg-primary" v-if="product.sale_rate">৳ <span v-text="product.sale_rate"></span></span>
                        </li>
                        <li v-if="products.length == 0" class="list-group-item text-center text-muted">
                            No products found
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12 mt-1" style="overflow-x: auto;">
        <table class="table table-bordered table-hover purTable">
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Rate</th>
                    <th>Total</th>
                    <th style="width: 3%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(cart, index) in carts" :key="index" v-if="carts.length > 0" :class="carts.length > 0 ? '' : 'd-none'">
                    <td class="text-center" v-text="index + 1"></td>
                    <td v-text="`${cart.name} - ${cart.code}`"></td>
                    <td class="text-center" v-text="cart.category_name"></td>
                    <td class="text-center">
                        <div class="input-group input-group-sm" style="width: 150px; margin: 0 auto;">
                            <button style="padding: 1px 5px;" class="btn btn-outline-secondary" type="button" @click="cart.quantity = Math.max(1, +cart.quantity - 1); quantityRateTotal(cart)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" min="1" step="any"
                                class="form-control text-center"
                                style="width:70px;padding: 1px 6px; outline: none; border-radius: 0;border-color: #000;"
                                v-model="cart.quantity"
                                @input="quantityRateTotal(cart)" />
                            <button style="padding: 1px 5px;" class="btn btn-outline-secondary" type="button" @click="cart.quantity = +cart.quantity + 1; quantityRateTotal(cart)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center" v-text="cart.unit_name"></td>
                    <td class="text-center">
                        <div class="input-group input-group-sm" style="width: 210px; margin: 0 auto;">
                            <button style="padding: 1px 5px;" class="btn btn-outline-secondary" type="button" @click="cart.sale_rate = Math.max(0, +cart.sale_rate - 1); quantityRateTotal(cart)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" min="0" step="any"
                                class="form-control text-center"
                                style="width:120px;padding: 1px 6px; outline: none; border-radius: 0;border-color: #000;"
                                v-model="cart.sale_rate"
                                @input="quantityRateTotal(cart)" />
                            <button style="padding: 1px 5px;" class="btn btn-outline-secondary" type="button" @click="cart.sale_rate = +cart.sale_rate + 1; quantityRateTotal(cart)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center" v-text="cart.total"></td>
                    <td class="text-center">
                        <i @click="removeCart(index)" class="bi bi-trash3 text-danger" style="cursor: pointer;"></i>
                    </td>
                </tr>
                <tr v-if="carts.length == 0" :class="carts.length == 0 ? '' : 'd-none'">
                    <td colspan="8" class="text-center">Not Found Data</td>
                </tr>
                <tr>
                    <td colspan="8" style="padding: 8px !important;"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-12 col-md-6 mt-2">
        <div class="card mb-0">
            <div class="card-body p-2">
                <div class="row">
                    <label class="col-md-3" for="">Customer</label>
                    <div class="col-md-9">
                        <div class="input-group mb-1">
                            <v-select :options="customers" style="width: 85%;" v-model="selectedCustomer" label="display_name" @input="onChangeCustomer"></v-select>
                            <button onclick="window.open('/customer', '_blank')" class="btn btn-primary btn-sm" type="button" style="font-size: 12px; width:15%"><i class="bi bi-person-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3" for="">Name</label>
                    <div class="col-md-9">
                        <div class="input-group mb-1">
                            <input type="text" :disabled="selectedCustomer.type == 'retail'" class="form-control" v-model="selectedCustomer.name" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3" for="">Mobile</label>
                    <div class="col-md-9">
                        <div class="input-group mb-1">
                            <input type="text" :disabled="selectedCustomer.type == 'retail'" class="form-control" v-model="selectedCustomer.phone" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3" for="">Address</label>
                    <div class="col-md-9">
                        <div class="input-group mb-1">
                            <input type="text" :disabled="selectedCustomer.type == 'retail'" class="form-control" v-model="selectedCustomer.address">
                        </div>
                    </div>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Note:</span>
                    <textarea rows="5" name="note" id="note" class="form-control" v-model="sale.note"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 mt-2">
        <form @submit.prevent="saveData($event)">
            <div class="card mb-0">
                <div class="card-body p-2">
                    <table class="table table-hover">
                        <tr>
                            <td>SubTotal</td>
                            <td colspan="3">
                                <input type="number" min="0" step="any" class="form-control" id="subtotal" name="subtotal" v-model="sale.subtotal" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td>
                                <input type="number" @input="calculateTotal" min="0" step="any" class="form-control" id="discountPercent" name="discountPercent" v-model="discountPercent" />
                            </td>
                            <td style="width: 13px;">%</td>
                            <td>
                                <input type="number" @input="calculateTotal" min="0" step="any" class="form-control" id="discount" name="discount" v-model="sale.discount" />
                            </td>
                        </tr>
                        <tr>
                            <td>Vat</td>
                            <td>
                                <input type="number" @input="calculateTotal" min="0" step="any" class="form-control" id="vatPercent" name="vatPercent" v-model="vatPercent" />
                            </td>
                            <td style="width: 13px;">%</td>
                            <td>
                                <input type="number" @input="calculateTotal" min="0" step="any" class="form-control" id="vat" name="vat" v-model="sale.vat" />
                            </td>
                        </tr>
                        <tr>
                            <td>Transport Cost</td>
                            <td colspan="3">
                                <input type="number" @input="calculateTotal" min="0" step="any" class="form-control" id="transport_cost" name="transport_cost" v-model="sale.transport_cost" />
                            </td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td colspan="3">
                                <input type="number" min="0" step="any" class="form-control" id="total" name="total" v-model="sale.total" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td>CashPaid</td>
                            <td colspan="3">
                                <input type="number" @input="calculateTotal" min="0" step="any" class="form-control" id="cashPaid" name="cashPaid" v-model="sale.cashPaid" />
                            </td>
                        </tr>
                        <tr>
                            <td><label class="form-label mb-0 btn btn-secondary w-100 px-0" @click="showModal">Multi-Payment</label></td>
                            <td colspan="3">
                                <input type="number" v-model="sale.bankPaid" id="paid" min="0" step="any" class="form-control" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td>Change Amount</td>
                            <td colspan="3">
                                <input type="number" v-model="sale.returnAmount" min="0" step="any" class="form-control" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td>Due</td>
                            <td>
                                <input type="number" v-model="sale.due" min="0" step="any" class="form-control" readonly />
                            </td>
                            <td colspan="2">
                                <input type="number" v-model="sale.previous_due" min="0" step="any" class="form-control text-danger" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <button type="submit" :disabled="onProgress" class="btn btn-success w-100" v-text="sale.id != '' ? 'Update' : 'Save'"></button>
                            </td>
                            <td colspan="2">
                                <button type="button" class="btn btn-danger w-100">Reset</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <!-- bank account entry -->
    <div class="modal showModal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header justify-content-between text-white" style="background: #236974;">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Multi-Payment Add To Cart & List</h1>
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>
                <div class="modal-body pb-3">
                    <form @submit.prevent="addToBankCart">
                        <div class="input-group">
                            <v-select :options="banks" id="banks" v-model="selectedBank" label="display_name" @input="onChangeBank" style="width: 350px;"></v-select>
                            <input type="text" class="form-control" id="last_digit" v-model="selectedBank.last_digit" @input="goToAmount" placeholder="Last Digit" />
                            <input type="number" step="any" min="0" class="form-control" id="bankAmount" v-model="selectedBank.amount" placeholder="Amount" />
                            <input type="submit" class="bankBtn" value="Add" style="border: none; background: #a9a9a9; color: #fff;" />
                        </div>
                    </form>
                    <table class="table table-bordered table-hover text-center mt-3">
                        <thead>
                            <tr>
                                <th colspan="6" style="background: gainsboro;">Bank Cart List</th>
                            </tr>
                            <tr>
                                <th>Sl</th>
                                <th>Bank Name</th>
                                <th>Account Number</th>
                                <th>Last Digit</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="bankCart.length > 0" :class="bankCart.length > 0 ? '' : 'd-none'">
                            <tr v-for="(cart, index) in bankCart" :key="index">
                                <td v-text="index + 1"></td>
                                <td v-text="cart.bank_name"></td>
                                <td v-text="cart.number"></td>
                                <td v-text="cart.last_digit"></td>
                                <td v-text="cart.amount"></td>
                                <td>
                                    <span @click="removeBankCart(index)" style="cursor: pointer;">X</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer text-white" style="background: #f1e5ac;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#sale",
        data() {
            return {
                sale: {
                    id: "{{$id}}",
                    invoice: "{{$invoice}}",
                    date: moment().format('YYYY-MM-DD'),
                    sale_type: 'retail',
                    employee_id: "",
                    subtotal: 0,
                    discount: 0,
                    vat: 0,
                    transport_cost: 0,
                    total: 0,
                    cashPaid: 0,
                    bankPaid: 0,
                    paid: 0,
                    returnAmount: 0,
                    due: 0,
                    previous_due: 0,
                    note: ''
                },

                discountPercent: 0,
                vatPercent: 0,
                customers: [],
                selectedCustomer: {
                    id: '',
                    display_name: 'Walk In Customer',
                    name: 'Walk In Customer',
                    phone: '',
                    address: '',
                    type: 'general'
                },
                products: [],
                searchProductText: "",
                highlightedIndex: 0,
                employees: [],
                selectedEmployee: null,
                banks: [],
                selectedBank: {
                    id: '',
                    display_name: 'select product',
                    last_digit: '',
                    amount: ''
                },
                carts: [],
                bankCart: [],
                stock: 0,
                isBarcode: false,
                onProgress: false,
            }
        },

        async created() {
            this.getEmployee();
            this.getBank();
            this.getCustomer();
            if (this.sale.id != '') {
                await this.getSale();
            }
        },

        methods: {
            async moveHighlight(direction) {
                if (!this.products.length) return;
                this.highlightedIndex += direction;
                if (this.highlightedIndex < 0) {
                    this.highlightedIndex = this.products.length - 1;
                } else if (this.highlightedIndex >= this.products.length) {
                    this.highlightedIndex = 0;
                }
                this.$nextTick(() => {
                    const list = this.$refs.productList;
                    if (list && list.children[this.highlightedIndex]) {
                        list.children[this.highlightedIndex].scrollIntoView({
                            block: 'nearest'
                        });
                    }
                });
            },
            getBank() {
                axios.get('/get-bank')
                    .then(res => {
                        this.banks = res.data.map(item => {
                            item.last_digit = "";
                            item.amount = "";
                            return item;
                        });
                    })
            },
            getEmployee() {
                axios.get('/get-employee')
                    .then(res => {
                        this.employees = res.data;
                    })
            },
            getCustomer() {
                axios.post('/get-customer', {
                        forSearch: 'yes',
                        customer_type: this.sale.sale_type
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
                        }, {
                            id: '',
                            display_name: 'New Customer',
                            name: '',
                            phone: '',
                            address: '',
                            type: 'new'
                        })
                    })
            },

            async onSearchCustomer(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get-customer", {
                            search: val,
                            customer_type: this.sale.sale_type
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
                if (this.selectedCustomer == null) {
                    this.selectedCustomer = {
                        id: '',
                        display_name: 'Walk In Customer',
                        name: 'Walk In Customer',
                        phone: '',
                        address: '',
                        type: 'general'
                    }
                    return;
                }

            },

            async getProduct() {
                await axios.post('/get-product', {
                        search: this.searchProductText,
                    })
                    .then(async res => {
                        this.products = res.data.map(item => {
                            item.sale_rate = this.sale.sale_type == 'retail' ? item.sale_rate : item.wholesale_rate;
                            item.id = item.id;
                            item.code = item.code ? String(item.code) : '';
                            return item;
                        });
                        await this.addToCart(this.products[0]);
                    })
            },

            async onSearchProduct(event) {
                if (event.target.value != '') {
                    await axios.post("/get-product", {
                            search: event.target.value,
                            is_service: 'false',
                            forSearch: 'yes'
                        })
                        .then(res => {
                            this.products = res.data.map(item => {
                                item.sale_rate = this.sale.sale_type == 'retail' ? item.sale_rate : item.wholesale_rate;
                                item.id = item.id;
                                item.code = item.code ? String(item.code) : '';
                                return item;
                            });
                        })
                } else {
                    this.products = [];
                }
            },

            async addToCart(product) {
                const exists = this.carts.find(p => p.id === product.id);
                if (!exists) {
                    let cart = {
                        id: product.id,
                        code: product.code ? String(product.code) : '',
                        category_name: product.category?.name,
                        name: product.name,
                        unit_name: product.unit?.name,
                        purchase_rate: product.purchase_rate,
                        sale_rate: product.sale_rate,
                        quantity: 1,
                        total: parseFloat(product.sale_rate).toFixed(2),
                    };
                    this.carts.push(cart);
                } else {
                    exists.quantity = Number(exists.quantity) + 1;
                    exists.total = (exists.quantity * exists.sale_rate).toFixed(2);
                }

                this.searchProductText = '';
                this.products = [];
                await this.calculateTotal();
            },

            async quantityRateTotal(cart) {
                let stock = await axios.post('/get-currentStock', {
                    productId: cart.id
                }).then(res => {
                    return res.data.length > 0 ? res.data[0].stock : 0;
                });

                this.carts = this.carts.map(item => {
                    if (item.id === cart.id) {
                        if (item.quantity == '') {
                            item.quantity = 1;
                        }
                        if (parseFloat(item.quantity) > parseFloat(stock)) {
                            toastr.error('Stock is unavailable');
                            item.quantity = stock;
                        }
                        item.total = parseFloat(item.sale_rate * item.quantity).toFixed(2);
                    }
                    return item;
                });
                await this.calculateTotal();
            },

            async removeCart(sl) {
                this.carts.splice(sl, 1);
                await this.calculateTotal();
            },

            async calculateTotal() {
                this.sale.subtotal = this.carts.reduce((pr, cu) => {
                    return pr + parseFloat(cu.total)
                }, 0).toFixed(2);
                if (event.target.id == 'discount') {
                    this.discountPercent = (this.sale.discount * 100) / this.sale.subtotal;
                }
                if (event.target.id == 'discountPercent') {
                    this.sale.discount = parseFloat((this.discountPercent * this.sale.subtotal) / 100).toFixed(2);
                }
                this.sale.total = parseFloat(this.sale.subtotal - this.sale.discount).toFixed(2);
                if (event.target.id == 'vat') {
                    this.vatPercent = (this.sale.vat * 100) / this.sale.total;
                }
                if (event.target.id == 'vatPercent') {
                    this.sale.vat = parseFloat((this.vatPercent * this.sale.total) / 100).toFixed(2);
                }
                this.sale.total = parseFloat(+this.sale.total + +this.sale.vat + +this.sale.transport_cost).toFixed(2);
                if (event.target.id == 'cashPaid' || this.bankCart.length > 0) {
                    this.sale.paid = parseFloat(parseFloat(this.sale.cashPaid) + parseFloat(this.sale.bankPaid)).toFixed(2);
                    if (parseFloat(this.sale.paid) > parseFloat(this.sale.total)) {
                        this.sale.returnAmount = parseFloat(this.sale.paid - this.sale.total).toFixed(2);
                        this.sale.due = 0;

                    } else {
                        this.sale.returnAmount = 0;
                        this.sale.due = parseFloat(this.sale.total - this.sale.paid).toFixed(2);
                    }
                } else {
                    this.sale.cashPaid = this.sale.total;
                    this.sale.bankPaid = 0;
                    this.sale.paid = this.sale.total;
                    this.sale.due = 0;
                    this.sale.returnAmount = 0;
                }
            },

            showModal() {
                $('.showModal').modal('show');
            },

            onChangeBank() {
                if (this.selectedBank == null) {
                    this.selectedBank = {
                        id: '',
                        display_name: 'select product',
                        last_digit: '',
                        amount: ''
                    }
                    return;
                }
                if (this.selectedBank.id != '') {
                    $('#staticBackdrop').find('#last_digit').select();
                }
            },

            goToAmount() {
                if (this.selectedBank.last_digit.length > 3) {
                    $('#staticBackdrop').find('#bankAmount').select();
                }
            },

            async addToBankCart() {
                if (this.selectedBank == null) {
                    toastr.error('Please select a bank')
                    return;
                }
                if (this.selectedBank.id == '') {
                    toastr.error('Please select a bank')
                    $('#staticBackdrop').find("#banks [type='search']").focus();
                    return;
                }
                let cart = this.bankCart.find(item => item.id == this.selectedBank.id);
                if (cart) {
                    cart.amount = parseFloat(cart.amount) + parseFloat(this.selectedBank.amount);
                } else {
                    this.bankCart.push({
                        id: this.selectedBank.id,
                        bank_name: this.selectedBank.bank_name,
                        number: this.selectedBank.number,
                        last_digit: this.selectedBank.last_digit,
                        amount: this.selectedBank.amount,
                    })
                }
                this.sale.bankPaid = this.bankCart.reduce((pr, cu) => {
                    return pr + parseFloat(cu.amount)
                }, 0).toFixed(2);
                await this.calculateTotal();
                this.clearBankCart();
            },

            removeBankCart(sl) {
                this.bankCart.splice(sl, 1);
                this.sale.bankPaid = this.bankCart.reduce((pr, cu) => {
                    return pr + parseFloat(cu.amount)
                }, 0).toFixed(2);
                this.calculateTotal();
            },

            clearBankCart() {
                this.selectedBank = {
                    id: '',
                    display_name: 'select product',
                    last_digit: '',
                    amount: ''
                };
            },

            saveData(event) {
                this.sale.employee_id = this.selectedEmployee ? this.selectedEmployee.id : "";
                let formdata = {
                    sale: this.sale,
                    customer: this.selectedCustomer,
                    carts: this.carts,
                    bankCart: this.bankCart,
                }
                let url = this.sale.id != '' ? '/update-sale' : '/sale'
                this.onProgress = true;
                axios.post(url, formdata)
                    .then(async res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        history.pushState(null, '', '/sale');
                        this.sale.invoice = res.data.invoice;
                        if (confirm('Do you want to go to the invoice page?')) {
                            window.open(`/sale-invoice/${res.data.saleId}`, '_blank');
                        }
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
            clearData() {
                this.sale = {
                    id: "",
                    invoice: "{{$invoice}}",
                    date: moment().format('YYYY-MM-DD'),
                    sale_type: 'retail',
                    employee_id: "",
                    subtotal: 0,
                    discount: 0,
                    vat: 0,
                    transport_cost: 0,
                    total: 0,
                    paid: 0,
                    due: 0,
                    previous_due: 0,
                };
                this.onProgress = false;
                this.discountPercent = 0;
                this.vatPercent = 0;
                this.selectedCustomer = {
                    id: '',
                    display_name: 'Walk In Customer',
                    name: 'Walk In Customer',
                    phone: '',
                    address: '',
                    type: 'general'
                };
                this.selectedEmployee = null;
                this.carts = [];
                this.getCustomer();
            },

            async getSale() {
                await axios.post('/get-sale', {
                    saleId: this.sale.id
                }).then(res => {
                    let sale = res.data[0];
                    let saleKeys = Object.keys(this.sale);
                    saleKeys.forEach(key => {
                        this.sale[key] = sale[key];
                    })

                    sale.details.map(item => {
                        let detail = {
                            id: item.menu_id,
                            code: item.code,
                            category_name: item.category_name,
                            name: item.name,
                            unit_name: item.unit_name,
                            purchase_rate: item.purchase_rate,
                            sale_rate: item.sale_rate,
                            quantity: item.quantity,
                            total: item.total
                        };
                        this.carts.push(detail);
                    })
                    sale.bank_details.map(item => {
                        let detail = {
                            id: item.bank_id,
                            bank_name: item.bank_name,
                            number: item.number,
                            last_digit: item.last_digit,
                            amount: item.amount
                        };
                        this.bankCart.push(detail);
                    })

                    this.selectedCustomer = {
                        id: sale.customer_id ?? '',
                        name: sale.customer_name,
                        phone: sale.customer_phone,
                        address: sale.customer_address,
                        display_name: sale.customer_type == 'general' ? 'Walk In Customer' : `${sale.customer_name} - ${sale.customer_phone} - ${sale.customer_address}`,
                        type: sale.customer_type
                    }

                    setTimeout(() => {
                        this.selectedEmployee = this.employees.find(item => item.id == sale.employee_id);
                    }, 1000);
                })
            }
        },
    })
</script>
@endpush