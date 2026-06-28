<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
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
        $bank = Bank::with('adUser', 'upUser', 'deUser')
            ->latest()->get()->map(function ($item) {
                $item->display_name = $item->name . ' - ' . $item->number . ' - ' . $item->bank_name;
                return $item;
            });

        return response()->json($bank);
    }

    public function create()
    {
        if (!checkAccess('bank')) {
            return view('error.403');
        }
        return view('pages.account.bank');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required',
            'type' => 'required',
            'balance' => 'required'
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        $check = Bank::where('bank_name', $request->bank_name)->where('number', $request->number)->where('branch_id', $this->branchId)->first();
        if (!empty($check)) {
            return send_error("Bank already exists", null, 422);
        }
        try {
            $check = Bank::where('name', $request->name)->withTrashed()->first();
            if (!empty($check) && $check->deleted_at != NULL) {
                $check->status = 'a';
                $check->deleted_by = NULL;
                $check->deleted_at = NULL;
                $check->update();
            } else {
                $data = new Bank();
                $dataKey = $request->except('id');
                foreach ($dataKey as $key => $value) {
                    $data[$key] = $value;
                }
                $data->created_by = $this->userId;
                $data->branch_id  = $this->branchId;
                $data->ipAddress  = request()->ip();
                $data->save();
            }

            return response()->json(['status' => true, 'message' => "Bank has created successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'number' => 'required',
            'type' => 'required',
            'balance' => 'required'
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        $check = Bank::where('id', '!=', $request->id)->where('bank_name', $request->bank_name)->where('number', $request->number)->where('branch_id', $this->branchId)->first();
        if (!empty($check)) {
            return send_error("Bank already exists", null, 422);
        }
        try {
            $data = Bank::find($request->id);
            $dataKey = $request->except('id');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            $data->updated_at = Carbon::now();
            $data->updated_by = $this->userId;
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            return response()->json(['status' => true, 'message' => "Bank has updated successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = Bank::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "Bank has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }

    public function getBankBalance(Request $request)
    {
        $dues = Bank::getBankBalance($request);

        return response()->json($dues);
    }

    public function bankLedger()
    {
        if (!checkAccess('bankLedger')) {
            return view('error.403');
        }
        return view('pages.report.bankLedger');
    }

    public function getBankLedger(Request $request)
    {
        $branchId = $this->branchId;
        $query = "
                select
                'a' as sequence,
                bt.id,
                bt.date,
                bt.created_at,
                concat('Bank Deposit - ', bt.invoice) as description,
                0 as withdraw,
                bt.amount as deposit,
                0 as balance
                from bank_transactions bt
                where bt.status = 'a'
                and bt.type = 'credit'
                " . (empty($request->bankId) ? "" : " and bt.bank_id = '$request->bankId'") . "
                " . ($branchId == null ? "" : " and bt.branch_id = '$branchId'") . "

                UNION
                select
                'b' as sequence,
                sb.id,
                sm.date,
                sm.created_at,
                concat('Sale Invoice - ', sm.invoice) as description,
                0 as withdraw,
                sb.amount as deposit,
                0 as balance
                from sale_banks sb
                join sales sm on sm.id = sb.sale_id
                where sb.status = 'a'
                and sm.order_status = 'completed'
                " . (empty($request->bankId) ? "" : " and sb.bank_id = '$request->bankId'") . "
                " . ($branchId == null ? "" : " and sb.branch_id = '$branchId'") . "

                UNION
                select
                'c' as sequence,
                cpr.id,
                cpr.date,
                cpr.created_at,
                concat('Customer Payment - ', cpr.invoice) as description,
                0 as withdraw,
                cpr.amount as deposit,
                0 as balance
                from receives cpr
                where cpr.status = 'a'
                and cpr.type = 'customer'
                and cpr.payment_method = 'bank'
                " . (empty($request->bankId) ? "" : " and cpr.bank_id = '$request->bankId'") . "
                " . ($branchId == null ? "" : " and cpr.branch_id = '$branchId'") . "

                UNION
                select
                'd' as sequence,
                spr.id,
                spr.date,
                spr.created_at,
                concat('Supplier Receive - ', spr.invoice) as description,
                0 as withdraw,
                spr.amount as deposit,
                0 as balance
                from receives spr
                where spr.status = 'a'
                and spr.type = 'supplier'
                and spr.payment_method = 'bank'
                " . (empty($request->bankId) ? "" : " and spr.bank_id = '$request->bankId'") . "
                " . ($branchId == null ? "" : " and spr.branch_id = '$branchId'") . "

                UNION
                select
                'e' as sequence,
                bt.id,
                bt.date,
                bt.created_at,
                concat('Bank Withdraw - ', bt.invoice) as description,
                bt.amount as withdraw,
                0 as deposit,
                0 as balance
                from bank_transactions bt
                where bt.status = 'a'
                and bt.type = 'debit'
                " . (empty($request->bankId) ? "" : " and bt.bank_id = '$request->bankId'") . "
                " . ($branchId == null ? "" : " and bt.branch_id = '$branchId'") . "
                
                UNION
                select
                'f' as sequence,
                spp.id,
                spp.date,
                spp.created_at,
                concat('Supplier Payment - ', spp.invoice) as description,
                spp.amount as withdraw,
                0 as deposit,
                0 as balance
                from payments spp
                where spp.status = 'a'
                and spp.type = 'supplier'
                and spp.payment_method = 'bank'
                " . (empty($request->bankId) ? "" : " and spp.bank_id = '$request->bankId'") . "
                " . ($branchId == null ? "" : " and spp.branch_id = '$branchId'") . "

                UNION
                select
                'g' as sequence,
                cpp.id,
                cpp.date,
                cpp.created_at,
                concat('Customer Payment - ', cpp.invoice) as description,
                cpp.amount as withdraw,
                0 as deposit,
                0 as balance
                from payments cpp
                where cpp.status = 'a'
                and cpp.type = 'customer'
                and cpp.payment_method = 'bank'
                " . (empty($request->bankId) ? "" : " and cpp.bank_id = '$request->bankId'") . "
                " . ($branchId == null ? "" : " and cpp.branch_id = '$branchId'") . "
                
                order by created_at asc";

        $ledgers = DB::select($query);

        $supplier = Bank::select('balance')->where('id', $request->bankId)
            ->where('branch_id', $branchId)
            ->first();
        $previousBalance = empty($supplier) ? 0 : $supplier->balance;

        $ledgers = collect($ledgers)->map(function ($ledger, $key) use ($previousBalance, $ledgers) {
            $lastBalance = $key == 0 ? $previousBalance : $ledgers[$key - 1]->balance;
            $ledger->balance = ($lastBalance + $ledger->deposit) - $ledger->withdraw;
            return $ledger;
        });

        $previousLedger = collect($ledgers)->filter(function ($ledger) use ($request) {
            return $ledger->date < $request->dateFrom;
        });
        $previousBalance = count($previousLedger) > 0 ? $previousLedger[count($previousLedger) - 1]->balance : $previousBalance;

        if (!empty($request->dateFrom) && !empty($request->dateTo)) {
            $ledgers = $ledgers->filter(function ($ledger) use ($request) {
                return $ledger->date >= $request->dateFrom && $ledger->date <= $request->dateTo;
            })->values();
        }


        return response()->json(['previousBalance' => $previousBalance, 'ledgers' => $ledgers]);
    }
}
