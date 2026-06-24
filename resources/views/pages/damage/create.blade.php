@extends('master')

@section('title', 'Damage Entry')
@section('breadcrumb', 'Damage Entry')
@push('style')
<style scoped>
    .purTable thead tr th {
        background: #96a0a5;
        padding: 3px 4px !important;
        color: #fff;
        text-align: center !important;
    }

    table tr td,
    table tr th {
        vertical-align: middle !important;
    }

    .btnCart,
    .btnCart:hover,
    .btnCart:focus {
        background: #228dc1;
        color: #fff;
    }
</style>
@endpush
@section('content')
<div class="row" id="damage">
    <div class="col-12 col-md-9 mt-2">
        <div class="row">
            <div class="col-12 col-md-6 pe-md-1 mb-1">
                <div class="card mb-0 supplierCard">
                    <div class="card-header py-2">
                        <h3 class="m-0 card-title p-0">Supplier Info</h3>
                    </div>
                    <div class="card-body p-3 py-2">
                        <div class="form-group row mb-1">
                            <label for="" class="col-4 col-md-4 form-label">Supplier:</label>
                            <div class="col-8 col-md-8">
                                <v-select :options="suppliers" v-model="selectedSupplier" label="display_name" @input="onChangeSupplier" @search="onSearchSupplier"></v-select>
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <label for="" class="col-4 col-md-4 form-label">Name:</label>
                            <div class="col-8 col-md-8">
                                <input type="text" :disabled="selectedSupplier.type == 'regular'" class="form-control" v-model="selectedSupplier.name" />
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <label for="" class="col-4 col-md-4 form-label">Phone:</label>
                            <div class="col-8 col-md-8">
                                <input type="text" :disabled="selectedSupplier.type == 'regular'" class="form-control" v-model="selectedSupplier.phone" />
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <label for="" class="col-4 col-md-4 form-label">Address:</label>
                            <div class="col-8 col-md-8">
                                <textarea type="text" :disabled="selectedSupplier.type == 'regular'" class="form-control" v-model="selectedSupplier.address"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 ps-md-1">
                <div class="card mb-0">
                    <div class="card-header py-2">
                        <h3 class="m-0 card-title p-0">Product Info</h3>
                    </div>
                    <div class="card-body p-3 py-2 pb-3">
                        <form @submit.prevent="addToCart">
                            <div class="form-group row mb-1">
                                <label for="" class="col-4 col-md-3 form-label">Product:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="products" id="product" v-model="selectedProduct" label="display_name" @input="onChangeProduct" @search="onSearchProduct"></v-select>
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="" class="col-4 col-md-3 form-label">Rate:</label>
                                <div class="col-8 col-md-4">
                                    <input type="number" min="0" step="any" class="form-control" v-model="selectedProduct.purchase_rate" @input="productTotal" />
                                </div>
                                <label for="" class="col-4 col-md-2 pe-md-0 form-label">Qty:</label>
                                <div class="col-8 col-md-3 ps-md-0">
                                    <input type="number" min="0" step="any" ref="quantity" class="form-control" v-model="selectedProduct.quantity" @input="productTotal" />
                                </div>
                            </div>
                            <div class="form-group row mb-1">
                                <label for="" class="col-4 col-md-3 form-label">Total:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" min="0" step="any" class="form-control" v-model="selectedProduct.total" readonly />
                                </div>
                            </div>
                            <div class="form-group row" style="display: flex; align-items: center;">
                                <div class="col-12 col-md-7" style="font-size: 13px;">
                                    <span>Stock:</span> <span class="text-success" v-text="stock"></span> <span v-text="selectedProduct.unit?.name"></span>
                                    <span class="text-danger" v-if="stock <= 0"> (Out of Stock)</span>
                                </div>
                                <div class="col-12 col-md-5 text-end">
                                    <button type="submit" class="btn btn-sm btnCart w-100">AddToCart</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 mt-1" style="overflow-x: auto;">
                <table class="table table-hover purTable">
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
                    <tbody v-if="carts.length > 0" :class="carts.length > 0 ? '' : 'd-none'">
                        <tr v-for="(cart, index) in carts" :key="index">
                            <td class="text-center" v-text="index + 1"></td>
                            <td v-text="`${cart.name} - ${cart.code}`"></td>
                            <td class="text-center" v-text="cart.category_name"></td>
                            <td class="text-center">
                                <input type="number" :disabled="damage.id != ''" min="0" step="any" style="width: 100px;padding:0; text-align: center; outline: none; border: 1px solid #c3c3c3; border-radius: 5px;" v-model="cart.quantity" @input="quantityRateTotal(cart)" />
                            </td>
                            <td class="text-center" v-text="cart.unit_name"></td>
                            <td class="text-center" v-text="cart.purchase_rate"></td>
                            <td class="text-center" v-text="cart.total"></td>
                            <td class="text-center">
                                <i @click="removeCart(index)" class="bi bi-trash3 text-danger" style="cursor: pointer;"></i>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" style="padding: 5px !important;"></td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 6px !important;" colspan="2"><strong>Note:</strong></td>
                            <td style="padding: 5px 6px !important;" colspan="6">
                                <textarea class="form-control" v-model="damage.note" placeholder="Enter note here"></textarea>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-if="carts.length == 0" :class="carts.length == 0 ? '' : 'd-none'">
                        <td colspan="8" class="text-center">Cart is Empty</td>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3 mt-2 ps-md-1">
        <form @submit.prevent="saveData($event)">
            <div class="card mb-0">
                <div class="card-header py-2">
                    <h3 class="m-0 card-title p-0">Payment Info</h3>
                </div>
                <div class="card-body p-3 pt-md-2 pb-md-1">
                    <div class="form-group row mb-1">
                        <label for="invoice" class="col-4 col-md-12 form-label mb-0">Invoice</label>
                        <div class="col-8 col-md-12">
                            <input type="text" readonly class="form-control" autocomplete="off" id="invoice" name="invoice" v-model="damage.invoice" />
                        </div>
                    </div>
                    <div class="form-group row mb-1">
                        <label for="subtotal" class="col-4 col-md-12 form-label mb-0">Date</label>
                        <div class="col-8 col-md-12">
                            <input type="date" class="form-control" autocomplete="off" id="date" name="date" v-model="damage.date" />
                        </div>
                    </div>
                    <div class="form-group row mb-1">
                        <label for="subtotal" class="col-4 col-md-12 form-label mb-0">Total</label>
                        <div class="col-8 col-md-12">
                            <input type="number" v-model="damage.total" min="0" step="any" class="form-control" readonly />
                        </div>
                    </div>
                </div>
                <div class="card-footer py-2">
                    <div class="form-group row mb-2">
                        <div class="col-6 col-md-6">
                            <button type="submit" :disabled="onProgress" class="btn btn-success w-100" v-text="damage.id != '' ? 'Update' : 'Save'"></button>
                        </div>
                        <div class="col-6 col-md-6">
                            <button type="button" class="btn btn-danger w-100">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#damage",
        data() {
            return {
                damage: {
                    id: "{{$id}}",
                    invoice: "{{$invoice}}",
                    date: moment().format('YYYY-MM-DD'),
                    total: 0,
                    note: ''
                },
                suppliers: [],
                selectedSupplier: {
                    id: '',
                    display_name: 'Walk In Supplier',
                    name: 'Walk In Supplier',
                    phone: '',
                    address: '',
                    type: 'general'
                },
                products: [],
                selectedProduct: {
                    id: '',
                    display_name: ''
                },
                carts: [],
                stock: 0,
                onProgress: false,
            }
        },

        async created() {
            this.getSupplier();
            this.getProduct();
            if (this.damage.id != '') {
                await this.getDamage();
            }
        },

        methods: {
            getSupplier() {
                axios.post('/get-supplier', {
                        forSearch: 'yes'
                    })
                    .then(res => {
                        this.suppliers = res.data;
                        this.suppliers.unshift({
                            id: '',
                            display_name: 'Walk In Supplier',
                            name: 'Walk In Supplier',
                            phone: '',
                            address: '',
                            type: 'general'
                        }, {
                            id: '',
                            display_name: 'New Supplier',
                            name: '',
                            phone: '',
                            address: '',
                            type: 'new'
                        })
                    })
            },

            async onSearchSupplier(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get-supplier", {
                            search: val,
                        })
                        .then(res => {
                            this.suppliers = res.data;
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getSupplier();
                }
            },

            onChangeSupplier() {
                if (this.selectedSupplier == null) {
                    this.selectedSupplier = {
                        id: '',
                        display_name: 'Walk In Supplier',
                        name: 'Walk In Supplier',
                        phone: '',
                        address: '',
                        type: 'general'
                    }
                    return;
                }

            },

            getProduct() {
                axios.post('/get-product', {
                        forSearch: 'yes'
                    })
                    .then(res => {
                        this.products = res.data;
                    })
            },
            async onSearchProduct(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get-product", {
                            search: val,
                            is_service: 'false'
                        })
                        .then(res => {
                            this.products = res.data;
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getProduct();
                }
            },

            async onChangeProduct() {
                if (this.selectedProduct == null) {
                    this.selectedProduct = {
                        id: '',
                        unit: {},
                        display_name: 'select product'
                    }
                    return;
                }
                if (this.selectedProduct.id != '') {
                    await axios.post('/get-currentStock', {
                        productId: this.selectedProduct.id
                    }).then(res => {
                        this.stock = res.data.length > 0 ? res.data[0].stock : 0;
                    })
                    this.$refs.quantity.focus();
                }
            },

            productTotal() {
                this.selectedProduct.total = parseFloat(this.selectedProduct.purchase_rate * this.selectedProduct.quantity).toFixed(2);
            },

            addToCart() {
                if (this.selectedProduct.id == '') {
                    toastr.error('Please select a product')
                    return;
                }
                let cart = this.carts.find(item => item.id == this.selectedProduct.id);

                if (cart != undefined) {
                    let newQuantity = parseFloat(cart.quantity) + parseFloat(this.selectedProduct.quantity)
                    if (parseFloat(newQuantity) > parseFloat(this.stock)) {
                        toastr.error('Stock is unavailable');
                        return;
                    }
                    cart.quantity = newQuantity;
                    cart.total = parseFloat(cart.purchase_rate * cart.quantity).toFixed(2);
                } else {
                    if (parseFloat(this.selectedProduct.quantity) > parseFloat(this.stock)) {
                        toastr.error('Stock is unavailable');
                        return;
                    }
                    this.carts.push({
                        id: this.selectedProduct.id,
                        code: this.selectedProduct.code,
                        category_name: this.selectedProduct.category?.name,
                        name: this.selectedProduct.name,
                        unit_name: this.selectedProduct.unit?.name,
                        purchase_rate: this.selectedProduct.purchase_rate,
                        quantity: this.selectedProduct.quantity,
                        total: this.selectedProduct.total,
                    })
                }
                this.clearProduct();
                this.calculateTotal();
            },

            async quantityRateTotal(cart) {
                this.carts = this.carts.map(item => {
                    if (item.quantity == '') {
                        item.quantity = 1;
                    }
                    item.total = parseFloat(item.purchase_rate * item.quantity).toFixed(2);
                    return item;
                })
                await this.calculateTotal();
            },

            async removeCart(sl) {
                let product = this.carts[sl];
                if (this.damage.id != "" && product.checkEdit == 'true') {
                    let productStock = await axios.post("/get-currentStock", {
                        productId: product.id
                    }).then(res => {
                        return res.data[0].stock;
                    })
                    if(parseFloat(product.quantity) > parseFloat(productStock)){
                        toastr.error("Product Stock unavailable");
                        return;
                    }
                }
                this.carts.splice(sl, 1);
                await this.calculateTotal();
            },

            clearProduct() {
                this.selectedProduct = {
                    id: '',
                    display_name: ''
                }
            },

            calculateTotal() {
                this.damage.total = this.carts.reduce((pr, cu) => {
                    return pr + parseFloat(cu.total)
                }, 0).toFixed(2);
            },

            saveData(event) {
                let formdata = {
                    damage: this.damage,
                    supplier: this.selectedSupplier,
                    carts: this.carts
                }
                let url = this.damage.id != '' ? '/update-damage' : '/damage'
                this.onProgress = true;
                axios.post(url, formdata)
                    .then(async res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        history.pushState(null, '', '/damage');
                        this.damage.invoice = res.data.invoice;
                        if (confirm('Do you want to go to the invoice page?')) {
                            window.open(`/damage-invoice/${res.data.damageId}`, '_blank');
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
                this.damage = {
                    id: "",
                    invoice: "{{$invoice}}",
                    date: moment().format('YYYY-MM-DD'),
                    total: 0,
                };
                this.onProgress = false;
                this.selectedSupplier = {
                    id: '',
                    display_name: 'Walk In Supplier',
                    name: 'Walk In Supplier',
                    phone: '',
                    address: '',
                    type: 'general'
                };
                this.carts = [];
                this.getSupplier();
            },

            async getDamage() {
                await axios.post('/get-damage', {
                    damageId: this.damage.id
                }).then(res => {
                    let damage = res.data[0];
                    let damageKeys = Object.keys(this.damage);
                    damageKeys.forEach(key => {
                        this.damage[key] = damage[key];
                    })

                    damage.details.map(item => {
                        let detail = {
                            id: item.menu_id,
                            code: item.code,
                            category_name: item.category_name,
                            name: item.name,
                            unit_name: item.unit_name,
                            purchase_rate: item.purchase_rate,
                            quantity: item.quantity,
                            total: item.total,
                            checkEdit: 'true'
                        };
                        this.carts.push(detail);
                    })

                    this.selectedSupplier = {
                        id: damage.supplier_id ?? '',
                        name: damage.supplier_name,
                        phone: damage.supplier_phone,
                        address: damage.supplier_address,
                        display_name: damage.supplier_type == 'general' ? 'Walk In Supplier' : `${damage.supplier_name} - ${damage.supplier_phone} - ${damage.supplier_address}`,
                        type: damage.supplier_type
                    }
                })
            }
        },
    })
</script>
@endpush