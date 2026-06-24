<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class InvestAccount extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function adUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select('id', 'name', 'username')->withTrashed();
    }
    public function upUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id')->select('id', 'name', 'username')->withTrashed();
    }
    public function deUser()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id')->select('id', 'name', 'username')->withTrashed();
    }


    // cash balance
    public static function getInvestBalance($request, $accountId = null, $date = null)
    {
        $request = (object)$request;
        $branchId = !empty($request->branchId) ? $request->branchId : session('branch')->id;

        $query = "select
                /* Received */
                
                (select ifnull(sum(it.amount), 0) from invest_transactions it
                where it.status = 'a'
                and it.type = 'deposit'
                " . ($accountId == null ? "" : " and it.invest_account_id = '$accountId'") . "
                " . ($date == null ? "" : " and it.date <= '$date'") . "
                " . ($branchId == null ? "" : " and it.branch_id = '$branchId'") . ") as investment_transaction_deposit,
                
                /* Payment */

                (select ifnull(sum(it.amount), 0) from invest_transactions it
                where it.status = 'a'
                and it.type = 'withdraw'
                " . ($accountId == null ? "" : " and it.invest_account_id = '$accountId'") . "
                " . ($date == null ? "" : " and it.date <= '$date'") . "
                " . ($branchId == null ? "" : " and it.branch_id = '$branchId'") . ") as investment_transaction_withdraw,
                
                (select investment_transaction_deposit - investment_transaction_withdraw) as balance";

        return $accountId == null ? DB::select($query) : DB::select($query)[0];
    }
}
