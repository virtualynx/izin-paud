<?php

namespace App\Http\Controllers;

use App\Models\TrxRequest;
use App\Models\TrxRequestDocument;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PermitController extends Controller
{
    public function __construct()
    {
    }

    public function index(){
        return view('pages.home');
        // return redirect('docs');
    }

    public function page_request(){
        return view('pages.permit.request');
    }

    public function page_verification(){
        return view('pages.permit.verification');
    }

    public function page_verify($req_id){
        $request = TrxRequest::query()
            ->with('documents.doctype')
            ->where('is_disabled', 0)
            ->where('req_id', $req_id)
            ->first();

        $documents = json_decode(json_encode($request->documents), true);

        foreach($documents as &$doc){
            $doc['mime'] = Storage::disk()->mimeType($doc['file_path']);
        }
        unset($doc);

        $documents = json_decode(json_encode($documents));

        return view('pages.permit.verify', [
            'request' => $request,
            'documents' => $documents
        ]);
    }

    public function document_preview($req_doc_id){
        $user = userinfo();

        $req_doc = TrxRequestDocument::query()
            ->where('is_disabled', 0)
            ->where('req_doc_id', $req_doc_id)
            ->first();

        $path_arr = explode("/", $req_doc->file_path);
        $filename = $path_arr[count($path_arr)-1];

        return Storage::disk()->response($req_doc->file_path, null, [
            'Content-Type' => Storage::disk()->mimeType($req_doc->file_path),
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
