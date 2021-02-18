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

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'originating_office');
    }

    public function tracking_summaries()
    {
        return $this->hasMany('App\Models\TrackingSummary');
    }
}
