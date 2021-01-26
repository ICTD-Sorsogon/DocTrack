<?php

namespace App\Http\Controllers;

use App\Events\DocumentCreateEvent;
use App\Events\DocumentUpdateEvent;
use App\Events\NewDocumentHasAddedEvent;
use App\Http\Requests\DocumentPostRequest;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\TrackingRecord;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getDocumentTypes(): Collection
    {
        return DocumentType::get();
    }

    public function getAllActiveDocuments(Document $documents)
    {
        return $documents->allDocuments(Auth::user());
    }

    public function getNonPaginatedActiveDocuments()
    {
        $documents = Document::where('current_office_id', Auth::user()->office_id)
                    ->where('is_terminal', false)
                    ->orderBy('date_filed', 'desc')
                    ->get();
        return $documents;
    }

    public function getSelectedDocument($id)
    {
        $document= Document::find($id);
    }

    public function receiveDocument(Request $request)
    {
        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->action = 'received';
            $tracking_record->through = $request->through;
            $tracking_record->approved_by = $request->approved_by;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'received']);

        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$tracking_record];
    }

    public function forwardDocument(Request $request)
    {
        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->action = 'forwarded';
            $tracking_record->through = $request->through;
            $tracking_record->approved_by = $request->approved_by;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->forwarded_by = $request->forwarded_by;
            $tracking_record->forwarded_to = $request->forwarded_to;
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'forwarded']);

        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$tracking_record];
    }

    public function terminateDocument(Request $request)
    {
        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->action = 'terminated';
            $tracking_record->approved_by = $request->approved_by;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'terminated']);
            $tracking_record->document->delete();

        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$tracking_record];
    }

    public function acknowledgeDocument(Request $request)
    {
        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->action = 'acknowledged';
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'acknowledged']);

        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$tracking_record];
    }

    public function holdRejectDocument(Request $request)
    {
        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->action = $request->hold_reject;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();
            $tracking_record->document->update(['status' => $request->hold_reject]);

        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$tracking_record];
    }

    public function addNewDocument(Document $document, DocumentPostRequest $request)
    {
        if(!$document->id){
            $request_obj = '{
                "attachment_page_count":"' . $request->attachment_page_count . '",
                "destination_office_id":"' . $request->destination_office_id . '",
                "document_type_id":"' . $request->document_type_id . '",
                "id":"' . $request->id . '",
                "originating_office":"' . $request->originating_office . '",
                "page_count":"' . $request->page_count . '",
                "remarks":"' . $request->remarks . '",
                "sender_name":"' . $request->sender_name . '",
                "subject":"' . $request->subject . '",
                "tracking_code":"' . $request->tracking_code . '"}';
    
            $user_id = Auth::user()->id;
            event(new DocumentCreateEvent($user_id, json_decode($request_obj)));

        } else{

        $old_values = Document::select('attachment_page_count','destination_office_id','document_type_id','id','originating_office','page_count','remarks','sender_name','subject','tracking_code')->where('id', $request->id)->get();
            $request_obj = '{
                "attachment_page_count":"' . $request->attachment_page_count . '",
                "destination_office_id":"' . $request->destination_office_id . '",
                "document_type_id":"' . $request->document_type_id . '",
                "id":"' . $request->id . '",
                "originating_office":"' . $request->originating_office . '",
                "page_count":"' . $request->page_count . '",
                "remarks":"' . $request->remarks . '",
                "sender_name":"' . $request->sender_name . '",
                "subject":"' . $request->subject . '",
                "tracking_code":"' . $request->tracking_code . '"}';

        $user_id = Auth::user()->id;
        event(new DocumentUpdateEvent($user_id, json_decode($old_values[0]), json_decode($request_obj)));

        }

        return $document->updateOrCreate(
            ['id' => $document->id],
            $request->validated()
        );


        if(!$document->id){
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->action = 'created';
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'created']);

        }

        return true;
      
    }
}
