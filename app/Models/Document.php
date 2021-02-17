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
    use \Askedio\SoftCascade\Traits\SoftCascadeTrait;

    protected $softCascade = ['document_recipient'];

    protected $fillable = [
        'tracking_code', 'subject', 'document_type_id',
        'destination_office_id', 'sender_name',
        'page_count', 'date_filed', 'is_terminal', 'is_external',
        'remarks', 'attachment_page_count', 'status', 'priority_level','acknowledged'
    ];

    protected $dispatchesEvents = [
        'saved' => DocumentEvent::class,
        // 'deleting' => DocumentEvent::class,
    ];

    protected $casts = [
        'destination_office_id' => 'collection'
    ];

    protected $hidden = ['destination_office_id'];

    protected $appends = ['destination', 'recipient', 'multiple'];
   
    public function document_recipient()
    {
       return $this->hasMany(DocumentRecipient::class);
    }

    public function getMultipleAttribute(){
        return $this->destination_office_id->count() > 1;
    }

    public function getRecipientAttribute(){
        $recipient = DocumentRecipient::whereDocumentId($this->id);
        if(!auth()->user()->can('update', $this)){
            $recipient->whereDestinationOffice(auth()->user()->office->id);
        }

        return $recipient->get();
    }

    public function getDestinationAttribute()
    {
        $value = auth()->user()->can('update', $this) ? json_decode(optional($this->attributes)['destination_office_id']) : [auth()->user()->office->id];
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
        $document = static::with(['document_type','origin_office', 'sender', 'tracking_records']);

        if($user->isUser()){

            $outgoing = $document->whereOriginatingOffice($user->office_id)->orderBy('documents.created_at', 'DESC')->get();

            $incoming = Document::with(['document_recipient' => function ($query){
                               $query->whereDestinationOffice(auth()->user()->office->id);
                        }])
                        ->whereHas('document_recipient', function($query) use($user){ $query->whereRaw("destination_office = {$user->office_id} AND acknowledged = 1 AND rejected = 0");})->get();

            return compact('incoming', 'outgoing');
        }

        return $document->orderBy('documents.created_at', 'DESC')->get();
    }
}
