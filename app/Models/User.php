<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'avatar'
    ];

    protected $guarded = [
        'username', 'password', 'first_name', 'middle_name',
        'last_name', 'suffix', 'gender', 'birthday',
        'id_number', 'office_id', 'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['fullname'];

    public function getFullnameAttribute()
    {
        return implode(' ', $this->only('first_name', 'middle_name', 'last_name', 'suffix'));
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function isUser()
    {
        return in_array($this->role->name, ['user','go']);
    }

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }

    public function sector()
    {
        return $this->belongsTo('App\Models\Sector');
    }

    public function tracking_records()
    {
        return $this->hasMany('App\Models\TrackingRecord', 'touched_by');
    }

    public function logs()
    {
        return $this->hasMany('App\Models\Log');
    }

    public function notification()
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function canEditThisDoc($id)
    {
        return $this->office->documents->find($id) || $this->isAdmin();
    }

    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }
}
