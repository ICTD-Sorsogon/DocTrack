<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'office_id', 'user_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function office()
    {
        return $this->hasMany('App\Models\User', 'office_id');
    }

    public function document()
    {
        return $this->hasMany('App\Models\Document', 'document_id');
    }
}

