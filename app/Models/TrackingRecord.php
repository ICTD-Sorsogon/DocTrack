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

    protected $fillable = [
        'document_id', 'action', 'status',
        'approved_by', 'touched_by', 'last_touched',
        'forwarded_by', 'forwarded_to', 'remarks','destination'
    ];

    protected $appends = [
        'date_filed',
        'touched_user'
    ];

    public function getTouchedUserAttribute($value)
    { 
        $value = $this->attributes['touched_by'];
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
        return $this->belongsTo('App\Models\Office');
    }

    public function document()
    {
        return $this->belongsTo('App\Models\Document');
    }

    public function document_recipient()
    {
        return $this->hasMany('App\Models\DocumentRecipient', 'document_id', 'document_id')->where('destination_office', $this->destination);
    }
}
