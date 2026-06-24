@extends('master')

@section('title', 'Employee List')
@section('breadcrumb', 'Employee List')
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
<div id="employeeList">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card m-0">
                <div class="card-body py-3 px-2">
                    <form @submit.prevent="showList" class="form-inline">
                        <div class="form-group">
                            <label for="searchType">SearchType</label>
                            <select id="searchType" class="form-select" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="department">By Department</option>
                                <option value="designation">By Designation</option>
                            </select>
                        </div>                        
                        <div class="form-group" :class="searchType == 'department' ? '' : 'd-none'" v-if="searchType == 'department'">
                            <label for="Department">Department</label>
                            <v-select :options="departments" v-model="selectedDepartment" label="name"></v-select>
                        </div>
                        <div class="form-group" :class="searchType == 'designation' ? '' : 'd-none'" v-if="searchType == 'designation'">
                            <label for="Designation">Designation</label>
                            <v-select :options="designations" v-model="selectedDesignations" label="name"></v-select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" class="form-select" v-model="status">
                                <option value="">All</option>
                                <option value="p">Pending</option>
                                <option value="a">Active</option>
                            </select>
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
                                    <th>Image</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Birth Date</th>
                                    <th>Join Date</th>
                                    <th>Gender</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in employees">
                                    <td v-html="index + 1"></td>
                                    <td v-html="item.imgSrc"></td>
                                    <td v-html="item.emp_code"></td>
                                    <td v-html="item.name"></td>
                                    <td v-html="item.department.name"></td>
                                    <td v-html="item.designation.name"></td>
                                    <td v-html="item.phone"></td>
                                    <td v-html="item.email"></td>
                                    <td v-html="item.address"></td>
                                    <td v-html="item.birth_date"></td>
                                    <td v-html="item.join_date"></td>
                                    <td v-html="item.gender"></td>
                                    <td v-html="item.salary"></td>
                                    <td v-html="item.statusTxt"></td>
                                </tr>
                                <tr :class="employees.length == 0 ? '' : 'd-none'" v-if="employees.length == 0">
                                    <td colspan="14" class="text-center">Not Found Data</td>
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
        el: '#employeeList',
        data: {
            searchType: '',
            status: '',
            employees: [],
            departments: [],
            selectedDepartment: null,
            designations: [],
            selectedDesignation: null,
            isLoading: null
        },

        created() {
            this.getDepartment();
            this.getDesignation();
        },

        methods: {
            getDepartment() {
                axios.post('/get-department')
                    .then(res => {
                        this.departments = res.data;
                    })
            },
            getDesignation() {
                axios.post('/get-designation')
                    .then(res => {
                        this.designations = res.data;
                    })
            },
            onChangeSearchType() {
                this.employees = [];
                this.selectedDepartment = null;
                this.selectedDesignation = null;
                this.status = "";
                this.isLoading = null;
            },

            showList() {
                let filter = {
                    departmentId: this.selectedDepartment ? this.selectedDepartment.id : '',
                    designationId: this.selectedDesignation ? this.selectedDesignation.id : '',
                    status: this.status
                }
                this.isLoading = false;
                axios.post('/get-employee', filter)
                    .then(res => {
                        this.employees = res.data.map((item, index) => {
                            item.statusTxt = item.status == 'a' ? "<span class='badge bg-success'>Active</span>" : "<span class='badge bg-warning'>Deactive</span>";
                            item.imgSrc = `<a href="${item.image ? '/'+item.image : '/noImage.jpg'}"><img src="${item.image ? '/'+item.image : '/noImage.jpg'}" style="width:45px;height:45px;" class="rounded"/></a>`;
                            return item;
                        });
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Employee List"
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
                                <h5>Employee List</h5>
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