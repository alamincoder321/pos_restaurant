<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DesignationtController extends Controller
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
        $designation = Designation::with('adUser', 'upUser', 'deUser')
            ->latest()
            ->get();
        return response()->json($designation);
    }

    public function create()
    {
        if (!checkAccess('designation')) {
            return view('error.403');
        }
        return view('pages.hr.designation');
    }

    public function store(Request $request)
    {
        $branchId = $this->branchId;
        $validator = Validator::make($request->all(), [
            'name'     => [
                'required',
                Rule::unique('Designations')
                    ->where(function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereNull('deleted_at'),
            ],
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $check = Designation::where('name', $request->name)->withTrashed()->first();
            if (!empty($check) && $check->deleted_at != NULL) {
                $check->status = 'a';
                $check->deleted_by = NULL;
                $check->deleted_at = NULL;
                $check->update();
            } else {
                $data = new Designation();
                $dataKey = $request->except('id');
                foreach ($dataKey as $key => $value) {
                    $data[$key] = $value;
                }
                $data->created_by = $this->userId;
                $data->branch_id  = $this->branchId;
                $data->ipAddress  = request()->ip();
                $data->save();
            }

            return response()->json(['status' => true, 'message' => "Designation has created successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        $branchId = $this->branchId;
        $validator = Validator::make($request->all(), [
            'name'     => [
                'required',
                Rule::unique('Designations')
                    ->ignore($request->id)
                    ->where(function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereNull('deleted_at'),
            ],
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $data = Designation::find($request->id);
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->updated_at = Carbon::now();
            $data->updated_by = $this->userId;
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            return response()->json(['status' => true, 'message' => "Designation has updated successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = Designation::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "Designation has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
