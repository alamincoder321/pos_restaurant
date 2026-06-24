@extends('master')

@section('title', 'Customer Entry')
@section('breadcrumb', 'Customer Entry')
@push('style')
<style>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
    }

    tr td,
    tr th {
        vertical-align: middle !important;
        text-align: center !important;
    }
</style>
@endpush
@section('content')
<div class="row" id="customer">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Customer Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-5">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="name">Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="name" name="name" v-model="customer.name" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="owner">Owner:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="owner" name="owner" v-model="customer.owner" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="address">Address:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="address" name="address" v-model="customer.address" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="area_id">Area:</label>
                                <div class="col-8 col-md-9">
                                    <v-select :options="areas" v-model="selectedArea" label="name"></v-select>
                                </div>
                            </div>
                            
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="phone">Mobile:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" class="form-control" autocomplete="off" id="phone" name="phone" v-model="customer.phone" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="email">Email:</label>
                                <div class="col-8 col-md-9">
                                    <input type="email" class="form-control" autocomplete="off" id="email" name="email" v-model="customer.email" />
                                </div>
                            </div>
                            
                            <div class="mt-md-0 mt-1 row">
                                <label class="col-md-3 col-12"></label>
                                <div class="col-md-3 col-12">
                                    <label for="status" class="form-label">
                                        <input type="checkbox" name="status" id="status" :false-value="'p'" :true-value="'a'" v-model="customer.status" />
                                        IsActive
                                    </label>
                                </div>
                                <div class="col-md-6 col-12 text-end">
                                    <button class="btn btn-danger" type="button">Reset</button>
                                    <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                        <span v-if="customer.id == ''">Save</span>
                                        <span v-if="customer.id != ''">Update</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 mt-2 mt-md-0">
                            <div class="form-group ImageBackground">
                                <span class="text-danger">(150 X 150)PX</span>
                                <img :src="imageSrc" class="imageShow" />
                                <label for="image">Upload Image</label>
                                <input type="file" id="image" class="form-control shadow-none" @change="imageUrl" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 mt-1">
        <!-- FILTER -->
        <input type="text" class="form-control w-25 rounded-0 mb-1" v-model="filter" placeholder="Search..." @keydown.enter="getCustomer">
        <div style="overflow-x: auto;">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>CustomerType</th>
                        <th>Mobile</th>
                        <th>Area</th>
                        <th>Previous_Balance</th>
                        <th>Credit_Limit</th>
                        <th>Status</th>
                        <th>Added_By</th>
                        <th>Updated_By</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(row, index) in customers" :key="row.id">
                        <td>@{{ row.sl }}</td>
                        <td>@{{ row.code }}</td>
                        <td>@{{ row.name }}</td>
                        <td>@{{ row.owner }}</td>
                        <td>@{{ row.type }}</td>
                        <td>@{{ row.phone }}</td>
                        <td>@{{ row.area?.name }}</td>
                        <td>@{{ row.previous_due }}</td>
                        <td>@{{ row.credit_limit }}</td>
                        <td v-html="row.statusTxt"></td>
                        <td>@{{ row.ad_user?.name }}</td>
                        <td>@{{ row.up_user?.name }}</td>
                        <td>
                            <a href="" title="edit" @click.prevent="editData(row)">
                                <i class="bi bi-pen text-info" style="font-size: 14px;"></i>
                            </a>
                            <a href="" title="delete" @click.prevent="deleteData(row.id)">
                                <i class="bi bi-trash text-danger" style="font-size: 14px;"></i>
                            </a>
                        </td>
                    </tr>
                    <tr style="display: none;" :style="{display: customers.length == 0 ? '' : 'none'}" v-if="customers.length == 0">
                        <td colspan="13">Data not found</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!-- PAGINATION -->
        <div class="text-left" style="margin-top: 10px; display: none;" :style="{display: customers.length > 0 ? '' : 'none'}" v-if="customers.length > 0">
            <button @click="changePage(page - 1)" :disabled="page == 1">Prev</button>

            <button
                v-for="(p, index) in pageNumbers"
                :key="p + '-' + index"
                :disabled="p === '...'"
                @click="changePage(p)"
                :class="['paginateBtn', p === page ? 'active-page' : '']">
                @{{ p }}
            </button>

            <button @click="changePage(page + 1)" :disabled="page == total_page">Next</button>
        </div>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#customer",
        data() {
            return {
                page: 1,
                per_page: 10,
                total_page: 0,
                filter: '',

                customer: {
                    id: '',
                    name: '',
                    owner: '',
                    email: '',
                    phone: '',
                    type: 'retail',
                    address: '',
                    area_id: '',
                    previous_due: 0,
                    credit_limit: 0,
                    status: 'a',
                    image: ''
                },
                customers: [],
                areas: [],
                selectedArea: null,

                imageSrc: "/noImage.jpg",
                onProgress: false,
            }
        },

        computed: {
            pageNumbers() {
                let pages = [];
                let total = this.total_page;
                let current = this.page;

                if (total <= 7) {
                    for (let i = 1; i <= total; i++) {
                        pages.push(i);
                    }
                } else {
                    if (current <= 4) {
                        pages = [1, 2, 3, 4, 5, '...', total];
                    } else if (current >= total - 3) {
                        pages = [1, '...', total - 4, total - 3, total - 2, total - 1, total];
                    } else {
                        pages = [1, '...', current - 1, current, current + 1, '...', total];
                    }
                }

                return pages;
            }
        },

        watch: {
            filter(newVal, oldVal) {
                if (newVal == '') {
                    this.getCustomer();
                } else if (newVal.length >= 3) {
                    this.getCustomer();
                }
            },
        },

        created() {
            this.getArea();
            this.getCustomer();
        },

        methods: {
            getArea() {
                axios.post('/get-area')
                    .then(res => {
                        this.areas = res.data;
                    })
            },
            getCustomer() {
                axios.post(`/get-customer?page=${this.page}&per_page=${this.per_page}&search=${this.filter}`)
                    .then(res => {
                        this.total_page = res.data.last_page;
                        this.customers = res.data.data.map((item, index) => {
                            item.sl = ((res.data.current_page - 1) * this.per_page) + index + 1;
                            item.statusTxt = item.status == 'a' ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-warning'>Deactive</span>";
                            item.imgSrc = `<a href="${item.image ? '/'+item.image : '/noImage.jpg'}"><img src="${item.image ? '/'+item.image : '/noImage.jpg'}" style="width:30px;height:30px;" class="rounded"/></a>`;
                            return item;
                        });
                    })
            },
            changePage(p) {
                if (p === '...') return;
                if (p < 1 || p > this.total_page) return;

                this.page = Number(p);
                this.getCustomer();
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.customer.id);
                formdata.append('image', this.customer.image);
                formdata.append('status', this.customer.status);
                formdata.append('area_id', this.selectedArea ? this.selectedArea.id : '');
                let url = this.customer.id != '' ? '/update-customer' : '/customer'
                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.getCustomer();
                    })
                    .catch(err => {
                        this.onProgress = false
                        var r = JSON.parse(err.request.response);
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

            editData(row) {
                let keys = Object.keys(this.customer);
                keys.forEach(item => {
                    this.customer[item] = row[item];
                })
                this.selectedArea = {
                    id: row.area_id,
                    name: row.area?.name
                }
                this.imageSrc = row.image ? '/' + row.image : "/noImage.jpg";
            },

            deleteData(rowId) {
                if (!confirm('Are you sure ?')) {
                    return;
                }
                axios.post('/delete-customer', {
                        id: rowId
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getCustomer();
                        }
                    })
            },

            clearData() {
                this.customer = {
                    id: '',
                    name: '',
                    owner: '',
                    email: '',
                    phone: '',
                    type: 'retail',
                    address: '',
                    area_id: '',
                    previous_due: 0,
                    credit_limit: 0,
                    status: 'a',
                    image: ''
                }
                this.imageSrc = "/noImage.jpg";
                this.onProgress = false;
            },

            imageUrl(event) {
                const WIDTH = 150;
                const HEIGHT = 150;
                if (event.target.files[0]) {
                    let reader = new FileReader();
                    reader.readAsDataURL(event.target.files[0]);
                    reader.onload = (ev) => {
                        let img = new Image();
                        img.src = ev.target.result;
                        img.onload = async e => {
                            let canvas = document.createElement('canvas');
                            canvas.width = WIDTH;
                            canvas.height = HEIGHT;
                            const context = canvas.getContext("2d");
                            context.drawImage(img, 0, 0, canvas.width, canvas.height);
                            let new_img_url = context.canvas.toDataURL(event.target.files[0].type);
                            this.imageSrc = new_img_url;
                            const resizedImage = await new Promise(rs => canvas.toBlob(rs, 'image/jpeg', 1))
                            this.customer.image = new File([resizedImage], event.target.files[0].name, {
                                type: resizedImage.type
                            });
                        }
                    }
                } else {
                    event.target.value = '';
                }
            }
        },
    })
</script>
@endpush