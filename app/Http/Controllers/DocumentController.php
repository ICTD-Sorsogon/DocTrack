<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentPostRequest;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use DB;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Log;
use Illuminate\Http\Request;
use App\Models\TrackingRecord;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use phpDocumentor\Reflection\Types\Boolean;

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

    public function addNewDocument(Document $document, DocumentPostRequest $request): Boolean
    {
        $document->updateOrCreate(
            ['id' => $document->id],
            $request->validated()
        );

        return true;
        /**
         * KENNETH SOLOMON
         * TODO after save or update, dipatch events user logs and doc logs
         * PLEASE USE LARAVEL EVENTS LIKE HERE https://laravel.com/docs/8.x/events
         */
    }
}
