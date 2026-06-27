@extends('master')

@section('title', 'Table Entry')
@section('breadcrumb', 'Table Entry')
@section('content')
<div class="row" id="table">
    <div class="col-12 col-md-12">
        <div class="card mb-0">
            <div class="card-body">
                <h5 class="card-title">Table Entry Form</h5>
                <form @submit.prevent="saveData($event)">
                    <div class="row">
                        <div class="col-12 col-md-6 offset-md-3">
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="floor_id">Floor:</label>
                                <div class="col-8 col-md-9">
                                    <select class="form-select" id="floor_id" name="floor_id" v-model="table.floor_id">
                                        <option value="">Select Floor</option>
                                        <option v-for="floor in floors" :value="floor.id">@{{ floor.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="name">Name:</label>
                                <div class="col-8 col-md-9">
                                    <input type="text" class="form-control" autocomplete="off" id="name" name="name" v-model="table.name" />
                                </div>
                            </div>
                            <div class="mb-1 row">
                                <label class="form-label col-4 col-md-3" for="capacity">Capacity:</label>
                                <div class="col-8 col-md-9">
                                    <input type="number" min="0" class="form-control" autocomplete="off" id="capacity" name="capacity" v-model="table.capacity" />
                                </div>
                            </div>
                            <div class="mb-1 row" v-if="table.id != ''" :class="table.id != '' ? '' : 'd-none'">
                                <label class="form-label col-4 col-md-3" for="table_status">Status:</label>
                                <div class="col-8 col-md-9">
                                    <select class="form-select" id="table_status" name="table_status" v-model="table.table_status">
                                        <option value="available">Available</option>
                                        <option value="occupied">Occupied</option>
                                        <option value="reserved">Reserved</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-1 text-end">
                                <button class="btn btn-danger" type="button">Reset</button>
                                <button class="btn btn-primary" type="submit" :disabled="onProgress">
                                    <span v-if="table.id == ''">Save</span>
                                    <span v-if="table.id != ''">Update</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-12 mt-1">
        <vue-good-table :columns="columns" :rows="tables" :fixed-header="false" :pagination-options="{
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
        el: "#table",
        data() {
            return {
                columns: [{
                        label: "Floor",
                        field: 'floor.name'
                    },
                    {
                        label: "Name",
                        field: 'name'
                    },
                    {
                        label: "Capacity",
                        field: 'capacity'
                    },
                    {
                        label: "Status",
                        field: 'table_status_txt'
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
                table: {
                    id: '',
                    name: '',
                    floor_id: '',
                    capacity: '',
                    table_status: 'available'
                },
                tables: [],
                floors: [],
                onProgress: false,
            }
        },

        created() {
            this.getFloor();
            this.getTable();
        },

        methods: {
            getFloor() {
                axios.post('/get-floor')
                    .then(res => {
                        this.floors = res.data;
                    })
            },
            getTable() {
                axios.post('/get-table')
                    .then(res => {
                        this.tables = res.data.map(item => {
                            item.table_status_txt = item.table_status.charAt(0).toUpperCase() + item.table_status.slice(1);
                            return item;
                        });
                    })
            },
            saveData(event) {
                let formdata = new FormData(event.target);
                formdata.append('id', this.table.id);
                let url = this.table.id != '' ? '/update-table' : '/table'
                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.clearData();
                        this.getTable();
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
                let keys = Object.keys(this.table);
                keys.forEach(item => {
                    this.table[item] = row[item];
                })
            },

            deleteData(rowId) {
                if (!confirm('Are you sure ?')) {
                    return;
                }
                axios.post('/delete-table', {
                        id: rowId
                    })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.getTable();
                        }
                    })
            },

            clearData() {
                this.table.id = '';
                this.table.name = '';
                this.table.capacity = '';
                this.onProgress = false;
            },

        },
    })
</script>
@endpush