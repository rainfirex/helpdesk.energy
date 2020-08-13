<?php

namespace App\Http\Controllers;

use App\ScreenshotFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ControllerScreenshots extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index');
    }

    public function index($ticket_id) {
        $screens = ScreenshotFile::select('id', 'url', 'name', 'mime_type')->where('ticket_id', '=', $ticket_id)->get();
        return response()->json([
            'success' => true,
            'screens' => $screens
        ]);
    }
}
