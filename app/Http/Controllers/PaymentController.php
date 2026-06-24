<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
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
        $transactions = Payment::with('adUser', 'upUser', 'bank', 'supplier', 'customer')->where('branch_id', $this->branchId);
        if (!empty($request->transactionId)) {
            $transactions->where('id', $request->transactionId);
        }
        if (!empty($request->customerId)) {
            $transactions->where('customer_id', $request->customerId);
        }
        if (!empty($request->supplierId)) {
            $transactions->where('supplier_id', $request->supplierId);
        }
        if (!empty($request->bankId)) {
            $transactions->where('bank_id', $request->bankId);
        }
        if (!empty($request->paymentMethod)) {
             $transactions->where('payment_method', $request->paymentMethod);
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

    public function create()
    {
        if (!checkAccess('payment')) {
            return view('error.403');
        }
        return view('pages.account.payment');
    }


    public function store(PaymentRequest $request)
    {
        if (!$request->validated()) return send_error("Validation Error", $request->validated(), 422);
        try {
            $invoice = Payment::where('invoice', $request->invoice)->first();
            if (empty($invoice)) {
                $invoice = transactionInvoice('Payment', 'P', $this->branchId, $request->type);
            }
            $data = new Payment();
            $data->invoice = $invoice;
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->created_by = $this->userId;
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->save();

            if ($request->type == 'customer') {
                $msg = "Customer payment has created successfully";
            } else {
                $msg = "Supplier payment has created successfully";
            }
            return response()->json(['status' => true, 'message' => $msg, 'invoice' => transactionInvoice('Payment', 'P', $this->branchId, $request->type)]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(PaymentRequest $request)
    {
        if (!$request->validated()) return send_error("Validation Error", $request->validated(), 422);
        try {
            $data = Payment::find($request->id);
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            if ($request->payment_method == 'cash') {
                $data->bank_id = NULL;
            }
            $data->updated_by = $this->userId;
            $data->updated_at = Carbon::now();
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            if ($request->type == 'customer') {
                $msg = "Customer payment has update successfully";
            } else {
                $msg = "Supplier payment has update successfully";
            }
            return response()->json(['status' => true, 'message' => $msg, 'invoice' => transactionInvoice('Payment', 'P', $this->branchId, $request->type)]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = Payment::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            if ($request->type == 'customer') {
                $msg = "Customer payment has deleted successfully";
            } else {
                $msg = "Supplier payment has deleted successfully";
            }
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }
}
