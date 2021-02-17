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

            $user_id = Auth::user()->id;

        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
        } catch (\Exception $error) {
            DB::rollback();
            throw $error;
        }
        DB::commit();

        $document->document_recipient->where('destination_office', auth()->user()->office->id)->first()->update([
            'received' => true
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
            $tracking_record->action = 'forwarded';
            $tracking_record->through = $request->through;
            $tracking_record->approved_by = $request->approved_by;
            $tracking_record->touched_by = Auth::user()->id;
            $tracking_record->last_touched = Carbon::now();
            $tracking_record->forwarded_by = $request->forwarded_by;
            $tracking_record->forwarded_to = $request->forwarded_to;
            $tracking_record->remarks = $request->documentRemarks;
            $tracking_record->save();

        } catch (ValidationException $error) {
            DB::rollback();
            throw $error;
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

    public function terminateDocument(Request $request)
    {
        $remarks = $request->documentRemarks;
        $approved_by = $request->approved_by;
        $subject = $request->subject;

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

            $admin = auth()->user()->isAdmin();

            DocumentRecipient::where(['document_id' => $request->id,
                                      'destination_office' => auth()->user()->office->id])->delete();

            if($admin){
                $tracking_record->document->update(['status' => 'terminated']);
                $tracking_record->document->delete();
            }

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

    public function acknowledgeDocument(Document $document, Request $request)
    {
        $document->update(['priority_level' => $request->priority_levels ]);

        DocumentRecipient::whereIn('recipient_id', $document->document_recipient->pluck('recipient_id'))
            ->update(['acknowledged' => 1]);

        $remarks = $request->remarks;
        $subject = $request->subject;

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
            $tracking_record->document->update(['priority_level' => $request->priority_levels]);

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
        $status = $request->hold_reject;
        $subject = $request->subject;

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
                [ 'document_id' => $document->id, 'destination_office' => $office->id ]);
        };

       DocumentRecipient::whereDocumentId($document->id)
            ->whereIn('destination_office', $diff->toArray())->forceDelete();

       TrackingRecord::create([
            'action' => 'created',
            'document_id' => $document->id,
            'touched_by' => auth()->user()->id,
            'remarks' => $document->remarks,
            'last_touched' => Carbon::now()
       ]);

       return $document;
    }

    public function trackingReports() {
        $documents = Document::withTrashed()
            ->with('tracking_records', 'tracking_records.user', 'tracking_records.user.office')
            ->get();
        $office = collect($documents)->groupBy('tracking_records.user.office');
        return $documents;
    }
}
