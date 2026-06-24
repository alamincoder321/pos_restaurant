<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleReturnRequest;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\SaleReturnDetail;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
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
        $returns = SaleReturn::where('branch_id', $this->branchId);
        if (!empty($request->returnId)) {
            $returns = $returns->where('id', $request->returnId);
        }
        if (!empty($request->dateFrom) && !empty($request->dateTo)) {
            $returns = $returns->whereBetween('date', [$request->dateFrom, $request->dateTo]);
        }
        if (!empty($request->customerId)) {
            $returns = $returns->where('customer_id', $request->customerId);
        }
        $returns = $returns->latest()->get()->map(function ($item) {
            $item->details = DB::table('sale_return_details as prd')
                ->select('p.name', 'p.code', 'u.name as unit_name', 'c.name as category_name', 'prd.*')
                ->leftJoin('menus as p', 'p.id', '=', 'prd.menu_id')
                ->leftJoin('units as u', 'u.id', '=', 'p.unit_id')
                ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
                ->where('sale_return_id', $item->id)
                ->where('prd.status', 'a')
                ->where('prd.branch_id', $this->branchId)
                ->get();
            $customer = Customer::where('id', $item->customer_id)->where('branch_id', $this->branchId)->withTrashed()->first();
            $sale = Sale::where('id', $item->sale_id)->first();
            $item->customer_code = $customer->code ?? 'WalkIn Customer';
            $item->customer_name = $customer->name ?? $sale->customer_name;
            $item->customer_phone = $customer->phone ?? $sale->customer_phone;
            $item->customer_address = $customer->address ?? $sale->customer_address;
            return $item;
        }, $returns);

        return response()->json($returns);
    }

    public function getDetailForReturns(Request $request)
    {
        $query = DB::select("select
                            sd.*,
                            p.code,
                            p.name,
                            (select sum(mrd.quantity) from sale_return_details mrd
                            where mrd.status = 'a'
                            and mrd.sale_detail_id = sd.id
                            and mrd.branch_id = sd.branch_id
                            and mrd.menu_id = sd.menu_id) as already_return_quantity,

                            (select sum(mrd.total) from sale_return_details mrd
                            where mrd.status = 'a'
                            and mrd.sale_detail_id = sd.id
                            and mrd.branch_id = sd.branch_id
                            and mrd.menu_id = sd.menu_id) as already_return_amount

                            from sale_details sd
                            left join menus p on p.id = sd.menu_id
                            where sd.status = 'a'
                            and sd.branch_id = ?
                            and sd.sale_id = ?", [$this->branchId, $request->saleId]);

        return response()->json($query);
    }

    public function create()
    {
        if (!checkAccess('saleReturn')) {
            return view('error.403');
        }
        return view('pages.sale.saleReturn');
    }

    public function store(SaleReturnRequest $request)
    {
        try {
            DB::beginTransaction();
            $saleReturn = (object) $request->saleReturn;

            $data = array(
                'invoice'     => invoiceGenerate('Sale_Return', '', $this->branchId),
                'sale_id' => $saleReturn->sale_id,
                'customer_id' => $saleReturn->customer_id,
                'date'        => $saleReturn->date,
                'total'       => $saleReturn->total,
                'created_by'  => $this->userId,
                'ipAddress'   => request()->ip(),
                'branch_id'   => $this->branchId
            );
            $saleReturn = SaleReturn::create($data);

            $cartDetails = array();
            foreach ($request->carts as $cart) {
                $cartDetails[] = [
                    'sale_return_id' => $saleReturn->id,
                    'sale_detail_id' => $cart['sale_detail_id'],
                    'menu_id'     => $cart['menu_id'],
                    'sale_rate'      => $cart['sale_rate'],
                    'quantity'       => $cart['return_quantity'],
                    'discount'       => $cart['discount'] ?? 0,
                    'total'          => $cart['returnTotal'],
                    'created_by'     => $this->userId,
                    'ipAddress'      => request()->ip(),
                    'branch_id'      => $this->branchId,
                ];
            }
            SaleReturnDetail::insert($cartDetails);

            DB::commit();
            $msg = "Sale Return has create successfully";
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return send_error('Something went wrong', $th->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = SaleReturn::find($request->id);
            $data->deleted_by = $this->userId;
            $data->status = 'd';
            $data->ipAddress = request()->ip();
            $data->update();

            SaleReturnDetail::where('sale_return_id', $request->id)->update([
                'deleted_by' => $this->userId,
                'status' => 'd',
                'ipAddress' => request()->ip(),
                'deleted_at' => Carbon::now()
            ]);

            $data->delete();

            $msg = "Sale Return has deleted successfully";
            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Throwable $th) {
            return send_error("Something went wrong", $th->getMessage());
        }
    }

    public function saleReturnRecord()
    {
        if (!checkAccess('saleReturnRecord')) {
            return view('error.403');
        }
        return view("pages.sale.saleReturnRecord");
    }
}
