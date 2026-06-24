<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Menu extends Model
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

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id')->select('id', 'name')->withTrashed();
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->select('id', 'name')->withTrashed();
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id')->select('id', 'name')->withTrashed();
    }


    //stock
    public static function stock($request, $date = null)
    {
        $branchId = session('branch')->id;
        $request = (object)$request;
        $clauses = "";
        if (!empty($request->productId)) {
            $clauses .= " and p.id = '$request->productId'";
        }
        if (!empty($request->categoryId)) {
            $clauses .= " and p.category_id = '$request->categoryId'";
        }
        if (!empty($request->brandId)) {
            $clauses .= " and p.brand_id = '$request->brandId'";
        }

        $query = DB::select("select p.id, p.code, p.name, p.purchase_rate, u.name as unit_name,

                            (select ifnull(sum(pd.quantity), 0) from purchase_details pd
                            join purchases pm on pm.id = pd.purchase_id
                            where pd.menu_id = p.id
                            and pd.status = 'a'
                            ".($date == null ? "" : " and pm.date <= '$date'" )."
                            ".($branchId == null ? "" : " and pd.branch_id = '$branchId'" ).") as purchase_quantity,
                            
                            (select ifnull(sum(prd.quantity), 0) from purchase_return_details prd
                            join purchase_returns prm on prm.id = prd.purchase_return_id
                            where prd.menu_id = p.id
                            and prd.status = 'a'
                            ".($date == null ? "" : " and prm.date <= '$date'" )."
                            ".($branchId == null ? "" : " and prd.branch_id = '$branchId'" ).") as purchase_return_quantity,

                            (select ifnull(sum(sd.quantity), 0) from sale_details sd
                            join sales sm on sm.id = sd.sale_id
                            where sd.menu_id = p.id
                            and sd.status = 'a'
                            ".($date == null ? "" : " and sm.date <= '$date'")."
                            ".($branchId == null ? "" : " and sd.branch_id = '$branchId'" ).") as sale_quantity,
                            
                            (select ifnull(sum(srd.quantity), 0) from sale_return_details srd
                            join sale_returns srm on srm.id = srd.sale_return_id
                            where srd.menu_id = p.id
                            and srd.status = 'a'
                            ".($date == null ? "" : " and srm.date <= '$date'")."
                            ".($branchId == null ? "" : " and srd.branch_id = '$branchId'" ).") as sale_return_quantity,
                            
                            (select ifnull(sum(dd.quantity), 0) from damage_details dd
                            join damages d on d.id = dd.damage_id
                            where dd.menu_id = p.id
                            and dd.status = 'a'
                            ".($date == null ? "" : " and d.date <= '$date'")."
                            ".($branchId == null ? "" : " and dd.branch_id = '$branchId'" ).") as damage_quantity,

                            (select (purchase_quantity + sale_return_quantity) - (sale_quantity + purchase_return_quantity + damage_quantity)) as stock,
                            (select stock * p.purchase_rate) as stock_value
                            from menus p
                            left join units u on u.id = p.unit_id
                            where p.status = 'a'
                            and p.branch_id = '$branchId'
                            and p.is_service = '0'
                            $clauses");


        return $query;
    }
}
