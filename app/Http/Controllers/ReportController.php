<?php

namespace App\Http\Controllers;

use App\Models\AccountHead;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
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

    public function profitLoss()
    {
        if (!checkAccess('profitLost')) {
            return view('error.403');
        }
        return view('pages.report.profitloss');
    }

    public function getOtherExpenseIncome(Request $request)
    {
        $reports = AccountHead::getOtherExpenseIncome($request);
        return response()->json($reports);
    }

    public function dailyReport()
    {
        if (!checkAccess('dailyReport')) {
            return view('error.403');
        }
        return view('pages.report.dailyreport');
    }

    public function getDailyReport(Request $request)
    {
        $clauses = "";
        if(!empty($request->userId)){
            $clauses .= " and u.id = $request->userId";
        }

        $query = DB::select("select u.id, u.code, u.name, u.username, u.email, u.phone,

                /*sale total*/
                (select ifnull(sum(sm.total), 0) from sales sm
                where sm.status = 'a'
                ".(empty($request->dateFrom) && empty($request->dateTo) ? "" : " and sm.date between '$request->dateFrom' and '$request->dateTo'")."
                and sm.created_by = u.id) as sale_total,

                /*cash sale receive*/
                (select ifnull(sum(sm.cashPaid), 0) from sales sm
                where sm.status = 'a'
                ".(empty($request->dateFrom) && empty($request->dateTo) ? "" : " and sm.date between '$request->dateFrom' and '$request->dateTo'")."
                and sm.created_by = u.id) as cash_sale_receive,

                /*customer receive total*/
                (select ifnull(sum(cpr.amount), 0) from receives cpr
                where cpr.status = 'a'
                and cpr.type = 'customer'
                and cpr.payment_method = 'cash'
                ".(empty($request->dateFrom) && empty($request->dateTo) ? "" : " and cpr.date between '$request->dateFrom' and '$request->dateTo'")."
                and cpr.created_by = u.id) as cash_customer_receive,

                /* total cash receive */
                (select cash_sale_receive + cash_customer_receive) as total_cash_receive,

                /*bank sale receive*/
                (select ifnull(sum(sm.bankPaid), 0) from sales sm
                where sm.status = 'a'
                ".(empty($request->dateFrom) && empty($request->dateTo) ? "" : " and sm.date between '$request->dateFrom' and '$request->dateTo'")."
                and sm.created_by = u.id) as bank_sale_receive,

                /*cash customer receive total*/
                (select ifnull(sum(cpr.amount), 0) from receives cpr
                where cpr.status = 'a'
                and cpr.type = 'customer'
                and cpr.payment_method = 'bank'
                ".(empty($request->dateFrom) && empty($request->dateTo) ? "" : " and cpr.date between '$request->dateFrom' and '$request->dateTo'")."
                and cpr.created_by = u.id) as bank_customer_receive,

                /* total bank receive */
                (select bank_sale_receive + bank_customer_receive) as total_bank_receive,

                /*total change amount*/
                (select ifnull(sum(sm.returnAmount), 0) from sales sm
                where sm.status = 'a'
                ".(empty($request->dateFrom) && empty($request->dateTo) ? "" : " and sm.date between '$request->dateFrom' and '$request->dateTo'")."
                and sm.created_by = u.id) as changeAmount,

                /*total return amount*/
                (select ifnull(sum(sr.total), 0) from sale_returns sr
                where sr.status = 'a'
                ".(empty($request->dateFrom) && empty($request->dateTo) ? "" : " and sr.date between '$request->dateFrom' and '$request->dateTo'")."
                and sr.created_by = u.id) as returnAmount,

                (select total_cash_receive + total_bank_receive - (changeAmount + returnAmount)) as netBalance
                
                from users u
                where u.status = 'a' $clauses");
                
        return response()->json($query);
    }

    public function openingClosingBalance(Request $request)
    {
        $cashBalance = AccountHead::getCashBalance($request, $request->date)->cashbalance;
        $bankBalance = array_reduce(Bank::getBankBalance($request, $request->date), function ($carry, $item) {
            return $carry + $item->currentbalance;
        }, 0);
        $data['balance'] = $cashBalance + $bankBalance;
        return response()->json($data);
    }

    public function daybook()
    {
        if (!checkAccess('daybook')) {
            return view('error.403');
        }
        return view('pages.report.daybook');
    }
}
