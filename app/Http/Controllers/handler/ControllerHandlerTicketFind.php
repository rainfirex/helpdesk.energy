<?php

namespace App\Http\Controllers\handler;

use App\Ticket;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class ControllerHandlerTicketFind extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index');
        $this->middleware('check.handler');
    }

    public function index(string $findText){

        $findText = trim(strtolower($findText));

        $user = Auth::user();

        if ($user) {
            $tickets = Ticket::where('title', 'like', "%$findText%")
                ->orWhere('number', 'like', "%$findText%")
                ->orWhere('description', 'like', "%$findText%")
                ->get();

            $tickets->load([
                'user' => function($query) {
                    $query->select('id', 'name', 'email', 'phone', 'department');
                },
                'statusTicket' => function($query) {
                    $query->select('id', 'status', 'title');
                },
                'performerUser' => function($query) {
                    $query->select('id','email', 'name', 'phone', 'title');
                }
            ]);

            return response()->json([
                'success' => true,
                'findText' => $findText,
                'tickets' => $tickets
            ]);
        }

        return response()->json([
           'success' => false,
           'message' => 'Вы не авторизованы'
        ]);
    }
}
