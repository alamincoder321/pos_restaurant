<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

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

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')->select('id', 'name')->withTrashed();
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id')->select('id', 'name')->withTrashed();
    }
    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id')->select('id', 'name', 'title')->withTrashed();
    }
}
