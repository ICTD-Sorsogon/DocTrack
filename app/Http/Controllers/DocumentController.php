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
use App\Models\Office;
use App\Models\TrackingRecord;
use App\Models\TrackingSummary;
use Illuminate\Http\Request;
use App\Models\TrackingReport;

class DocumentController extends Controller
{
    const PRIORITY_LEVEL = [ NULL, 604800000, 1296000000, 2592000000, 'Infinity'];

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

        $diff = $document->acknowledgedDiff();
        $office = Office::whereOfficeCode('DO')->first();

        auth()->user()->office->report->increment('transactions');

        if(self::PRIORITY_LEVEL[$document->priority_level] < $diff){
            $office->report->increment('delayed');
        }

        $office->report->update([
            'speeds' => $office->report->speeds?->push($diff) ?? [$diff]
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

        $office = auth()->user()->office;
        $office->report->increment('transactions');
        $diff = $document->receivedDiff();

        if(self::PRIORITY_LEVEL[$document->priority_level] < $diff){
           $office->report->increment('delayed');
        }

        $office->report->update([
            'speeds' => $office->report->speeds?->push($diff) ?? [$diff]
        ]);

        $document->document_recipient()->whereDestinationOffice(auth()->user()->office->id)->update(['forwarded' => true]);
        $document->update(['status' => 'forwarded', 'destination_office_id' => [$request->forwarded_to], 'priority_level' => null ]);

        if(optional($recipient)->forwarded){
           $recipient->update([
                'acknowledged' => 0,
                'received' => 0,
                'forwarded' => 0
            ]);

            return [$tracking_record];
        }


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
            }
            $tracking_record->document->update(['status' => 'acknowledged', 'priority_level' => $request->priority_levels]);

            TrackingSummary::create([
                'action' => 'acknowledged',
                'document_id' => $document->id,
                'office_id' => auth()->user()->office_id
            ]);

            $diff = $document->created_at->diffInSeconds(Carbon::now());
            $office = $document->origin_office;

            if($document->status == 'forwarded'){
                $trackingRecord = $document->tracking_records->where('action', 'forwarded')->last();
                $office = $trackingRecord->forwardedByOffice;
                $diff = $trackingRecord->created_at->diffInSeconds(Carbon::now());
            }


            Office::whereOfficeCode('DO')->first()->report->increment('transactions');

            if(self::PRIORITY_LEVEL[$tracking_record->document->priority_level] < $diff){
                $office->report->increment('delayed');
            }

            $office->report->update([
                'speeds' => $office->report->speeds?->push($diff) ?? [$diff] //use optional for php < 8
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
        // Should only create one creation record in the summary
        if($document->wasRecentlyCreated) {
            TrackingSummary::create([
                'action' => 'created',
                'document_id' => $document->id,
                'office_id' => auth()->user()->office_id
            ]);

            TrackingReport::firstOrCreate([ 'office_id' => auth()->user()->office_id])->increment('transactions');

        }

       DocumentRecipient::whereDocumentId($document->id)
            ->whereIn('destination_office', $diff->toArray())->forceDelete();

       return $document;
    }

    public function trackingReports()
    {
       $summary = TrackingReport::with('office')->get();
       if(!auth()->user()->isAdmin()){
            return $summary->only(auth()->user()->office_id)->first();
       }

       return $summary;
    }

    public function officeReports()
    {
        $summary = TrackingSummary::with('office', 'document')->where('office_id', auth()->user()->office_id)
        ->get();
        return $summary;
    }
}
