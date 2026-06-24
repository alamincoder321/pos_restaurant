<?php

namespace App\Http\Controllers;

use App\Models\InvestTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvestTransactionController extends Controller
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
        $investment_transaction = InvestTransaction::with('adUser', 'upUser', 'deUser', 'investAccount')->where('branch_id', $this->branchId);

        if (!empty($request->dateFrom) && !empty($request->dateTo)) {
            $investment_transaction = $investment_transaction->whereBetween('date', [$request->dateFrom, $request->dateTo]);
        }

        if (!empty($request->accountId)) {
            $investment_transaction = $investment_transaction->where('invest_account_id', $request->accountId);
        }
        
        if (!empty($request->type)) {
            $investment_transaction = $investment_transaction->where('type', $request->type);
        }

        $investment_transaction = $investment_transaction->orderBy('id', 'desc')->get();

        return response()->json($investment_transaction);
    }

    public function create()
    {
        if (!checkAccess('investTransaction')) {
            return view('error.403');
        }
        return view('pages.account.investTransaction');
    }
    
    public function list()
    {
        if (!checkAccess('investTransactionList')) {
            return view('error.403');
        }
        return view('pages.account.investTransactionList');
    }

    public function store(Request $request)
    {
        try {
            $data = new InvestTransaction();
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->created_by = $this->userId;
            $data->branch_id  = $this->branchId;
            $data->ipAddress  = request()->ip();
            $data->save();

            return response()->json(['status' => true, 'message' => "Investment Transaction has created successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $data = InvestTransaction::find($request->id);
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->updated_at = Carbon::now();
            $data->updated_by = $this->userId;
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            return response()->json(['status' => true, 'message' => "Investment Transaction has updated successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = InvestTransaction::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "Investment Transaction has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
