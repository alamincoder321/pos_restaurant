@extends('master')

@section('title', 'User Access Entry')
@section('breadcrumb', 'User Access Entry')
@push('style')
<style scoped>
    .table tr td {
        padding: 4px 8px !important;
        vertical-align: middle !important;
    }

    .table tr td label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    input[type="checkbox"] {
        width: 15px;
        height: 15px;
    }
</style>
@endpush
@section('content')
<div id="userAccessPush">
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card mb-2">
                <div class="card-body py-3">
                    <h2 class="mb-0 text-center">User Access For {{$user->name}} - {{$user->code}}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card mb-0">
                <div class="card-body py-3 px-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="w-25">
                            <label for="checkAll" style="cursor: pointer;display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" id="checkAll" @change="checkAll">Check All
                            </label>
                        </div>
                        <div class="w-75 input-group justify-content-end gap-2">
                            <label for="entry" style="cursor: pointer;display: flex; align-items: center; gap: 4px;">
                                <input type="checkbox" value="entry" id="entry" v-model="action"> Entry
                            </label>
                            <label for="update" style="cursor: pointer;display: flex; align-items: center; gap: 4px;">
                                <input type="checkbox" value="update" id="update" v-model="action"> Update
                            </label>
                            <label for="delete" style="cursor: pointer;display: flex; align-items: center; gap: 4px;">
                                <input type="checkbox" value="delete" id="delete" v-model="action"> Delete
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12 col-md-4">
            <table class="table table-bordered">
                <tr>
                    <td style="background: #ccc;padding: 5px 8px !important;">
                        <label for="sales">
                            <input type="checkbox" id="sales" value="sales" @change="handleGroupCheck($event)" />
                            <strong>Order Panel</strong>
                        </label>
                    </td>
                </tr>
                <tr class="sales">
                    <td>
                        <label for="sale">
                            <input type="checkbox" id="sale" value="sale" v-model="useraccess" />
                            <span>Order Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="sales">
                    <td>
                        <label for="saleRecord">
                            <input type="checkbox" id="saleRecord" value="saleRecord" v-model="useraccess" />
                            <span>Order Record</span>
                        </label>
                    </td>
                </tr>
                <tr class="sales">
                    <td>
                        <label for="saleReturn">
                            <input type="checkbox" id="saleReturn" value="saleReturn" v-model="useraccess" />
                            <span>Order Return Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="sales">
                    <td>
                        <label for="saleReturnRecord">
                            <input type="checkbox" id="saleReturnRecord" value="saleReturnRecord" v-model="useraccess" />
                            <span>Order Return Record</span>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-12 col-md-4">
            <table class="table table-bordered">
                <tr>
                    <td style="background: #ccc;padding: 5px 8px !important;">
                        <label for="hrm">
                            <input type="checkbox" id="hrm" value="hrm" @change="handleGroupCheck($event)" />
                            <strong>HRM Panel</strong>
                        </label>
                    </td>
                </tr>
                <tr class="hrm">
                    <td>
                        <label for="salary">
                            <input type="checkbox" id="salary" value="salary" v-model="useraccess" />
                            <span>Salary Generate</span>
                        </label>
                    </td>
                </tr>
                <tr class="hrm">
                    <td>
                        <label for="salaryList">
                            <input type="checkbox" id="salaryList" value="salaryList" v-model="useraccess" />
                            <span>Salary Record</span>
                        </label>
                    </td>
                </tr>
                <tr class="hrm">
                    <td>
                        <label for="employee">
                            <input type="checkbox" id="employee" value="employee" v-model="useraccess" />
                            <span>Employee Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="hrm">
                    <td>
                        <label for="employeeList">
                            <input type="checkbox" id="employeeList" value="employeeList" v-model="useraccess" />
                            <span>Employee List</span>
                        </label>
                    </td>
                </tr>
                <tr class="hrm">
                    <td>
                        <label for="department">
                            <input type="checkbox" id="department" value="department" v-model="useraccess" />
                            <span>Department Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="hrm">
                    <td>
                        <label for="designation">
                            <input type="checkbox" id="designation" value="designation" v-model="useraccess" />
                            <span>Designation Entry</span>
                        </label>
                    </td>
                </tr>
            </table>
        </div>

        <div class="col-12 col-md-4">
            <table class="table table-bordered">
                <tr>
                    <td style="background: #ccc;padding: 5px 8px !important;">
                        <label for="accounts">
                            <input type="checkbox" id="accounts" value="accounts" @change="handleGroupCheck($event)" />
                            <strong>Accounts Panel</strong>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="expense">
                            <input type="checkbox" id="expense" value="expense" v-model="useraccess" />
                            <span>Expense Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="income">
                            <input type="checkbox" id="income" value="income" v-model="useraccess" />
                            <span>Income Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="receive">
                            <input type="checkbox" id="receive" value="receive" v-model="useraccess" />
                            <span>Receive</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="payment">
                            <input type="checkbox" id="payment" value="payment" v-model="useraccess" />
                            <span>Payment</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="bankTransaction">
                            <input type="checkbox" id="bankTransaction" value="bankTransaction" v-model="useraccess" />
                            <span>BankTransaction</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="accounthead">
                            <input type="checkbox" id="accounthead" value="accounthead" v-model="useraccess" />
                            <span>AccountHead Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="bank">
                            <input type="checkbox" id="bank" value="bank" v-model="useraccess" />
                            <span>Bank Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="investTransaction">
                            <input type="checkbox" id="investTransaction" value="investTransaction" v-model="useraccess" />
                            <span>InvestTransaction</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="investTransactionList">
                            <input type="checkbox" id="investTransactionList" value="investTransactionList" v-model="useraccess" />
                            <span>InvestTransaction List</span>
                        </label>
                    </td>
                </tr>
                <tr class="accounts">
                    <td>
                        <label for="invest">
                            <input type="checkbox" id="invest" value="invest" v-model="useraccess" />
                            <span>InvestAccount Entry</span>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="col-12 col-md-4">
            <table class="table table-bordered">
                <tr>
                    <td style="background: #ccc;padding: 5px 8px !important;">
                        <label for="reports">
                            <input type="checkbox" id="reports" value="reports" @change="handleGroupCheck($event)" />
                            <strong>Reports Panel</strong>
                        </label>
                    </td>
                </tr>
                <tr class="reports">
                    <td>
                        <label for="profitLoss">
                            <input type="checkbox" id="profitLoss" value="profitLoss" v-model="useraccess" />
                            <span>Profit/Loss</span>
                        </label>
                    </td>
                </tr>
                <tr class="reports">
                    <td>
                        <label for="cashLedger">
                            <input type="checkbox" id="cashLedger" value="cashLedger" v-model="useraccess" />
                            <span>Cash Ledger</span>
                        </label>
                    </td>
                </tr>
                <tr class="reports">
                    <td>
                        <label for="bankLedger">
                            <input type="checkbox" id="bankLedger" value="bankLedger" v-model="useraccess" />
                            <span>Bank Ledger</span>
                        </label>
                    </td>
                </tr>
                <tr class="reports">
                    <td>
                        <label for="customerDue">
                            <input type="checkbox" id="customerDue" value="customerDue" v-model="useraccess" />
                            <span>Customer Due</span>
                        </label>
                    </td>
                </tr>
                <tr class="reports">
                    <td>
                        <label for="customerLedger">
                            <input type="checkbox" id="customerLedger" value="customerLedger" v-model="useraccess" />
                            <span>Customer Ledger</span>
                        </label>
                    </td>
                </tr>

                <tr class="reports">
                    <td>
                        <label for="dailyReport">
                            <input type="checkbox" id="dailyReport" value="dailyReport" v-model="useraccess" />
                            <span>Daily Report</span>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-12 col-md-4">
            <table class="table table-bordered">
                <tr>
                    <td colspan="2" style="background: #ccc;padding: 5px 8px !important;">
                        <label for="control">
                            <input type="checkbox" id="control" value="control" @change="handleGroupCheck($event)" />
                            <strong>Control Panel</strong>
                        </label>
                    </td>
                </tr>
                <tr class="control">
                    <td>
                        <label for="menu">
                            <input type="checkbox" id="menu" value="menu" v-model="useraccess" />
                            <span>Menu Entry</span>
                        </label>
                    </td>
                    <td>
                        <label for="menuList">
                            <input type="checkbox" id="menuList" value="menuList" v-model="useraccess" />
                            <span>Menu List</span>
                        </label>
                    </td>
                </tr>
                <tr class="control">
                    <td>
                        <label for="customer">
                            <input type="checkbox" id="customer" value="customer" v-model="useraccess" />
                            <span>Customer Entry</span>
                        </label>
                    </td>
                    <td>
                        <label for="customerList">
                            <input type="checkbox" id="customerList" value="customerList" v-model="useraccess" />
                            <span>Customer List</span>
                        </label>
                    </td>
                </tr>
                <tr class="control">
                    <td>
                        <label for="area">
                            <input type="checkbox" id="area" value="area" v-model="useraccess" />
                            <span>Area Entry</span>
                        </label>
                    </td>
                    <td>
                        <label for="unit">
                            <input type="checkbox" id="unit" value="unit" v-model="useraccess" />
                            <span>Unit Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="control">
                    <td colspan="2">
                        <label for="category">
                            <input type="checkbox" id="category" value="category" v-model="useraccess" />
                            <span>Category Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="control">
                    <td>
                        <label for="floor">
                            <input type="checkbox" id="floor" value="floor" v-model="useraccess" />
                            <span>Floor Entry</span>
                        </label>
                    </td>
                    <td>
                        <label for="table">
                            <input type="checkbox" id="table" value="table" v-model="useraccess" />
                            <span>Table Entry</span>
                        </label>
                    </td>
                </tr>
                <tr class="control">
                    <td>
                        <label for="user">
                            <input type="checkbox" id="user" value="user" v-model="useraccess" />
                            <span>User Entry</span>
                        </label>
                    </td>
                    <td>
                        <label for="userAccess">
                            <input type="checkbox" id="userAccess" value="userAccess" v-model="useraccess" />
                            <span>User Access</span>
                        </label>
                    </td>
                </tr>
                <tr class="control">
                    <td>
                        <label for="companyProfile">
                            <input type="checkbox" id="companyProfile" value="companyProfile" v-model="useraccess" />
                            <span>Company Profile</span>
                        </label>
                    </td>
                    <td>
                        <label for="businessInfo">
                            <input type="checkbox" id="businessInfo" value="businessInfo" v-model="useraccess" />
                            <span>Business Info</span>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
    </div>


    <div class="row mt-2">
        <div class="col-12 col-md-12">
            <div class="card mb-1">
                <div class="card-body py-3 px-2 text-end">
                    @if(buttonAction('entry') || buttonAction('update'))
                    <button type="button" class="btn btn-success" @click="saveData">Save Access</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push("js")
