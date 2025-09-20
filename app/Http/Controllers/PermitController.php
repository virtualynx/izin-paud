<?php

namespace App\Http\Controllers;

use App\Models\Masters\DoctypeRequirement;
use App\Models\TrxPermitDecree;
use App\Models\TrxRequest;
use App\Models\TrxRequestDocument;
use App\Services\PermitService;
use App\Services\UserService;
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

    public function request_list(){
        return view('pages.permit.request_list');
    }

    public function verify_list(){
        return view('pages.permit.verify_list');
    }

    public function approval_list(){
        return view('pages.permit.approval_list');
    }

    public function decree_list(){
        return view('pages.permit.decree_list');
    }

    public function page_verify($req_id, UserService $userService, PermitService $permitService){
        $params = request()->all();

        $user = userinfo();

        $is_approver = false;
        if(!empty($params['mode']) && $params['mode'] == '1'){
            if(!is_approver()){
                abort(403);
            }

            $req_approval_map = $permitService->getRequestApprovalMap([$req_id]);
            if($req_approval_map[$req_id] == null){
                abort(403);
            }

            $user = userinfo();
            $mainPosition = $userService->getMainPosition($user->user_id);
            if($req_approval_map[$req_id]->approver_position_id != $mainPosition->position_id){
                abort(403);
            }

            $is_approver = true;
        }

        $request = TrxRequest::query()
            ->with('documents.doctype')
            ->with('revision_note')
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

        return view('pages.permit.verify_and_approve', [
            'request' => $request,
            'documents' => $documents,
            'is_approver' => $is_approver,
            'mode' => $params['mode']?? 0
        ]);
    }

    public function page_edit($req_id){
        $params = request()->all();
        $user = userinfo();

        $request = TrxRequest::query()
            ->with('documents.doctype')
            ->with('documents.revision_note')
            ->with('revision_note')
            ->where('is_disabled', 0)
            ->where('req_id', $req_id)
            ->first();

        $transformed_documents = [];
        foreach($request->documents as $doc){
            $path_arr = explode("/", $doc['file_path']);
            $filename = $path_arr[count($path_arr)-1];
            $doc = json_decode(json_encode($doc), true);
            $doc['filename'] = $filename;
            $doc['mime'] = Storage::disk()->mimeType($doc['file_path']);
            $transformed_documents []= $doc;
        }

        $transformed_documents = json_decode(json_encode($transformed_documents));

        return view('pages.permit.edit_and_revision', [
            'request' => $request,
            'documents' => $transformed_documents,
            'is_revision' => false
        ]);
    }

    public function page_revision($req_id){
        $params = request()->all();
        $user = userinfo();

        $request = TrxRequest::query()
            ->with('documents.doctype')
            ->with('documents.revision_note')
            ->with('revision_note')
            ->where('is_disabled', 0)
            ->where('req_id', $req_id)
            ->first();

        $filteredDocuments = [];
        foreach($request->documents as $doc){
            if(empty($doc->revision_note)){
                continue;
            }

            $path_arr = explode("/", $doc['file_path']);
            $filename = $path_arr[count($path_arr)-1];
            $doc = json_decode(json_encode($doc), true);
            $doc['filename'] = $filename;
            $doc['mime'] = Storage::disk()->mimeType($doc['file_path']);
            $filteredDocuments []= $doc;
        }

        $filteredDocuments = json_decode(json_encode($filteredDocuments));

        return view('pages.permit.edit_and_revision', [
            'request' => $request,
            'documents' => $filteredDocuments,
            'is_revision' => true
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

        // $headers = [
        //     'Content-Type' => Storage::disk()->mimeType($req_doc->file_path),
        //     'Content-Disposition' => $contentDisposition,
        //     'Cache-Control' => 'public, max-age=31536000', // 1 year
        //     'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000),
        //     'Last-Modified' => gmdate('D, d M Y H:i:s \G\M\T', $lastModified),
        //     'ETag' => $etag,
        // ];

        $headers = [
            'Content-Type' => Storage::disk()->mimeType($req_doc->file_path),
            'Content-Disposition' => $contentDisposition,
            'Cache-Control' => 'must-revalidate', // Force revalidation with ETag
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s \G\M\T', $lastModified),
        ];

        return Storage::disk()->response($req_doc->file_path, null, $headers);
    }

    public function decree_preview($permit_decree_id){
        $params = request()->all();

        $user = userinfo();

        $decree = TrxPermitDecree::query()
            ->where('is_disabled', 0)
            ->where('permit_decree_id', $permit_decree_id)
            ->first();

        $path_arr = explode("/", $decree->file_path);
        $filename = $path_arr[count($path_arr)-1];

        $filePath = Storage::disk()->path($decree->file_path);
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
            'Content-Type' => Storage::disk()->mimeType($decree->file_path),
            'Content-Disposition' => $contentDisposition,
            'Cache-Control' => 'public, max-age=31536000', // 1 year
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000),
            'Last-Modified' => gmdate('D, d M Y H:i:s \G\M\T', $lastModified),
            'ETag' => $etag,
        ];

        return Storage::disk()->response($decree->file_path, null, $headers);
    }
}
