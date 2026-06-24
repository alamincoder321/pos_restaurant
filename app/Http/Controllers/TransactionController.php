<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
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
        $transactions = Transaction::with('adUser', 'upUser', 'account')->where('branch_id', $this->branchId);
        if (!empty($request->transactionId)) {
            $transactions->where('id', $request->transactionId);
        }
        if (!empty($request->accountId)) {
            $transactions->where('account_id', $request->accountId);
        }
        if (!empty($request->type)) {
            $transactions->where('type', $request->type);
        }
        if (!empty($request->dateFrom) && !empty($request->dateTo)) {
            $transactions->whereBetween('date', [$request->dateFrom, $request->dateTo]);
        }
        $transactions = $transactions->latest()->get();
        return response()->json($transactions);
    }

    public function expense()
    {
        if (!checkAccess('expense')) {
            return view('error.403');
        }
        return view('pages.account.expense');
    }

    public function income()
    {
        if (!checkAccess('income')) {
            return view('error.403');
        }
        return view('pages.account.income');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice' => 'required',
            'type' => 'required',
            'date' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $invoice = Transaction::where('invoice', $request->invoice)->first();
            if (empty($invoice)) {
                $invoice = transactionInvoice('Transaction', 'T', $this->branchId, $request->type);
            }
            $data = new Transaction();
            $data->invoice = $invoice;
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->created_by = $this->userId;
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->save();

            if ($request->type == 'expense') {
                $msg = "Expense has created successfully";
            } else {
                $msg = "Income has created successfully";
            }
            return response()->json(['status' => true, 'message' => $msg, 'invoice' => transactionInvoice('Transaction', 'T', $this->branchId, $request->type)]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice' => 'required',
            'type' => 'required',
            'date' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $data = Transaction::find($request->id);
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->updated_by = $this->userId;
            $data->updated_at = Carbon::now();
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            if ($request->type == 'expense') {
                $msg = "Expense has updated successfully";
            } else {
                $msg = "Income has updated successfully";
            }
            return response()->json(['status' => true, 'message' => $msg, 'invoice' => transactionInvoice('Transaction', 'T', $this->branchId, $request->type)]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = Transaction::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            if ($request->type == 'expense') {
                $msg = "Expense has deleted successfully";
            } else {
                $msg = "Income has deleted successfully";
            }
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
