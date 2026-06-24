@extends('master')

@section('title', 'Employee Salary Generate')
@section('breadcrumb', 'Employee Salary Generate')
@push('style')
<style>
    .table>thead>tr>th {
        text-align: center !important;
        background-color: gray;
        color: #fff;
        vertical-align: middle !important;
    }
    .table>tbody>tr>td{
        vertical-align: middle !important;
    }
</style>
@endpush
@section('content')
<div id="salaries">
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
                            <label for="month">Month</label>
                            <input type="month" id="month" v-model="salary.month" class="form-control"/>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-sm">Generate</button>
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
                    <div class="row">
                        <div class="col-md-4 col-12 d-flex align-items-center gap-2 mb-1">
                            <label for="note">Note</label>
                            <input type="text" id="note" @input="setNote" v-model="salary.note" class="form-control form-control-sm" placeholder="Note here" autocomplete="off" />
                        </div>
                    </div>
                    <div id="reportContent" style="overflow-x: auto;">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" @change="checkAll" style="cursor:pointer;width: 20px;height: 20px;" :true-value="`true`" :false-value="`false`" v-model="allCheck" />
                                    </th>
                                    <th>Sl</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Salary</th>
                                    <th>OverTime</th>
                                    <th>Deduction</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th style="width: 15%;">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in salaries">
                                    <td>
                                        <input type="checkbox" @change="paidSalary" style="cursor:pointer;width: 20px;height: 20px;" :true-value="`true`" :false-value="`false`" v-model="item.check" />
                                    </td>
                                    <td v-html="index + 1"></td>
                                    <td v-html="item.emp_code"></td>
                                    <td v-html="item.name"></td>
                                    <td v-html="item.department.name"></td>
                                    <td v-html="item.designation.name"></td>
                                    <td v-html="item.gross_salary"></td>
                                    <td>
                                        <input type="number" v-model="item.ot_amount" class="form-control form-control-sm" @input="calculateSalary">
                                    </td>
                                    <td>
                                        <input type="number" v-model="item.deduction" class="form-control form-control-sm" @input="calculateSalary">
                                    </td>
                                    <td v-html="item.total"></td>
                                    <td>
                                        <input type="number" v-model="item.paid" class="form-control form-control-sm" @input="paidSalary">
                                    </td>
                                    <td v-html="item.due"></td>
                                    <td v-html="item.note"></td>
                                </tr>
                                <tr :class="salaries.length > 0 ? '' : 'd-none'" v-if="salaries.length > 0">
                                    <td colspan="9" class="text-end py-2">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="text-center py-2">
                                        <strong v-text="salary.amount"></strong>
                                    </td>
                                    <td colspan="3" class="text-end py-2">
                                        <button @click="saveSalary" class="btn btn-sm btn-primary" type="button" :disabled="onProgress">
                                            <span v-if="salary.salary_id == ''">Save</span>
                                            <span v-if="salary.salary_id != ''">Update</span>
                                        </button>
                                    </td>
                                </tr>
                                <tr :class="salaries.length == 0 ? '' : 'd-none'" v-if="salaries.length == 0">
                                    <td colspan="13" class="text-center">Not Found Data</td>
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
        el: '#salaries',
        data: {
            searchType: "",
            allCheck: 'false',
            month: moment().format('YYYY-MM'),
            departments: [],
            selectedDepartment: null,
            designations: [],
            selectedDesignation: null,
            salaries: [],
            salary: {
                month: moment().format('YYYY-MM'),
                amount: 0,
                note: "",
                salary_id: ''
            },
            isLoading: null,
            onProgress: false
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
                    month: this.salary.month
                }
                this.isLoading = false;
                axios.post(`/check-salary`, filter)
                    .then(res => {
                        this.salaries = res.data.salaries;
                        this.salary.salary_id = res.data.salary_id;
                        let checkSalary = this.salaries.filter(item => item.check == 'true');
                        this.salary.amount = checkSalary.reduce((pr, cu) => {
                            return pr + parseFloat(cu.total)
                        }, 0).toFixed(2);
                        this.allCheck = checkSalary.length == this.salaries.length && this.salaries.length != 0 ? 'true' : 'false';

                        this.isLoading = true;
                    })
            },

            calculateSalary() {
                this.salaries = this.salaries.map(item => {
                    item.total = parseFloat((+item.gross_salary + +item.ot_amount) - item.deduction).toFixed(2);
                    item.paid = item.total;
                    return item;
                })
                this.salary.amount = this.salaries.filter(item => item.check == 'true').reduce((pr, cu) => {
                    return pr + parseFloat(cu.total)
                }, 0).toFixed(2);
            },
            paidSalary() {
                this.salaries = this.salaries.map(item => {
                    if (parseFloat(item.paid) > parseFloat(item.total)) {
                        item.paid = item.total;
                    }
                    item.due = parseFloat(item.total - item.paid).toFixed(2);
                    return item;
                })
                this.allCheck = this.salaries.length === this.salaries.filter(item => item.check == 'true').length ? 'true' : 'false';
                this.salary.amount = this.salaries.filter(item => item.check == 'true').reduce((pr, cu) => {
                    return pr + parseFloat(cu.total)
                }, 0).toFixed(2);
            },
            setNote() {
                this.salaries = this.salaries.map(item => {
                    item.note = this.salary.note;
                    return item;
                })
            },
            checkAll() {
                if (this.allCheck == 'true') {
                    this.salaries = this.salaries.map(item => {
                        item.check = 'true';
                        return item;
                    })
                } else {
                    this.salaries = this.salaries.map(item => {
                        item.check = 'false';
                        return item;
                    })
                }
                this.calculateSalary();
            },

            saveSalary() {
                let carts = this.salaries.filter(item => item.check == 'true');
                if (carts.length == 0) {
                    toastr.error("Salary cart is empty");
                    successSound('error');
                    return;
                }
                let formdata = new FormData();
                formdata.append('carts', JSON.stringify(carts));
                formdata.append('salary', JSON.stringify(this.salary));

                let url = this.salary.salary_id != '' ? `/update-salary` : `/salary`
                this.onProgress = true;
                axios.post(url, formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.onProgress = false
                        this.salaries = [];
                        this.isLoading = null;
                        this.allCheck = 'false';
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
            }
        },
    })
</script>
@endpush