<script>
    new Vue({
        el: "#userAccessPush",
        data() {
            return {
                action: [],
                useraccess: [],
                onProgress: false,
            }
        },
        mounted() {
            ['sales', 'purchases', 'accounts', 'control', 'hrm', 'reports'].forEach(group => {
                document.querySelectorAll(`tr.${group} input[type="checkbox"]`).forEach(cb => {
                    cb.addEventListener('change', () => {
                        const all = Array.from(document.querySelectorAll(`tr.${group} input[type="checkbox"]`))
                            .filter(c => !['entry', 'update', 'delete'].includes(c.id));
                        const groupCheckbox = document.getElementById(group);
                        if (all.every(c => c.checked)) {
                            groupCheckbox.checked = true;
                        } else {
                            groupCheckbox.checked = false;
                        }

                        const allCheckboxes = Array.from(document.querySelectorAll('input[type="checkbox"]:not(#checkAll)'))
                            .filter(c => !['entry', 'update', 'delete'].includes(c.id));
                        if (allCheckboxes.every(c => c.checked)) {
                            document.getElementById('checkAll').checked = true;
                        } else {
                            document.getElementById('checkAll').checked = false;
                        }
                    });
                });
            });
        },

        created() {
            this.getUserAccess();
        },

        methods: {
            getUserAccess() {
                axios.post("/get-userAccess", {
                        id: "{{$user->id }}"
                    })
                    .then(res => {
                        this.action = res.data.action == null ? [] : res.data.action.split(',');
                        this.useraccess = JSON.parse(res.data.access);
                        this.$nextTick(() => {
                            ['sales', 'purchases', 'accounts', 'control', 'hrm', 'reports'].forEach(group => {
                                const groupCheckbox = document.getElementById(group);
                                const groupCheckboxes = Array.from(document.querySelectorAll(`tr.${group} input[type="checkbox"]`))
                                    .filter(cb => !['entry', 'update', 'delete'].includes(cb.id));
                                const groupValues = groupCheckboxes.map(cb => cb.value);
                                groupCheckbox.checked = groupValues.every(val => this.useraccess.includes(val));
                            });

                            const allCheckboxes = Array.from(document.querySelectorAll('input[type="checkbox"]:not(#checkAll)'))
                                .filter(cb => !['entry', 'update', 'delete'].includes(cb.id));
                            const allValues = allCheckboxes.map(cb => cb.value);
                            const allChecked = allValues.every(val => this.useraccess.includes(val));
                            document.getElementById('checkAll').checked = allChecked;
                        });
                    });
            },
            handleGroupCheck(event) {
                const checked = event.target.checked;
                const group = event.target.value;
                const groupCheckboxes = Array.from(document.querySelectorAll(`tr.${group} input[type="checkbox"]`))
                    .filter(cb => !['entry', 'update', 'delete'].includes(cb.id));
                const values = groupCheckboxes.map(cb => cb.value);

                if (checked) {
                    this.useraccess = Array.from(new Set([...this.useraccess, ...values]));
                } else {
                    this.useraccess = this.useraccess.filter(val => !values.includes(val));
                }
                this.$nextTick(() => {
                    const groupCheckboxes = Array.from(document.querySelectorAll(`tr.${group} input[type="checkbox"]`))
                        .filter(cb => !['entry', 'update', 'delete'].includes(cb.id));
                    groupCheckboxes.forEach(cb => {
                        cb.checked = this.useraccess.includes(cb.value);
                    });
                    const groupCheckbox = document.getElementById(group);
                    groupCheckbox.checked = groupCheckboxes.every(cb => cb.checked);
                    const allCheckboxes = Array.from(document.querySelectorAll('input[type="checkbox"]:not(#checkAll)'))
                        .filter(cb => !['entry', 'update', 'delete'].includes(cb.id));
                    document.getElementById('checkAll').checked = allCheckboxes.every(cb => cb.checked);
                });
            },
            checkAll(event) {
                const checked = event.target.checked;
                const allCheckboxes = Array.from(document.querySelectorAll('input[type="checkbox"]:not(#checkAll)'))
                    .filter(cb => !['entry', 'update', 'delete'].includes(cb.id));
                const values = allCheckboxes.map(cb => cb.value);
                allCheckboxes.forEach(cb => {
                    cb.checked = checked;
                });
                if (checked) {
                    this.useraccess = Array.from(new Set(values));
                } else {
                    this.useraccess = [];
                }
            },
            saveData() {
                this.onProgress = true;
                let formdata = new FormData();
                formdata.append('id', "{{$user->id }}");
                formdata.append('action', JSON.stringify(this.action));
                formdata.append('access', JSON.stringify(this.useraccess));
                axios.post("/save-userAccess", formdata)
                    .then(res => {
                        toastr.success(res.data.message);
                        this.getUserAccess();
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
        },
    })
</script>
@endpush