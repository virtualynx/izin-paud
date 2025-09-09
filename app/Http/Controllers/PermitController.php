<?php

namespace App\Http\Controllers;

use App\Models\TrxRequest;
use App\Models\TrxRequestDocument;
use Illuminate\Routing\Controller;
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
            ->with('latest_revision_note')
            ->where('is_disabled', 0)
            ->where('req_id', $req_id)
            ->first();

        $documents = json_decode(json_encode($request->documents), true);

        foreach($documents as &$doc){
            $path_arr = explode("/", $doc['file_path']);
            $filename = $path_arr[count($path_arr)-1];
            $doc['filename'] = $filename;

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
        $params = request()->all();

        $user = userinfo();

        $req_doc = TrxRequestDocument::query()
            ->where('is_disabled', 0)
            ->where('req_doc_id', $req_doc_id)
            ->first();

        $path_arr = explode("/", $req_doc->file_path);
        $filename = $path_arr[count($path_arr)-1];

        $filePath = Storage::disk()->path($req_doc->file_path);
        $lastModified = filemtime($filePath);
        $etag = md5_file($filePath);

        // Check if client has cached version
        $ifModifiedSince = request()->header('If-Modified-Since');
        $ifNoneMatch = request()->header('If-None-Match');
        
        if (($ifModifiedSince && strtotime($ifModifiedSince) === $lastModified) || 
            ($ifNoneMatch && $ifNoneMatch === $etag)) {
            return response()->json(null, 304); // Not Modified
        }

        // Tentukan Content-Disposition berdasarkan action
        $contentDisposition = !empty($params['action']) && $params['action'] == 'download' 
            ? 'attachment; filename="' . $filename . '"' // Force download
            : 'inline; filename="' . $filename . '"';    // Preview in browser

        $headers = [
            'Content-Type' => Storage::disk()->mimeType($req_doc->file_path),
            'Content-Disposition' => $contentDisposition,
            'Cache-Control' => 'public, max-age=31536000', // 1 year
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000),
            'Last-Modified' => gmdate('D, d M Y H:i:s \G\M\T', $lastModified),
            'ETag' => $etag,
        ];

        return Storage::disk()->response($req_doc->file_path, null, $headers);
    }
}
