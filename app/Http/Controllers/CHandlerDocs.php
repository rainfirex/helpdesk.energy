<?php

namespace App\Http\Controllers;


use App\DocFile;

class CHandlerDocs extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index');
    }

    public function index(int $ticket_id){
        $docs = DocFile::select('id', 'url', 'name', 'mime_type')->where('ticket_id', '=', $ticket_id)->get();
        return response()->json([
            'docs' => $docs
        ]);
    }
}