<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControllerTicketFind extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index');
    }

    public function index(string $findText) {

        $findText = trim(strtolower($findText));

        $user = Auth::user();

        if (empty($user)){
            return response()->json([
                'success' => false,
                'message' => 'Вы не авторизованы'
            ]);
        }

        $tickets = Ticket::where([
            ['user_id', '=', $user->id],
            ['title', 'like', "%$findText%"]
        ])->orWhere([
            ['user_id', '=', $user->id],
            ['number', 'like', "%$findText%"]
        ])->orWhere([
          ['user_id', '=', $user->id],
          ['description', 'like', "%$findText%"]
        ])->get();

        $tickets->load([
//            'user' => function($query) {
//                $query->select('id', 'name', 'email', 'phone', 'department');
//            },
            'statusTicket' => function($query) {
                $query->select('id', 'status', 'title');
            },
            'performerUser' => function($query) {
                $query->select('id','email', 'name', 'phone', 'title');
            }
        ]);

        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }
}
