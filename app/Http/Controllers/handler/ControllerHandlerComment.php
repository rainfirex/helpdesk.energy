<?php

namespace App\Http\Controllers\handler;

use App\CommentTicket;
use App\Mail\MailHandlerCommentNew;
use App\Mail\MailHandlerTicket;
use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

class ControllerHandlerComment extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index','store', 'show', 'resetNew');
        $this->middleware('check.handler');
    }

    public function index($ticket_id){
        $user = Auth::user();

        if ($user) {
            $comments = CommentTicket::select('id', 'created_at','description','user_id', 'is_handler', 'is_new')
                ->where('ticket_id', $ticket_id)
                ->orderBy('created_at', 'DESC')
                ->get();

            $comments->load(['user' => function($query){
                $query->select('id', 'name', 'title');
            }]);

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
                'user_id'     => $user->id,
                'ticket_id'   => $request->input('ticket_id'),
                'description' => trim($request->input('description')),
                'is_handler'  => true,
                'is_new'      => true
            ]);


            if (!empty($ticket->user->email)) {
                Mail::to($ticket->user->email)->send(new MailHandlerCommentNew($ticket->user->name, $ticket->title, $ticket->number, $user->name, trim($request->input('description') )));
            }


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

            $comment->load(['user' => function($query){
                $query->select('id', 'name', 'title');
            }]);


            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);
        }
        else
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизирован!']);
    }

    /**
     * Сбросить флаг new
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetNew($id) {
        $comment = CommentTicket::find($id);
        $comment->is_new = false;
        $comment->save();

        return response()->json([
            'success' => true
        ]);
    }
}
