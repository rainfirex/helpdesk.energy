<?php

namespace App\Http\Controllers\user;

use App\CommentTicket;
use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

class ControllerComment extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->only('index', 'store', 'show', 'resetNew');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ticket_id)
    {
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
                'description' => $request->input('description'),
                'is_handler' => false,
                'is_new'     => true
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
