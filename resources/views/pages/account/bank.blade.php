@extends('master')

@section('title', 'Bank Entry')
@section('breadcrumb', 'Bank Entry')
@section('content')
<div class="row" id="bank">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Bank Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="name">Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="name" name="name" v-model="bank.name" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="number">Number:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="number" name="number" v-model="bank.number" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="type">Type:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="type" name="type" v-model="bank.type" />
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="bank_name">Bank Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="bank_name" name="bank_name" v-model="bank.bank_name" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="branch_name">Branch Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="branch_name" name="branch_name" v-model="bank.branch_name" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="balance">Balance:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" min="0" step="any" :readonly="bank.id != '' && (role == 'user' || role == 'manager')" class="form-control" autocomplete="off" id="balance" name="balance" v-model="bank.balance" />
                                </div>
                            </div>
                            <div class="mt-1 row">
                                <div class="col-4 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" id="status" type="checkbox" :true-value="'a'" :false-value="'p'" v-model="bank.status">
                                        <label class="form-label" for="status">Is Active</label>
                                    </div>
                                </div>
                                <div class="col-8 col-md-9 text-end">
                                    <button class="btn btn-danger" type="button">Reset</button>
                                    <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                        <span v-if="bank.id == ''">Save</span>
                                        <span v-if="bank.id != ''">Update</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 mt-1">
        <vue-good-table :columns="columns" :rows="banks" :fixed-header="false" :pagination-options="{
                enabled: true,
                perPage: 100,
            }" :search-options="{ enabled: true }" :line-numbers="true" styleClass="vgt-table condensed" max-height="550px">
            <template #table-row="props">
                <span class="d-flex gap-2 justify-content-end" v-if="props.column.field == 'before'">
                    <a href="" title="edit" @click.prevent="editData(props.row)">
                        <i class="bi bi-pen text-info" style="font-size: 14px;"></i>
                    </a>
                    <a href="" title="delete" @click.prevent="deleteData(props.row.id)">
                        <i class="bi bi-trash text-danger" style="font-size: 14px;"></i>
                    </a>
                </span>
            </template>
        </vue-good-table>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#bank",
        data() {
            return {
                columns: [{
                        label: "Account Name",
                        field: 'name'
                    },
                    {
                        label: "Account Number",
                        field: 'number'
                    },
                    {
                        label: "Account Type",
                        field: 'type'
                    },
                    {
                        label: "Bank Name",
                        field: 'bank_name'
                    },
                    {
                        label: "Branch Name",
                        field: 'branch_name'
                    },
                    {
                        label: "Balance",
                        field: 'balance'
                    },
                    {
                        label: "Added_By",
                        field: 'ad_user.username'
                    },
                    {
                        label: "Updated_By",
                        field: 'up_user.username'
                    },
                    {
                        label: "Action",
                        field: "before"
                    }
                ],
                bank: {
                    id: '',
                    name: '',
                    number: '',
                    type: '',
                    bank_name: '',
                    branch_name: '',
                    balance: 0,
                    status: 'a',
                },
                banks: [],

                role: "{{ auth()->user()->role }}",
                loading: true,
                onProgress: false
            }
        },

        created() {
            this.getBank();
        },

        methods: {
            getBank() {
                this.loading = true;
                axios.post(`/get-bank`)
                    .then(res => {
                        this.banks = res.data.map((item, index) => {
                            item.sl = index + 1;
                            item.statusTxt = item.status == 'a' ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-warning'>Deactive</span>";
                            return item;
                        });
                        this.loading = false;
                    })
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.bank.id);
                formdata.append('status', this.bank.status);
                let url = this.bank.id != '' ? `/update-bank` : `/bank`

                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.getBank();
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
                let keys = Object.keys(this.bank);
                keys.forEach(item => {
                    this.bank[item] = row[item];
                })
            },

            deleteData(rowId) {
                if (!confirm("Are you sure ?")) {
                    return;
                }
                axios.post(`/delete-bank`, {
                        id: rowId
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getBank();
                        }
                    })
            },

            clearData() {
                this.bank = {
                    id: '',
                    name: '',
                    number: '',
                    type: '',
                    bank_name: '',
                    branch_name: '',
                    balance: 0,
                    status: 'a',
                }
                this.onProgress = false;
            }
        },
    })
</script>
@endpush