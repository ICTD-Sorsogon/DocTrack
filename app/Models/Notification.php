<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'office_id', 'user_id',
        'status'
    ];

    protected $appends = ['avatar', 'office_code'];

    public function getAvatarAttribute()
    {
        return $this->user->avatar;
    }

    public function getOfficeCodeAttribute()
    {
        return $this->user->office->name;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }

}

