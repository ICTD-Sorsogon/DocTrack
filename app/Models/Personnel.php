<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;
    public $timestamp = false;

    protected $guarded = [
        'name'
    ];

    public function tracking_record() {
        return $this->hasMany('App\Models\TrackingRecord');
    }

    public function office() {
        return $this->belongsTo('App\Models\Office');
    }
}
