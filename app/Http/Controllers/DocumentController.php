<?php

namespace App\Http\Controllers;

use App\Events\NewDocumentHasAddedEvent;
use App\Http\Requests\DocumentPostRequest;
use Auth;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Document;
use App\Models\DocumentType;

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

    public function addNewDocument(Document $document, DocumentPostRequest $request)
    {
        $document->updateOrCreate(
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
