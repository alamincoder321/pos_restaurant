<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
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
        $branch = Brand::with('adUser', 'upUser', 'deUser')->latest()->get();
        return response()->json($branch);
    }

    public function create()
    {
        if (!checkAccess('brand')) {
            return view('error.403');
        }
        return view('pages.control.brand');
    }

    public function store(Request $request)
    {
        $branchId = $this->branchId;
        $validator = Validator::make($request->all(), [
            'name'     => [
                'required',
                Rule::unique('brands')
                ->where(function ($query) use ($branchId) {
                    $query->where('branch_id', $branchId);
                })
                ->whereNull('deleted_at'),
            ],
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $check = Brand::where('name', $request->name)->withTrashed()->first();
            if (!empty($check) && $check->deleted_at != NULL) {
                $check->status = 'a';
                $check->deleted_by = NULL;
                $check->deleted_at = NULL;
                $check->update();
            } else {
                $data = new Brand();
                $dataKey = $request->except('id');
                foreach ($dataKey as $key => $value) {
                    $data[$key] = $value;
                }
                $data->created_by = $this->userId;
                $data->branch_id  = $this->branchId;
                $data->ipAddress  = request()->ip();
                $data->save();
            }

            return response()->json(['status' => true, 'message' => "Brand has created successfully"]);
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
                Rule::unique('brands')
                    ->ignore($request->id)
                    ->where(function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereNull('deleted_at'),
            ],
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $data = Brand::find($request->id);
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->updated_at = Carbon::now();
            $data->updated_by = $this->userId;
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            return response()->json(['status' => true, 'message' => "Brand has updated successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = Brand::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "Brand has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
