<?php

namespace App\Http\Controllers;

use App\Events\DocumentEvent;
use App\Http\Requests\DocumentPostRequest;
use Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\{Document,DocumentRecipient, DocumentType};
use App\Models\Office;
use App\Models\TrackingRecord;
use App\Models\TrackingSummary;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    const PRIORITY_LEVEL = [ null => 'NONE', 604800, 1296000, 2592000, 'Infinity'];

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
        $document->update(['status' => 'received']);
        $document->document_recipient->where('destination_office', auth()->user()->office->id)->first()->update([
            'received' => true
        ]);

        $speed = $document->acknowledgedDiff();

        return TrackingRecord::create([
            'document_id' => $request->id,
            'destination' => $request->destination,
            'action' => 'received',
            'through' => $request->through,
            'approved_by' => $request->approved_by,
            'touched_by' => Auth::user()->id,
            'last_touched' => Carbon::now(),
            'remarks' => $request->documentRemarks,
            'transaction_of' => Office::DocketOffice(),
            'speed' => $speed,
            'delayed' => self::PRIORITY_LEVEL[$document->priority_level] < $speed
        ]);
    }

    public function forwardDocument(Document $document, Request $request)
    {
        abort_if($request->forwarded_to == auth()->user()->office->id, 403);
        abort_if($document->multiple, 403);

        $recipient = $document->document_recipient()->whereDestinationOffice($request->forwarded_to)->first();
        $document->document_recipient()->whereDestinationOffice(auth()->user()->office->id)->update(['forwarded' => true]);
        $document->update(['status' => 'forwarded', 'destination_office_id' => [$request->forwarded_to], 'priority_level' => null ]);

        $speed = $document->receivedDiff();

        if(optional($recipient)->forwarded){ // if destination is going back to origin, reset origin
           $recipient->update([ 'acknowledged' => 0, 'received' => 0, 'forwarded' => 0 ]);
        } else {
            $document->document_recipient()->create([ // if not create a new one
                'document_id' => $document->id, 'destination_office' => $request->forwarded_to
            ]);
        }

        return TrackingRecord::create([
            'document_id' => $document->id,
            'destination' => $request->forwarded_to,
            'action' => 'forwarded',
            'through' => $request->through,
            'approved_by' => $request->approved_by,
            'touched_by' => Auth::user()->id,
            'last_touched' => Carbon::now(),
            'forwarded_by' => $request->forwarded_by,
            'forwarded_to' => $request->forwarded_to,
            'remarks' => $request->documentRemarks,
            'speed' => $speed,
            'transaction_of' => $request->forwarded_by,
            'delayed' => self::PRIORITY_LEVEL[$document->priority_level] < $speed
        ]);
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

        $document->update(['status' => 'acknowledged', 'priority_level' => $request->priority_levels]);
        $speed = $document->forwardeddiff();
        foreach($document->destination as $destination) {
            $tracking_record = TrackingRecord::create([
                'document_id' => $request->id,
                'action' => 'acknowledged',
                'destination' => $destination->id,
                'touched_by' => Auth::user()->id,
                'last_touched' => Carbon::now(),
                'remarks' => $request->documentRemarks,
                'transaction_of' => $document->status == 'forwarded' ?  $document->lastForwarded()->forwardedByOffice : $document->originating_office,
                'speed' => $speed,
                'delayed' => self::PRIORITY_LEVEL[$document->priority_level] < $speed
            ]);
        }

        return $tracking_record;
    }

    public function holdDocument(Document $document, Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($document->destination as $destination) {
                $tracking_record = new TrackingRecord();
                $tracking_record->document_id = $request->id;
                $tracking_record->action = 'on hold';
                $tracking_record->touched_by = Auth::user()->id;
                $tracking_record->destination = $destination->id;
                $tracking_record->last_touched = Carbon::now();
                $tracking_record->remarks = $request->documentRemarks;
                $tracking_record->save();
                $tracking_record->document_recipient->first()->update(['hold' => 1]);
                $tracking_record->document->update([
                    'status' => 'on hold',
                    'priority_level' => $request->priority_levels
                    ]);
            }

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
            $tracking_record = TrackingRecord::where('document_id', $request->id)->first();
            $tracking_record->action = 'date changed';
            $tracking_record->update([
                'last_touched' => Carbon::parse($updatedTime)
                ]);
            TrackingSummary::where('document_id', $tracking_record->document_id)
                ->where('office_id', auth()->user()->office_id)
                ->update(['created_at' => Carbon::parse($updatedTime)]);
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

    public function releaseDocument(Document $document, Request $request)
    {

        DB::beginTransaction();
        try {
            foreach($document->destination as $destination) {
                $tracking_record = new TrackingRecord();
                $tracking_record->document_id = $request->id;
                $tracking_record->action = 'released';
                $tracking_record->destination = $destination->id;
                $tracking_record->touched_by = Auth::user()->id;
                $tracking_record->last_touched = Carbon::now();
                $tracking_record->remarks = $request->documentRemarks;
                $tracking_record->save();
                $tracking_record->document->update(['priority_level' => $request->priority_levels]);
                $tracking_record->document_recipient->first()->update(['hold' => 0]);
            }

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
        // FIXME: Editing a document creates a new record in the tracking_summaries table
        // TODO: Highlight in front end menu, Forward not tracked in fastest and slowest
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
       $summary = TrackingRecord::with('document')->whereNotNull('transaction_of')->get(['transaction_of', 'document_id', 'action', 'speed', 'delayed', 'last_touched','touched_by']);

       if(!auth()->user()->isAdmin()){
           return $summary->whereStrict('transaction_of', auth()->user()->office_id);
       }

       return $summary;
    }

    public function officeReports()
    {
        $summary = TrackingSummary::with('office', 'document')->where('office_id', auth()->user()->office_id)
        ->get();
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
