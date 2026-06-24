<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SalaryDetail;
use App\Models\SalaryMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    protected $userId;
    protected $branchId;
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $this->branchId = $request->session()->get('branch')->id;
            $this->userId = auth()->user()->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $salaries = SalaryMaster::with('detail', 'adUser', 'upUser')->where('branch_id', $this->branchId);
        if (!empty($request->salaryId)) {
            $salaries = $salaries->where('id', $request->salaryId);
        }
        if (!empty($request->month)) {
            $salaries = $salaries->where('month', $request->month);
        }
        $salaries = $salaries->latest()->get()->map(function ($salary) use ($request) {
            $detail = $salary->detail;
            if (!empty($request->departmentId)) {
                $detail = $salary->detail
                    ->filter(function ($item) use ($request) {
                        return $item->employee->department_id == $request->departmentId;
                    });
            }
            if (!empty($request->designationId)) {
                $detail = $salary->detail
                    ->filter(function ($item) use ($request) {
                        return $item->employee->designation_id == $request->designationId;
                    });
            }
            unset($salary->detail);
            $salary->detail = $detail;
            return $salary;
        });

        return response()->json($salaries);
    }

    public function create()
    {
        if (!checkAccess('salary')) {
            return view('error.403');
        }
        return view('pages.hr.salary');
    }

    public function salaryList()
    {
        if (!checkAccess('salaryList')) {
            return view('error.403');
        }
        return view('pages.hr.salaryList');
    }

    public function checkSalary(Request $request)
    {
        $salaries = User::with('department', 'designation')
            ->where('role', 'employee')
            ->where('branch_id', $this->branchId);
        if (!empty($request->employeeId)) {
            $salaries = $salaries->where('id', $request->employeeId);
        }
        if (!empty($request->departmentId)) {
            $salaries = $salaries->where('department_id', $request->departmentId);
        }
        if (!empty($request->designationId)) {
            $salaries = $salaries->where('designation_id', $request->designationId);
        }
        $salaries = $salaries->latest()->get();
        $monthcheck = SalaryMaster::where('month', $request->month)->first();
        if (!empty($monthcheck)) {
            $salaries = SalaryDetail::with('employee')->where("salary_id", $monthcheck->id)->get()->map(function ($item) {
                return [
                    'employee_id'  => $item->employee->id,
                    'emp_code'     => $item->employee->emp_code,
                    'name'         => $item->employee->name,
                    'designation'  => $item->employee->designation,
                    'department'   => $item->employee->department,
                    'basic_salary' => $item->basic_salary,
                    'house_rent'   => $item->house_rent,
                    'medical_fee'  => $item->medical_fee,
                    'other_fee'    => $item->other_fee,
                    'gross_salary' => $item->gross_salary,
                    'ot_amount'    => $item->ot_amount,
                    'deduction'    => $item->deduction,
                    'advance'      => $item->advance,
                    'total'        => $item->total,
                    'paid'         => $item->paid,
                    'due'          => $item->due,
                    'note'         => $item->note,
                    'check'        => 'true'
                ];
            });
        } else {
            $salaries = $salaries->map(function ($item) {
                return [
                    'employee_id'  => $item->id,
                    'emp_code'     => $item->emp_code,
                    'name'         => $item->name,
                    'designation'  => $item->designation,
                    'department'   => $item->department,
                    'basic_salary' => $item->basic_salary,
                    'house_rent'   => $item->house_rent,
                    'medical_fee'  => $item->medical_fee,
                    'other_fee'    => $item->other_fee,
                    'gross_salary' => $item->gross_salary,
                    'ot_amount'    => 0,
                    'deduction'    => 0,
                    'advance'      => 0,
                    'total'        => $item->gross_salary,
                    'paid'         => $item->gross_salary,
                    'due'          => 0,
                    'note'         => '',
                    'check'        => 'false'
                ];
            });
        }
        $data['salary_id'] = !empty($monthcheck) ? $monthcheck->id : '';
        $data['salaries']  = $salaries;
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // if (!$request->validated()) return send_error("Validation Error", $request->validated(), 422);
        try {
            DB::beginTransaction();
            $salary = json_decode($request->only('salary')['salary'], true);
            unset($salary['note']);
            unset($salary['salary_id']);
            $data = new SalaryMaster();
            $data->invoice = generateCode('Salary_Master', 'ET', $this->branchId);
            foreach ($salary as $key => $value) {
                $data[$key] = $value;
            }
            $data->date       = date('Y-m-d');
            $data->created_by = $this->userId;
            $data->ipAddress  = request()->ip();
            $data->branch_id  = $this->branchId;
            $data->save();

            $carts = json_decode($request->only('carts')['carts'], true);
            foreach ($carts as $item) {
                $detail = new SalaryDetail();
                unset($item['emp_code']);
                unset($item['name']);
                unset($item['department']);
                unset($item['designation']);
                unset($item['check']);
                foreach ($item as $key => $value) {
                    $detail[$key] = $value;
                }
                $detail->salary_id = $data->id;
                $detail->created_by = $this->userId;
                $detail->ipAddress = request()->ip();
                $detail->branch_id = $this->branchId;
                $detail->save();
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => "Salary has created successfully"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        // if (!$request->validated()) return send_error("Validation Error", $request->validated(), 422);
        try {
            DB::beginTransaction();
            $salary = json_decode($request->only('salary')['salary'], true);
            $salaryId = $salary['salary_id'];
            unset($salary['note']);
            unset($salary['salary_id']);
            $data = SalaryMaster::find($salaryId);
            foreach ($salary as $key => $value) {
                $data[$key] = $value;
            }
            $data->updated_by = $this->userId;
            $data->updated_at = Carbon::now();
            $data->ipAddress  = request()->ip();
            $data->branch_id  = $this->branchId;
            $data->update();

            $carts = json_decode($request->only('carts')['carts'], true);
            SalaryDetail::where('salary_id', $salaryId)->forceDelete();
            foreach ($carts as $item) {
                $detail = new SalaryDetail();
                unset($item['emp_code']);
                unset($item['name']);
                unset($item['department']);
                unset($item['designation']);
                unset($item['check']);
                foreach ($item as $key => $value) {
                    $detail[$key] = $value;
                }
                $detail->salary_id  = $salaryId;
                $detail->created_by = $this->userId;
                $data->updated_by   = $this->userId;
                $data->updated_at   = Carbon::now();
                $detail->ipAddress  = request()->ip();
                $detail->branch_id  = $this->branchId;
                $detail->save();
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => "Salary has updated successfully"]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = SalaryMaster::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "Salary has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
