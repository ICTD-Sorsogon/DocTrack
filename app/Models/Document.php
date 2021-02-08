<?php

namespace App\Models;

use App\Events\DocumentEvent;
use App\Models\Traits\TrackingNumberBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TrackingNumberBuilder;

    protected $fillable = [
        'tracking_code', 'subject', 'document_type_id',
        'destination_office_id', 'current_office', 'sender_name',
        'page_count', 'date_filed', 'is_terminal', 'is_external',
        'remarks', 'attachment_page_count', 'status', 'priority_level','acknowledged'
    ];

    protected $dispatchesEvents = [
        'saved' => DocumentEvent::class,
        'deleting' => DocumentEvent::class,
    ];

    public function getDestinationOfficeIdAttribute($value)
    {
        $value = auth()->user()->can('update', $this) ? json_decode($value) : [auth()->user()->office->id];
        return Office::whereIn('id', $value)->get();
    }

    public function setDestinationOfficeIdAttribute($value)
    {
        $this->attributes['destination_office_id'] = json_encode($value);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->tracking_code = $model->tracking_code ?? $model->buildTrackingNumber($model);
            $model->originating_office = $model->originating_office ??  auth()->user()->office_id;
            $model->sender_name = User::find($model->sender_name)->id ?? $model->sender_name;
            $model->status = $model->status ?? 'created';
        });
    }

    public function current_office()
    {
        return $this->belongsTo('App\Models\Office', 'current_office_id');
    }

    public function origin_office()
    {
        return $this->belongsTo('App\Models\Office', 'originating_office');
    }

    public function destination()
    {
       return $this->belongsTo('App\Models\Office');
    }

    public function tracking_records()
    {
        return $this->hasMany('App\Models\TrackingRecord', 'document_id');
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
        $document = static::with(['document_type','origin_office', 'sender', 'tracking_records']);

        if($user->isUser()){
            $document->whereRaw("((json_contains(`destination_office_id`, {$user->office_id}) AND acknowledged = 1) OR originating_office = {$user->office_id} )");
        }

        return $document->orderBy('created_at', 'DESC')->get();
    }
}
