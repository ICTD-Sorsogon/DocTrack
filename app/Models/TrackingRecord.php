<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackingRecord extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'document_id', 'actions', 'status',
        'approved_by', 'touched_by', 'last_touched',
        'forwarded_by', 'forwarded_to', 'remarks'
    ];

    public function getCreatedAtAttribute($value) 
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }

    public function document()
    {
        return $this->belongsTo('App\Models\Document');
    }
}
