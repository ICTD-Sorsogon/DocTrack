<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [
        'tracking_code', 'subject', 'document_type_id',
        'originating_office', 'current_office', 'sender_name',
        'page_count', 'date_filed', 'is_terminal',
        'remarks', 'attachment_page_count'
    ];

    public function office()
    {
        return $this->belongsTo('App\Models\Office');
    }

    public function tracking_records()
    {
        return $this->hasMany('App\Models\TrackingRecord');
    }

    public function document_type()
    {
        return $this->belongsTo('App\Models\DocumentType');
    }
}
