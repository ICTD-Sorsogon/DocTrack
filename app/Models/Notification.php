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

    public function document()
    {
        return $this->belongsTo('App\Models\Document', 'document_id');
    }

    public function expire()
    {
        $days = Carbon::now()->diffInDays($this->attributes['updated_at']);
        if($this->priority_level() == 1 && $days > 7){
            return true;
        }
        else if($this->priority_level() == 2 && $days > 15){
            return true;
        }
        else if($this->priority_level() == 3 && $days > 30){
            return true;
        }

        return false;

    }

    public function priority_level()
    {
        return $this->document->priority_level;
    }
}

