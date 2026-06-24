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
<div id="salaryList">
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
                            <label for="status">Month</label>
                            <input type="month" id="month" class="form-control" v-model="month" />
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
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Salary</th>
                                    <th>Overtime</th>
                                    <th>Deduction</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(salary, key) in salaries">
                                    <tr>
                                        <td colspan="12" class="text-center">
                                            <strong>
                                                Salary Of Month @{{salary.month | dateFormat('MMMM-YYYY')}}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr v-for="(item, index) in salary.detail">
                                        <td v-html="index + 1"></td>
                                        <td v-html="item.employee?.emp_code"></td>
                                        <td v-html="item.employee?.name"></td>
                                        <td v-html="item.employee?.department?.name"></td>
                                        <td v-html="item.employee?.designation?.name"></td>
                                        <td v-html="item.gross_salary"></td>
                                        <td v-html="item.ot_amount"></td>
                                        <td v-html="item.deduction"></td>
                                        <td v-html="item.total"></td>
                                        <td v-html="item.paid"></td>
                                        <td v-html="item.due"></td>
                                        <td v-html="item.note"></td>
                                    </tr>
                                    <tr v-if="salary.detail.length == 0">
                                        <td colspan="12" class="text-center">Not Found Data</td>
                                    </tr>
                                </template>
                                <tr :class="salaries.length == 0 ? '' : 'd-none'" v-if="salaries.length == 0">
                                    <td colspan="12" class="text-center">Not Found Data</td>
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
        el: '#salaryList',
        data: {
            searchType: '',
            month: moment().format('YYYY-MM'),
            salaries: [],
            departments: [],
            selectedDepartment: null,
            designations: [],
            selectedDesignation: null,
            isLoading: null
        },
        filters: {
            dateFormat(dt, format) {
                return dt == "" || dt == null ? "" : moment(dt).format(format);
            }
        },
        created() {
            this.getDepartment();
            this.getDesignation();
        },

        methods: {
            getDepartment() {
                axios.post(`/get-department`)
                    .then(res => {
                        this.departments = res.data;
                    })
            },
            getDesignation() {
                axios.post(`/get-designation`)
                    .then(res => {
                        this.designations = res.data;
                    })
            },

            onChangeSearchType() {
                this.salaries = [];
                this.selectedDepartment = null;
                this.selectedDesignation = null;
                this.isLoading = null;
            },

            showList() {
                let filter = {
                    departmentId: this.selectedDepartment ? this.selectedDepartment.id : '',
                    designationId: this.selectedDesignation ? this.selectedDesignation.id : '',
                    month: this.month
                }
                this.isLoading = false;
                axios.post(`/get-salary`, filter)
                    .then(res => {
                        this.salaries = res.data;
                        this.isLoading = true;
                    })
            },

            async print() {
                const oldTitle = window.document.title;
                window.document.title = "Salary List"
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
                                <h4 class="m-0">Salary List</h4>
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