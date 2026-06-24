<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
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

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id')->select('id', 'name')->withTrashed();
    }

    // customer due
    public static function customerDue($request, $date = null)
    {
        $branchId = session('branch')->id;
        $request = (object)$request;
        $clauses = "";
        if (!empty($request->customerId)) {
            $clauses .= " and c.id = '$request->customerId'";
        }
        if (!empty($request->areaId)) {
            $clauses .= " and c.area_id = '$request->areaId'";
        }

        if (!empty($request->customer_type)) {
            $clauses .= " and c.type = '$request->customer_type'";
        }

        $query = "select
                    c.id, c.code, c.name, c.owner, c.phone, c.address,
                    (select ifnull(sum(sm.total), 0) from sales sm
                    where sm.status = 'a'
                    " . ($date == null ? "" : " and sm.date <= '$date'") . "
                    " . ($branchId == null ? "" : " and sm.branch_id = '$branchId'") . "
                    and sm.customer_id = c.id) as sale_total,
                    
                    (select ifnull(sum(sm.paid), 0) from sales sm
                    where sm.status = 'a'
                    " . ($date == null ? "" : " and sm.date <= '$date'") . "
                    " . ($branchId == null ? "" : " and sm.branch_id = '$branchId'") . "
                    and sm.customer_id = c.id) as sale_paid,
                    
                    (select ifnull(sum(cr.amount), 0) from receives cr
                    where cr.status = 'a'
                    and cr.type = 'customer'
                    " . ($date == null ? "" : " and cr.date <= '$date'") . "
                    " . ($branchId == null ? "" : " and cr.branch_id = '$branchId'") . "
                    and cr.customer_id = c.id) as received_amount,
                    
                    (select ifnull(sum(cp.amount), 0) from payments cp
                    where cp.status = 'a'
                    and cp.type = 'customer'
                    " . ($date == null ? "" : " and cp.date <= '$date'") . "
                    " . ($branchId == null ? "" : " and cp.branch_id = '$branchId'") . "
                    and cp.customer_id = c.id) as payment_amount,
                    
                    (select ifnull(sum(sr.total), 0) from sale_returns sr
                    where sr.status = 'a'
                    " . ($date == null ? "" : " and sr.date <= '$date'") . "
                    " . ($branchId == null ? "" : " and sr.branch_id = '$branchId'") . "
                    and sr.customer_id = c.id) as return_amount,
                    
                    (select (c.previous_due + sale_total + payment_amount) - (sale_paid + received_amount + return_amount)) as due

                    from customers c
                    where c.status = 'a'
                    $clauses
                    " . ($branchId == null ? "" : " and c.branch_id = '$branchId'") . "";

        return DB::select($query);
    }
}
