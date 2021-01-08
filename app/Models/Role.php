<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public $timestamp = false;

    protected $guarded = [
        'name'
    ];

    public function users() {
        return $this->hasMany('App\Models\User');
    }

}
