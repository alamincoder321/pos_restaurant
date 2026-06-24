<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
        $users = User::with('branch');
        if ($this->branchId != 1) {
            $users = $users->where('branch_id', $this->branchId);
        }

        $users = $users->latest()->get();

        return response()->json($users);
    }

    public function create()
    {
        if (!checkAccess('user')) {
            return view('error.403');
        }
        return view('pages.control.user.create');
    }

    public function profile()
    {
        return view('pages.control.user.profile');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'username' => [
                'required',
                Rule::unique('users')->whereNull('deleted_at'),
            ],
            'password' => 'required',
            'phone'    => 'required',
            'role'     => 'required'
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $check = User::where('username', $request->username)->withTrashed()->first();
            if (!empty($check) && $check->deleted_at != NULL) {
                $check->status = 'a';
                $check->deleted_at = NULL;
                $check->update();
            } else {
                $data = new User();
                $data->code = generateCode('User', 'U', $request->branch_id);
                $dataKey = $request->except('id', 'image');
                foreach ($dataKey as $key => $value) {
                    $data[$key] = $value;
                }
                if ($request->hasFile('image')) {
                    $data->image = imageUpload($request, 'image', 'uploads/user', $data->code . '_' . $this->branchId);
                }
                $data->password = Hash::make($request->password);
                // $data->branch_id = $this->branchId;
                $data->ipAddress = request()->ip();
                $data->save();
            }

            return response()->json(['status' => true, 'message' => "User has created successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'username' => [
                'required',
                Rule::unique('users')->ignore($request->id)->whereNull('deleted_at'),
            ],
            'phone'    => 'required',
            'role'     => 'required',
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
                $data->image = imageUpload($request, 'image', 'uploads/user', $data->code . '_' . $this->branchId);
            }
            if (!empty($request->password)) {
                $data->password = Hash::make($request->password);
            }
            // $data->branch_id = $this->branchId;
            $data->ipAddress = request()->ip();
            $data->updated_at = Carbon::now();
            $data->update();

            return response()->json(['status' => true, 'message' => "User has updated successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = User::find($request->id);
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "User has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }

    public function userAccess($id)
    {
        if (!checkAccess('userAccess')) {
            return view('error.403');
        }
        $data['user'] = User::withTrashed()->find($id);
        return view('pages.control.user.userAccess', $data);
    }

    public function getUserAccess(Request $request)
    {
        $userAccess = UserAccess::select('access')->where('user_id', $request->id)->first();
        if (empty($userAccess)) {
            $userAccess = [];
        } else {
            $userAccess = json_decode($userAccess->access, true);
        }
        return response()->json(['access' => $userAccess, 'action' => User::where('id', $request->id)->value('action')]);
    }

    public function saveUserAccess(Request $request)
    {
        try {
            $userAccess = UserAccess::where('user_id', $request->id)->first();
            if (empty($userAccess)) {
                $userAccess = new UserAccess();
                $userAccess->user_id = $request->id;
            }
            $userAccess->access = json_encode($request->access);
            $userAccess->ipAddress = request()->ip();
            $userAccess->created_by = $this->userId;
            $userAccess->branch_id = $this->branchId;
            $userAccess->save();

            // update user action button
            User::where('id', $request->id)->update([
                'action' => count(json_decode($request->action)) > 0 ? implode(',', json_decode($request->action)) : NULL
            ]);

            return response()->json(['status' => true, 'message' => "User access has been created successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
