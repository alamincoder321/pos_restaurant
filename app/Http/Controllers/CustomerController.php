<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
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
        $customers = Customer::with('adUser', 'upUser', 'area')->where('branch_id', $this->branchId);
        if (!empty($request->customerId)) {
            $customers = $customers->where('id', $request->customerId);
        }
        if (!empty($request->areaId)) {
            $customers = $customers->where('area_id', $request->areaId);
        }
        if (!empty($request->search)) {
            $customers = $customers->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }
        
        if (!empty($request->forSearch)) {
            $customers = $customers->limit(50)->latest()->get();
        } else {
            if (!empty($request->per_page)) {
                $customers = $customers->latest()->paginate($request->per_page ?? 20);
            } else {
                $customers = $customers->latest()->get();
            }
        }

        if (empty($request->per_page)) {
            $customers = $customers->map(function ($item) {
                $item->display_name = $item->name . ' - ' . $item->phone . ' - ' . $item->code;
                return $item;
            });
        }

        return response()->json($customers);
    }

    public function create()
    {
        if (!checkAccess('customer')) {
            return view('error.403');
        }
        return view('pages.control.customer.create');
    }

    public function customerList()
    {
        if (!checkAccess('customerList')) {
            return view('error.403');
        }
        return view('pages.control.customer.index');
    }


    public function store(Request $request)
    {
        $branchId = $this->branchId;
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'phone' => [
                'required',
                Rule::unique('customers')
                    ->where(function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereNull('deleted_at'),
            ],
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $check = Customer::where('phone', $request->phone)->withTrashed()->first();
            if (!empty($check) && $check->deleted_at != NULL) {
                $check->status = 'a';
                $check->deleted_at = NULL;
                $check->update();
            } else {
                $data = new Customer();
                $data->code = generateCode('Customer', 'CI');
                $dataKey = $request->except('id', 'image');
                foreach ($dataKey as $key => $value) {
                    $data[$key] = $value;
                }
                if ($request->hasFile('image')) {
                    $data->image = imageUpload($request, 'image', 'uploads/customer', $data->code . '_' . $this->branchId);
                }
                $data->created_by = $this->userId;
                $data->ipAddress = request()->ip();
                $data->branch_id = $this->branchId;
                $data->save();
            }

            return response()->json(['status' => true, 'message' => "Customer has created successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function update(Request $request)
    {
        $branchId = $this->branchId;
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'phone' => [
                'required',
                Rule::unique('customers')
                    ->ignore($request->id)
                    ->where(function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->whereNull('deleted_at'),
            ],
        ]);
        if ($validator->fails()) return send_error("Validation Error", $validator->errors(), 422);
        try {
            $data = Customer::find($request->id);
            $dataKey = $request->except('id', 'image');
            foreach ($dataKey as $key => $value) {
                $data[$key] = $value;
            }
            if ($request->hasFile('image')) {
                if (File::exists($data->image)) {
                    File::delete($data->image);
                }
                $data->image = imageUpload($request, 'image', 'uploads/customer', $data->code . '_' . $this->branchId);
            }
            $data->updated_by = $this->userId;
            $data->updated_at = Carbon::now();
            $data->ipAddress = request()->ip();
            $data->branch_id = $this->branchId;
            $data->update();

            return response()->json(['status' => true, 'message' => "Customer has updated successfully"]);
        } catch (\Throwable $th) {
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = Customer::find($request->id);
            if (File::exists($data->image)) {
                File::delete($data->image);
            }
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            $data->delete();
            return response()->json(['status' => true, 'message' => "Customer has deleted successfully"]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }

    // customer due
    public function customerDue()
    {
        if (!checkAccess('customerDue')) {
            return view('error.403');
        }
        return view('pages.report.customerDue');
    }

    public function getCustomerDue(Request $request)
    {
        $date = $request->date ? $request->date : null;
        $dues = Customer::customerDue($request, $date);
        return response()->json($dues);
    }

    public function customerLedger()
    {
        if (!checkAccess('customerLedger')) {
            return view('error.403');
        }
        return view('pages.report.customerLedger');
    }

    public function getCustomerLedger(Request $request)
    {
        $branchId = $this->branchId;
        $query = "select
                'a' as sequence,
                sm.id,
                sm.date,
                sm.created_at,
                concat('Sale Invoice - ', sm.invoice, '(Customer: ', ifnull(c.name, sm.customer_name), ')') as description,
                sm.total as bill,
                sm.paid as paid,
                (sm.total - sm.paid) as due,
                0 as cash_payment,
                0 as cash_receive,
                0 as return_amount,
                0 as balance
                from sales sm
                left join customers c on c.id = sm.customer_id
                where sm.status = 'a'
                " . (empty($request->customerId) ? "" : " and sm.customer_id = '$request->customerId'") . "
                " . ($branchId == null ? "" : " and sm.branch_id = '$branchId'") . "

                UNION
                select
                'b' as sequence,
                sr.id,
                sr.date,
                sr.created_at,
                concat('Sale Return Invoice - ', sr.invoice) as description,
                0 as bill,
                0 as paid,
                0 as due,
                0 as cash_payment,
                0 as cash_receive,
                sr.total as return_amount,
                0 as balance
                from sale_returns sr
                left join customers c on c.id = sr.customer_id
                where sr.status = 'a'
                " . (empty($request->customerId) ? "" : " and sr.customer_id = '$request->customerId'") . "
                " . ($branchId == null ? "" : " and sr.branch_id = '$branchId'") . "

                UNION
                select
                'c' as sequence,
                cp.id,
                cp.date,
                cp.created_at,
                concat('Customer Payment - ', cp.invoice) as description,
                0 as bill,
                0 as paid,
                0 as due,
                cp.amount as cash_payment,
                0 as cash_receive,
                0 as return_amount,
                0 as balance
                from payments cp
                left join customers c on c.id = cp.customer_id
                where cp.status = 'a'
                and cp.type = 'customer'
                " . (empty($request->customerId) ? "" : " and cp.customer_id = '$request->customerId'") . "
                " . ($branchId == null ? "" : " and cp.branch_id = '$branchId'") . "

                UNION
                select
                'd' as sequence,
                cp.id,
                cp.date,
                cp.created_at,
                concat('Customer Receive - ', cp.invoice) as description,
                0 as bill,
                0 as paid,
                0 as due,
                0 as cash_payment,
                cp.amount as cash_receive,
                0 as return_amount,
                0 as balance
                from receives cp
                left join customers c on c.id = cp.customer_id
                where cp.status = 'a'
                and cp.type = 'customer'
                " . (empty($request->customerId) ? "" : " and cp.customer_id = '$request->customerId'") . "
                " . ($branchId == null ? "" : " and cp.branch_id = '$branchId'") . "
                
                order by created_at asc";

        $ledgers = DB::select($query);

        $customer = Customer::select('previous_due')->where('id', $request->customerId)
            ->where('branch_id', $branchId)
            ->first();
        $previousBalance = empty($customer) ? 0 : $customer->previous_due;

        $ledgers = collect($ledgers)->map(function ($ledger, $key) use ($previousBalance, $ledgers) {
            $lastBalance = $key == 0 ? $previousBalance : $ledgers[$key - 1]->balance;
            $ledger->balance = ($lastBalance + $ledger->bill + $ledger->cash_payment) - ($ledger->paid + $ledger->cash_receive + $ledger->return_amount);
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
