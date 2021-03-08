<?php

namespace App\Models;

use App\Events\DocumentEvent;
use App\Models\Traits\TrackingNumberBuilder;
use Carbon\Carbon;
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

    protected $appends = ['destination', 'multiple'];

    public function document_recipient()
    {
       return $this->hasMany(DocumentRecipient::class);
    }

    public function incoming_trashed()
    {
        return $this->hasMany(DocumentRecipient::class)->withTrashed();
    }

    public function getMultipleAttribute(){
        return $this->destination_office_id->count() > 1;
    }

    public function getDestinationAttribute()
    {
        $value = auth()->user()->can('update', $this) ? json_decode(optional($this->attributes)['destination_office_id']) : [auth()->user()->office->id];
        return Office::get()->only($value);
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

    public function tracking_summaries()
    {
        return $this->hasMany('App\Models\TrackingSummary');
    }

    public function acknowledgedDiff()
    {
        return $this->tracker()->whereAction('acknowledged')->get()->last()->created_at->diffInSeconds(Carbon::now());
    }

    public function receivedDiff()
    {
        return $this->tracker()->whereAction('received')->get()->last()->created_at->diffInSeconds(Carbon::now());
    }

    public function forwardedDiff()
    {
        return ($this->lastForwarded() ?? $this)->created_at->diffInSeconds(Carbon::now());
    }

    public function lastForwarded()
    {
        return $this->tracker()->whereAction('forwarded')->get()->last(); 
    }

    public static function allDocuments(User $user)
    {
        $document = static::with(['document_type','origin_office', 'sender', 'tracking_records', 'document_recipient']);
        if($user->isUser()){

            $outgoing = (clone $document)->whereOriginatingOffice($user->office_id)->orderBy('documents.created_at', 'DESC')->get();
            $incoming = $document
                        ->with(['document_recipient' => function($query) use($user) {
                            $query->whereDestinationOffice($user->office->id)->get();
                        }])
                        ->whereHas('document_recipient', function($query) use($user){ $query->whereRaw("destination_office = {$user->office_id} AND acknowledged = 1 AND hold = 0");})->get();

            return compact('incoming', 'outgoing');
        }

        return $document->orderBy('documents.created_at', 'DESC')->get();

    }

    public static function allDocumentsArchive(User $user, $request)
    {
        $document = static::select('documents.*', static::raw('YEAR(created_at) as year'))
                        ->with(['document_type','origin_office', 'sender', 'tracking_records', 'incoming_trashed'])->withTrashed()
                        ->whereHas('incoming_trashed', function($query){ $query->whereNotNull('deleted_at'); });
        $year = static::getYr($document);
        static::filter($document, $request);
        if ($user->isUser()) {
            $ot = $document->where('originating_office', auth()->user()->office->id)->orderBy('created_at', 'DESC')->get();
            $in = Document::with(['document_type','origin_office', 'sender', 'tracking_records', 'incoming_trashed' => function ($query){
                                $query->whereDestinationOffice(auth()->user()->office->id);
                            }])->withTrashed()
                            ->whereHas('incoming_trashed', function($query) use($user, $request){
                                static::filter($query->where('destination_office', $user->office_id)->onlyTrashed(), $request);
                            })->get();
            $document = $ot->merge($in);
        }
        return response()->json([
            'data' => ($user->isAdmin())?$document->orderBy('created_at', 'DESC')->get() : $document,
            'year' => ($user->isAdmin())?$year : static::userYearCollection()
        ]);
    }

    public static function getYr($document)
    {
        return collect($document->pluck('year')->unique())->flatten();
    }

    public static function userYearCollection()
    {
        $oId = auth()->user()->office->id;
        $in = Document::with('origin_office')->select(Document::raw('YEAR(created_at) as year'))->where('originating_office', $oId)->onlyTrashed()->get();
        $ot = DocumentRecipient::select(DocumentRecipient::raw('YEAR(created_at) as year'))->where('destination_office', $oId)->onlyTrashed()->get();
        return static::getYr($in)->merge(static::getYr($ot));
    }

    public static function filter($document, $request)
    {
        $isByYear = ($request->filterBy == 'Year')?true:false;
        $selected = $request->selected;

        return $document->when(!$isByYear, function ($uquery) use ($selected, $isByYear) {
            return $uquery->whereBetween('created_at', [$selected[0].' 00:00:00', $selected[1].' 23:59:59']);
        })->when($isByYear, function ($uquery) use ($selected) {
            return $uquery->whereIn(DocumentRecipient::raw('YEAR(`created_at`)'), $selected);
        });
    }

}
