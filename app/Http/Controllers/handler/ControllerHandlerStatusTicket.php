<?php

namespace App\Http\Controllers\handler;

use App\StatusTicket;

use App\Http\Controllers\Controller;

class ControllerHandlerStatusTicket extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index');
        $this->middleware('check.handler');
    }

    public function index() {

        $statusTicket = StatusTicket::all();

        return response()->json([
            'success'=> true,
            'statusTicket' => $statusTicket
        ]);
    }
}
