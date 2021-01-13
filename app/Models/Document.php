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

    public function current_office()
    {
        return $this->belongsTo('App\Models\Office', 'current_office_id');
    }

    public function origin_office()
    {
        return $this->belongsTo('App\Models\Office', 'originating_office');
    }

    public function tracking_records()
    {
        return $this->hasMany('App\Models\TrackingRecord');
    }

    public function document_type()
    {
        return $this->belongsTo('App\Models\DocumentType');
    }

    public function sender() 
    {
        return $this->belongsTo('App\Models\Personnel', 'sender_name');
    }

    public function tracker() 
    {
        return $this->hasMany('App\Models\TrackingRecord');
    }

    public static function allDocuments(User $user) 
    {
        $document = static::with('document_type', 'current_office', 'origin_office', 'sender')
                    ->where('is_terminal', false);

        if($user->isUser()){
            $document->whereRaw("(destination_office_id = {$user->office_id} OR originating_office = {$user->office_id} )");
        }

        return $document->get();
    }
}
