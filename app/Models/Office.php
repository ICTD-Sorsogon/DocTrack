<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    public $guarded = [
        'name', 'address', 'office_code',
        'contact_number', 'contact_email'
    ];

    public static function DocketOffice()
    {
        return self::whereOfficeCode('DO')->first()->id;
    }

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'originating_office');
    }

    public function tracker()
    {
        return $this->hasMany('App\Models\TrackingRecord', 'transaction_of');
    }

    public function notification()
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function report()
    {
        return $this->hasOne('App\Models\TrackingReport');
    }
}
