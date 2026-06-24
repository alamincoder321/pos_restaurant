<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
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
        $employees = User::with('adUser', 'upUser', 'department', 'designation')
            ->where('role', 'employee')
            ->where('branch_id', $this->branchId);
        if (!empty($request->supplierId)) {
            $employees = $employees->where('id', $request->supplierId);
        }
        if (!empty($request->departmentId)) {
            $employees = $employees->where('department_id', $request->departmentId);
        }
        if (!empty($request->designationId)) {
            $employees = $employees->where('designation_id', $request->designationId);
        }
        if (!empty($request->status)) {
            $employees = $employees->where('status', $request->status);
        }
        $employees = $employees->latest()->get()->map(function ($item){
            $item->display_name = $item->name . ' -' . $item->phone . ' - ' . $item->emp_code;
            return $item;
        });
        return response()->json($employees);
    }

    public function create()
    {
        if (!checkAccess('employee')) {
            return view('error.403');
        }
        return view('pages.hr.employee.create');
    }

    public function employeeList()
    {
        if (!checkAccess('employeeList')) {
            return view('error.403');
        }
        return view('pages.hr.employee.index');
    }


    public function store(Request $request)
    {
        $branchId = $this->branchId;
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('users')
                    ->where(function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereNull('deleted_at'),
            ],
            'username' => [
                'required',
                Rule::unique('users')
                    ->whereNull('deleted_at'),
            ],
            'department_id' => 'required',
            'designation_id' => 'required',
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $check = User::where('name', $request->name)->where('role', 'employee')->withTrashed()->first();
            if (!empty($check) && $check->deleted_at != NULL) {
                $check->status = 'a';
                $check->deleted_at = NULL;
                $check->update();
            } else {
                $data = new User();
                $data->code = generateCode('User', 'U');
                $data->emp_code = generateEmpCode('User', 'E');
                $dataKey = $request->except('id', 'image', 'password');
                foreach ($dataKey as $key => $value) {
                    $data[$key] = $value;
                }
                if ($request->hasFile('image')) {
                    $data->image = imageUpload($request, 'image', 'uploads/employee', $data->code . '_' . $this->branchId);
                }
                $data->role       = 'employee';
                $data->password = Hash::make($request->password);
                $data->created_by = $this->userId;
                $data->ipAddress  = request()->ip();
                $data->branch_id  = $this->branchId;
                $data->save();
            }

            return response()->json(['status' => true, 'message' => "Employee has created successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        $branchId = $this->branchId;
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                Rule::unique('users')
                    ->ignore($request->id)
                    ->where(function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereNull('deleted_at'),
            ],
            'username' => [
                'required',
                Rule::unique('users')
                    ->ignore($request->id)
                    ->whereNull('deleted_at'),
            ],
            'department_id' => 'required',
            'designation_id' => 'required',
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $data = User::find($request->id);
            $dataKey = $request->except('id', 'image', 'password');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            if ($request->hasFile('image')) {
                if (File::exists($data->image)) {
                    File::delete($data->image);
                }
                $data->image = imageUpload($request, 'image', 'uploads/employee', $data->code . '_' . $this->branchId);
            }
            if (!empty($request->password)) {
                $data->password = Hash::make($request->password);
            }
            $data->updated_by = $this->userId;
            $data->updated_at = Carbon::now();
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            return response()->json(['status' => true, 'message' => "Employee has updated successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = User::find($request->id);
            if (File::exists($data->image)) {
                File::delete($data->image);
            }
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "Employee has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
