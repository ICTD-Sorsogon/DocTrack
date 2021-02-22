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

    public function incoming_trashed()
    {
        return $this->hasMany(DocumentRecipient::class)->withTrashed();
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

    public static function allDocuments(User $user)
    {
        $document = static::with(['document_type','origin_office', 'sender', 'tracking_records']);
        if($user->isUser()){

            $outgoing = (clone $document)->whereOriginatingOffice($user->office_id)->orderBy('documents.created_at', 'DESC')->get();
            $incoming = $document
                        ->whereHas('document_recipient', function($query) use($user){ $query->whereRaw("destination_office = {$user->office_id} AND acknowledged = 1 AND rejected = 0");})->get();

            return compact('incoming', 'outgoing');
        }

        return $document->orderBy('documents.created_at', 'DESC')->get();

    }

    public static function allDocumentsArchive(User $user, $request)
    {
        $isByYear = ($request->filterBy == 'Year')?true:false;
        $selected = $request->selected;


        //dd($selected);

        /*$document = static::with(['document_type','origin_office', 'sender', 'tracking_records', 'incoming_trashed']);
        if($user->isUser()){
            $ot = $document->whereOriginatingOffice($user->office_id)->orderBy('documents.created_at', 'DESC')->get();
            $in = Document::with(['document_recipient' => function ($query){
                               $query->whereDestinationOffice(auth()->user()->office->id);
                        }])
                        ->whereHas('document_recipient', function($query) use($user){
                            $query->whereRaw("destination_office = {$user->office_id} AND acknowledged = 1 AND rejected = 0");
                        })->get();
            return compact('in', 'ot');
        }
        //return $document->whereHas('incoming_trashed')->onlyTrashed()->get();
        return $document->orderBy('documents.created_at', 'DESC')->onlyTrashed()->get();*/

        //new code
        $document = static::select('documents.*', static::raw('YEAR(created_at) as year'))
                        ->with(['document_type','origin_office', 'sender', 'tracking_records', 'incoming_trashed'])->onlyTrashed();
        $year = static::getYr($document);
        /*$document->when(!$isByYear, function ($query) use ($selected) {
                return $query->whereBetween('created_at', [$selected[0].' 00:00:00', $selected[1].' 23:59:59']);
            })->when($isByYear, function ($query) use ($selected) {
                return $query->whereIn(Document::raw('YEAR(`created_at`)'), $selected);
            });*/
        static::filter($document, $selected, $isByYear);
        if ($user->isUser()) {
            $ot = $document->where('originating_office', auth()->user()->office->id)->orderBy('created_at', 'DESC')->get();
            $in = Document::with(['document_type','origin_office', 'sender', 'tracking_records', 'incoming_trashed' => function ($query){
                                $query->whereDestinationOffice(auth()->user()->office->id);
                            }])->withTrashed()
                            ->whereHas('incoming_trashed', function($query) use($user, $selected, $isByYear){
                                //$query->where('destination_office', $user->office_id)->onlyTrashed();
                                /*$query->where('destination_office', $user->office_id)->onlyTrashed()
                                        ->when(!$isByYear, function ($uquery) use ($selected, $isByYear) {
                                            return $uquery->whereBetween('created_at', [$selected[0].' 00:00:00', $selected[1].' 23:59:59']);
                                        })->when($isByYear, function ($uquery) use ($selected) {
                                            return $uquery->whereIn(DocumentRecipient::raw('YEAR(`created_at`)'), $selected);
                                        });*/
                                static::filter($query->where('destination_office', $user->office_id)->onlyTrashed(), $selected, $isByYear);
                            })->get();
            //$year_out = collect($document->pluck('year')->unique())->flatten();
            /*$year_out = collect(Document::with('origin_office')
                            ->select(Document::raw('YEAR(created_at) as year'))
                            ->where('originating_office', auth()->user()->office->id)->onlyTrashed()->get()->pluck('year')->unique()
                        )->flatten();
            $year_inc = collect(DocumentRecipient::select(DocumentRecipient::raw('YEAR(created_at) as year'))
                            ->where('destination_office', $user->office_id)->onlyTrashed()->get()->pluck('year')->unique()
                        )->flatten();*/
            $document = $ot->merge($in);
        }
        //new code*/
        //if ($user->isAdmin()){
            //$document->orderBy('created_at', 'DESC');
            //$year = collect($document->pluck('year')->unique())->flatten();
        //}
        return response()->json([
            'data' => ($user->isAdmin())?$document->orderBy('created_at', 'DESC')->get() : $document,
            'year' => ($user->isAdmin())?$year : static::userYearCollection() //$year_out->merge($year_inc)
        ]);

        //backup
        /*$document = static::select('documents.*', static::raw('YEAR(created_at) as year'))
                        ->with(['document_type','origin_office', 'sender', 'tracking_records', 'incoming_trashed'])->onlyTrashed();
        if ($user->isUser()) {
            $ot = $document->where('originating_office', auth()->user()->office->id)->orderBy('created_at', 'DESC')->get();
            $in = Document::with(['document_type','origin_office', 'sender', 'tracking_records', 'incoming_trashed' => function ($query){
                                $query->whereDestinationOffice(auth()->user()->office->id);
                            }])->withTrashed()
                            ->whereHas('incoming_trashed', function($query) use($user){
                                $query->where('destination_office', $user->office_id)->onlyTrashed();
                            })->get();
            $year_out = collect($document->pluck('year')->unique())->flatten();
            $year_inc = collect(DocumentRecipient::select(DocumentRecipient::raw('YEAR(created_at) as year'))
                            ->where('destination_office', $user->office_id)->onlyTrashed()->get()->pluck('year')->unique()
                        )->flatten();
            $document = $ot->merge($in);
            //$year = $document->pluck('year');

        }
        if ($user->isAdmin()){
           $document->orderBy('created_at', 'DESC');
           //$year = $document->pluck('year');
           $year = collect($document->pluck('year')->unique())->flatten();
        }

        return response()->json([
            'data' => ($user->isAdmin())?$document->get() : $document,
            'year' => ($user->isAdmin())?$year : $year_out->merge($year_inc)
        ]);*/
        //backup







        /*$document = static::with('document_type','origin_office', 'sender', 'tracking_records')->onlyTrashed();
        //$document = static::with(['document_type','origin_office', 'sender', 'tracking_records'])->onlyTrashed();

        if ($user->isUser()) {
            //$document->whereRaw("(destination_office_id = {$user->office_id} OR originating_office = {$user->office_id} )");
            //$document->whereJsonContains('destination_office_id', $user->office_id)->where('acknowledged', 1)->orWhere('originating_office', $user->office_id);
        }

        $document->when(!$isByYear, function ($query) use ($selected) {
                    return $query->whereBetween('created_at', [$selected[0].' 00:00:00', $selected[1].' 23:59:59']);
                })->when($isByYear, function ($query) use ($selected) {
                    return $query->whereIn(Document::raw('YEAR(`created_at`)'), $selected);
                });

        $docu = $document->orderBy('created_at', 'DESC')->get();
        $year = static::select(Document::raw('YEAR(created_at) as year'))
            ->onlyTrashed()
            ->distinct()
            ->get()
            ->pluck('year');

        return response()->json(['data' => $docu, 'year' => $year]);*/
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

    public static function filter($document, $selected, $isByYear)
    {
        return $document->when(!$isByYear, function ($uquery) use ($selected, $isByYear) {
            return $uquery->whereBetween('created_at', [$selected[0].' 00:00:00', $selected[1].' 23:59:59']);
        })->when($isByYear, function ($uquery) use ($selected) {
            return $uquery->whereIn(DocumentRecipient::raw('YEAR(`created_at`)'), $selected);
        });
    }

}
