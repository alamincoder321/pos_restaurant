@extends('master')

@section('title', 'Invest Account Entry')
@section('breadcrumb', 'Invest Account Entry')
@section('content')
<div class="row" id="account">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Invest Account Entry Form</h5>
                <form @submit.prevent="{{ buttonAction('entry') || buttonAction('update') ? 'saveData($event)' : null }}">
                    <div class="row">
                        <div class="col-12 col-md-6 offset-md-3">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="name">Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="name" name="name" v-model="account.name" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="description">Description:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="description" name="description" v-model="account.description" />
                                </div>
                            </div>
                            
                            <div class="mt-1 text-end">
                                @if (buttonAction('entry') || buttonAction('update'))
                                <button class="btn btn-danger" type="button">Reset</button>
                                <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                    <span v-if="account.id == ''">Save</span>
                                    <span v-if="account.id != ''">Update</span>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 mt-1">
        <vue-good-table :columns="columns" :rows="accounts" :fixed-header="false" :pagination-options="{
                enabled: true,
                perPage: 100,
            }" :search-options="{ enabled: true }" :line-numbers="true" styleClass="vgt-table condensed" max-height="550px">
            <template #table-row="props">
                <span class="d-flex gap-2 justify-content-end" v-if="props.column.field == 'before'">
                    @if (buttonAction('update'))
                    <a href="" title="edit" @click.prevent="editData(props.row)">
                        <i class="bi bi-pen text-info" style="font-size: 14px;"></i>
                    </a>
                    @endif
                    @if (buttonAction('delete'))
                    <a href="" title="delete" @click.prevent="deleteData(props.row.id)">
                        <i class="bi bi-trash text-danger" style="font-size: 14px;"></i>
                    </a>
                    @endif
                </span>
            </template>
        </vue-good-table>
    </div>
</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#account",
        data() {
            return {
                columns: [{
                        label: "Name",
                        field: 'name'
                    },
                    {
                        label: "Description",
                        field: 'description'
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
                account: {
                    id: '',
                    name: '',
                    description: '',
                },
                accounts: [],
                loading: true,
                onProgress: false,
            }
        },

        created() {
            this.getAccount();

        },

        methods: {
            getAccount() {
                this.loading = true;
                axios.post(`/get-invest`)
                    .then(res => {
                        this.accounts = res.data.map((item, index) => {
                            item.sl = index + 1;
                            return item;
                        })
                        this.loading = false;
                    })
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.account.id);
                let url = this.account.id != '' ? `/update-invest` : `/invest`

                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.getAccount();
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
                let keys = Object.keys(this.account);
                keys.forEach(item => {
                    this.account[item] = row[item];
                })
            },

            deleteData(rowId) {
                if (!confirm("Are you sure ?")) {
                    return;
                }
                axios.post(`/delete-invest`, {
                        id: rowId
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);

                            this.getAccount();
                        }
                    })
            },

            clearData() {
                this.account = {
                    id: '',
                    name: '',
                    description: '',
                }
                this.onProgress = false;
            }

        },
    })
</script>
@endpush