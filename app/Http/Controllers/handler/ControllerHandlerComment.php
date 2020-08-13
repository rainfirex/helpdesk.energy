<?php

namespace App\Http\Controllers\handler;

use App\CommentTicket;
use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

class ControllerHandlerComment extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index','store', 'show');
        $this->middleware('check.handler');
    }

    public function index($ticket_id){
        $user = Auth::user();

        if ($user) {
            $comments = CommentTicket::select('created_at','description','user_id')
                ->where('ticket_id', $ticket_id)
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'success' => true,
                'comments' => $comments
            ], 200);
        }
    }

    public function store(Request $request){
        $validator = Validator::make([
            'ticket_id' => $request->input('ticket_id'),
            'description' => $request->input('description')
        ], [
            'ticket_id'   => 'required|integer',
            'description' => 'required'
        ], [
            'ticket_id.required'   => 'Не указан id заявки',
            'ticket_id.integer'    => 'Не верный id заявки',
            'description.required' => 'Не указан комментарий.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->all()]);
        }

        $user = Auth::user();
        if($user) {

            $ticket = Ticket::find($request->input('ticket_id'));
            if ($ticket->status_id === StatusTicket::ST_COMPLETED || $ticket->status_id === StatusTicket::ST_REJECTED)
                return response()->json([
                    'success' => false,
                    'message' => 'Эту заявку нельзя комментировать.'
                ]);

            $comment = CommentTicket::create([
                'user_id'   => $user->id,
                'ticket_id' => $request->input('ticket_id'),
                'description' => $request->input('description')
            ]);

            return response()->json([
                'success' => true,
                'comment_id' => $comment->id
            ], 200);
        }
        else {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизирован!']);
        }
    }

    public function show($id) {
        $user = Auth::user();
        if ($user) {

            $comment = CommentTicket::find($id);

            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);
        }
        else
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизирован!']);
    }
}