<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingSummary extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'action','document_id','office_id'
    ];

    public function document()
    {
        return $this->belongsTo('App\Models\Document');
    }

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }
}
