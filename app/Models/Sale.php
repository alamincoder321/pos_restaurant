<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->select('id', 'code', 'name', 'owner', 'phone', 'address')->withTranshed();
    }
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id')->select('id', 'emp_code', 'name')->withTranshed();
    }
}
