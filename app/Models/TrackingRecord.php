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
        'id', 'updated_at'
    ];

    protected $appends = [
        'date_filed',
        'touched_user'
    ];

    public function getTouchedUserAttribute($value)
    { 
        $value = $this->attributes['touched_by'] ?? NULL;
        return User::with('office')->find($value)->only('avatar', 'office');
    }

    public function getDateFiledAttribute()
    {
        return Carbon::parse($this->attributes['last_touched'])->diffForHumans();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'touched_by');
    }

    public function office()
    {
        return $this->belongsTo('App\Models\Office','transaction_of', 'id');
    }

    public function document()
    {
        return $this->belongsTo('App\Models\Document');
    }

    public function document_recipient()
    {
        return $this->hasMany('App\Models\DocumentRecipient', 'document_id', 'document_id')->where('destination_office', $this->destination);
    }

    public function forwardedByOffice()
    {
        return $this->hasOne('App\Models\Office','id','forwarded_by');
    }
}
