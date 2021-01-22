<?php

namespace App\Http\Controllers;

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
            // $tracking_record = TrackingRecord::find($request->id);
            $tracking_record->document_id = $request->id;
            $tracking_record->action = 'receive';
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->through = $request->through;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'receive']);

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
            // $tracking_record = TrackingRecord::find($request->id);
            $tracking_record->document_id = $request->id;
            $tracking_record->action = 'forward';
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->forwarded_by = $request->forwarded_by;
            $tracking_record->forwarded_to = $request->forwarded_to;
            $tracking_record->through = $request->through;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'forward']);

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
        return $document->updateOrCreate(
            ['id' => $document->id],
            $request->validated()
        );

        if(!$document->id){
            $user_id = Auth::user()->id;
            event(new NewDocumentHasAddedEvent($user_id, $request));
        }

        return true;
        /**
         * KENNETH SOLOMON
         * TODO after save or update, dipatch events user logs and doc logs
         * PLEASE USE LARAVEL EVENTS LIKE HERE https://laravel.com/docs/8.x/events
         */
    }
}
