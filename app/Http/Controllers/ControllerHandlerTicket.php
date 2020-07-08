<?php

namespace App\Http\Controllers;

use App\CommentTicket;
use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ControllerHandlerTicket extends Controller
{
    const LIMIT_ON_PAGE = 5;

    public function __construct()
    {
        return $this->middleware('auth:api')->only('index', 'changeStatus');
    }

    public function index(int $page = 1) {

        $count = Ticket::count();
        $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;

        $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();

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
           'tickets' => $tickets,
           'count'   => $count,
           'offset' => $offset
        ]);
    }

    public function countPage() {
        $count = Ticket::count();
        $countPage = ceil( $count / self::LIMIT_ON_PAGE);
        return response()->json([
            'success' => true,
            'count' => $countPage
        ]);
    }

    public function show($id) {

        $ticket = Ticket::find($id);
        $ticket->load([
            'statusTicket' => function($query) {

            },
            'user' => function($query) {
                $query->select('id', 'name', 'email', 'phone', 'title');
            },
            'comments' => function($query) {
                $query->select('ticket_id', 'description', 'created_at', 'user_id');
                $query->orderBy('id', 'DESC');
            }
        ]);

        return response()->json([
           'success' => true,
           'ticket'  => $ticket
        ]);
    }

    public function changeStatus( Request $request) {

        $validator = Validator::make([
            'ticket_id' => $request->input('ticket_id'),
            'status'    => $request->input('status')
        ], [
            'ticket_id' => 'required|integer',
            'status'    => 'required'
        ], [
            'ticket_id.required' => 'Не указан id заявки',
            'ticket_id.integer'  => 'Не верный id заявки',
            'status.required'    => 'Не указан статус.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->all()]);
        }

        $user = Auth::user();

        if ($user) {
            $ticket = Ticket::find($request->input('ticket_id'));
            if ($ticket->status_id === StatusTicket::ST_COMPLETED || $ticket->status_id === StatusTicket::ST_REJECTED)
                return response()->json([
                    'success' => false,
                    'message' => 'Эта заявка уже завершена.'
                ]);

            $status = StatusTicket::find($request->input('status'));

            $comment = CommentTicket::create([
                'user_id'   => $user->id,
                'ticket_id' => $ticket->id,
                'description' => sprintf('Статус заявки изменен на: "%s"', $status->title)
            ]);

            $ticket->status_id = $status->id;
            $ticket->performer_user_id = $user->id;
            $ticket->save();

            $ticket->load([
                'statusTicket' => function($query) {

                },
                'user' => function($query) {
                    $query->select('id', 'name', 'email', 'phone', 'title');
                },
                'comments' => function($query) {
                    $query->select('ticket_id', 'description', 'created_at', 'user_id');
                    $query->orderBy('id', 'DESC');
                }
            ]);

            return response()->json([
                'success' => true,
                'ticket'  => $ticket
            ]);
        }

    }
}
