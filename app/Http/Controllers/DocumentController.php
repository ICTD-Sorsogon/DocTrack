<?php

namespace App\Http\Controllers;

use App\Events\DocumentEvent;
use App\Http\Requests\DocumentPostRequest;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Document;
use App\Models\DocumentRecipient;
use App\Models\DocumentType;
use App\Models\TrackingRecord;
use App\Models\TrackingSummary;
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

    public function getAllArchiveDocuments(Document $documents, Request $request)
    {
        return $documents->allDocumentsArchive(Auth::user(), $request);
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

    public function receiveDocument(Document $document, Request $request)
    {
        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->destination = $request->destination;
            $tracking_record->action = 'received';
            $tracking_record->through = $request->through;
            $tracking_record->approved_by = $request->approved_by;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();
            $tracking_record->document->update(['status' => 'received']);
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();

        $document->document_recipient->where('destination_office', auth()->user()->office->id)->first()->update([
            'received' => true
        ]);

        TrackingSummary::create([
            'action' => 'received',
            'document_id' => $document->id,
            'office_id' => auth()->user()->office_id
        ]);

        return [$tracking_record];
    }

    public function forwardDocument(Document $document, Request $request)
    {
        abort_if($request->forwarded_to == auth()->user()->office->id, 403);
        abort_if($document->multiple, 403);

        $recipient = $document->document_recipient()->whereDestinationOffice($request->forwarded_to)->first();


        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->destination = $request->forwarded_to;
            $tracking_record->action = 'forwarded';
            $tracking_record->through = $request->through;
            $tracking_record->approved_by = $request->approved_by;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->forwarded_by = $request->forwarded_by;
            $tracking_record->forwarded_to = $request->forwarded_to;
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();

            TrackingSummary::create([
                'action' => 'forwarded',
                'document_id' => $document->id,
                'office_id' => auth()->user()->office_id
            ]);

        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();

        if(optional($recipient)->forwarded){
           $recipient->update([
                'acknowledged' => 0,
                'received' => 0,
                'forwarded' => 0
            ]);

            return [$tracking_record];
        }

        $destination = $document->destination_office_id->push($request->forwarded_to);
        $document->document_recipient()->whereDestinationOffice(auth()->user()->office->id)->update(['forwarded' => true]);

        $document->update(['status' => 'forwarded', 'destination_office_id' => [$request->forwarded_to], 'priority_level' => null ]);

        $document->document_recipient()->create([
            'document_id' => $document->id, 'destination_office' => $request->forwarded_to
        ]);

        return [$tracking_record];
    }

    public function terminateDocument(Document $document, Request $request)
    {
        DB::beginTransaction();
        try {
            $tracking_record = new TrackingRecord();
            $tracking_record->document_id = $request->id;
            $tracking_record->destination = auth()->user()->office->id;
            $tracking_record->action = 'terminated';
            $tracking_record->approved_by = $request->approved_by;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();

            $admin = auth()->user()->isAdmin();

            DocumentRecipient::where(['document_id' => $request->id,
                                      'destination_office' => auth()->user()->office->id])->delete();

            $document->status = 'terminated';
            DocumentEvent::dispatch($document);

            if($admin){
                $tracking_record->document->update(['status' => 'terminated']);
                $tracking_record->document->delete();
            }

        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();
        return [$tracking_record];
    }

    public function acknowledgeDocument(Document $document, Request $request)
    {
        DocumentRecipient::whereIn('recipient_id', $document->document_recipient->pluck('recipient_id'))
            ->update(['acknowledged' => true]);

        DB::beginTransaction();
        try {
            foreach($document->destination as $destination) {
                $tracking_record = new TrackingRecord();
                $tracking_record->document_id = $request->id;
                $tracking_record->action = 'acknowledged';
                $tracking_record->destination = $destination->id;
                $tracking_record->touched_by = Auth::user()->id;
                $tracking_record->last_touched = Carbon::now();
                $tracking_record->remarks = $request->documentRemarks;
                $tracking_record->save();
                $tracking_record->document->update(['status' => 'acknowledged', 'priority_level' => $request->priority_levels]);
            }

            TrackingSummary::create([
                'action' => 'acknowledged',
                'document_id' => $document->id,
                'office_id' => auth()->user()->office->id
            ]);
            $user_id = Auth::user()->id;

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

    public function changeDateDocument(Request $request)
    {
        $updatedTime = $request->date_filed. ' ' .$request->time_filed;
        DB::beginTransaction();
        try {
            $tracking_record = TrackingRecord::find($request->id);
            $tracking_record->action = 'date changed';
            $tracking_record->update([
                'last_touched' => Carbon::parse($updatedTime)
                ]);

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
       $document = $document->updateOrCreate(
            ['id' => $document->id],
            $request->validated()
        );

        $diff = DocumentRecipient::whereDocumentId($document->id)->pluck('destination_office')->diff(
            $document->destination_office_id
        );

        foreach($document->destination as $office){
            DocumentRecipient::updateOrCreate(
                [ 'document_id' => $document->id, 'destination_office' => $office->id ],
                [ 'document_id' => $document->id, 'destination_office' => $office->id ]
            );
            TrackingSummary::create([
                'action' => 'created',
                'document_id' => $document->id,
                'office_id' => auth()->user()->office->id
            ]);
            TrackingRecord::create([
                'action' => 'created',
                'document_id' => $document->id,
                'destination' => $office->id,
                'touched_by' => auth()->user()->id,
                'remarks' => $document->remarks,
                'last_touched' => Carbon::now()
            ]);
        };

       DocumentRecipient::whereDocumentId($document->id)
            ->whereIn('destination_office', $diff->toArray())->forceDelete();

       return $document;
    }

    public function trackingReports()
    {
        $summary = TrackingSummary::with('office', 'document')->get()->groupBy('document_id');
        return $summary;
    }

    public function restoreDocument(Request $request)
    {
        $docu = $this->myDocu($request->documentId);
        $docu->status = 'received';
        $docu->deleted_at = null;
        $docu->save();

        return ($request->isRoot)?
            $this->myDocu($request->documentId)->incoming_trashed()->restore() :
            DocumentRecipient::withTrashed()->where('recipient_id', $request->dbId)->restore();
    }

    public function myDocu($id)
    {
        return Document::withTrashed()->find($id);
    }
}
