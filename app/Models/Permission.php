<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    public $timestamp = false;

    protected $guarded = [
        'name'
    ];


    public function role() {
        return $this->hasMany('App\Models\RolePermission');
    }
}
