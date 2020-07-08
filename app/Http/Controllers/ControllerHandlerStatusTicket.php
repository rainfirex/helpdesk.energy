<?php

namespace App\Http\Controllers;

use App\StatusTicket;
use Illuminate\Http\Request;

class ControllerHandlerStatusTicket extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth:api')->only('index');
    }

    public function index() {

        $statusTicket = StatusTicket::all();

        return response()->json([
            'success'=> true,
            'statusTicket' => $statusTicket
        ]);
    }
}
