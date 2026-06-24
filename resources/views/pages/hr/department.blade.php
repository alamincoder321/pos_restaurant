@extends('master')

@section('title', 'Department Entry')
@section('breadcrumb', 'Department Entry')
@section('content')
<div class="row" id="department">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Department Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-6 offset-md-3">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="name">Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="name" name="name" v-model="department.name" />
                                </div>
                            </div>
                            <div class="mt-1 text-end">
                                <button class="btn btn-danger" type="button">Reset</button>
                                <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                    <span v-if="department.id == ''">Save</span>
                                    <span v-if="department.id != ''">Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 mt-1">
        <vue-good-table :columns="columns" :rows="departments" :fixed-header="false" :pagination-options="{
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
        el: "#department",
        data() {
            return {
                columns: [{
                        label: "Name",
                        field: 'name'
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
                department: {
                    id: '',
                    name: '',
                },
                departments: [],
                onProgress: false,
            }
        },

        created() {
            this.getDepartment();
        },

        methods: {
            getDepartment() {
                axios.post('/get-department')
                    .then(res => {
                        this.departments = res.data;
                    })
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.department.id);
                let url = this.department.id != '' ? '/update-department' : '/department'
                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.getDepartment();
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
                let keys = Object.keys(this.department);
                keys.forEach(item => {
                    this.department[item] = row[item];
                })
            },

            deleteData(rowId) {
                if (!confirm('Are you sure ?')) {
                    return;
                }
                axios.post('/delete-department', {
                        id: rowId
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getDepartment();
                        }
                    })
            },

            clearData() {
                this.department = {
                    id: '',
                    name: '',
                }
                this.onProgress = false;
            },

        },
    })
</script>
@endpush