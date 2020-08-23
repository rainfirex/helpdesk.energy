<?php

namespace App\Http\Controllers\handler;

use App\CommentTicket;
use App\Mail\MailHandlerTicket;
use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

class ControllerHandlerTicket extends Controller
{
    const LIMIT_ON_PAGE = 10;

    public function __construct()
    {
        $this->middleware('auth:api')->only('index',
            'changeStatus','countPage','show','state',
            'countTypeTicket','resetNew', 'getTicketsOnStatus');
        $this->middleware('check.handler');
    }

    /**
     * @param int $page
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $page = 1, string $type = 'all') {

        $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;

        if ($type === 'all') {

            $count = Ticket::count();

            $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();

        } elseif ($type === 'new') {

            $count = Ticket::where('is_new', '=', true)->count();

            $tickets = Ticket::where('is_new', '=', true)->offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();

        } else {

            $st_ticket = StatusTicket::where('status', '=', $type)->first();

            $count = Ticket::where('status_id', '=', $st_ticket->id)->count();

            $tickets = Ticket::where('status_id', '=', $st_ticket->id)->offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();

        }

        $tickets->load([
            'user' => function($query) {
                $query->select('id', 'name', 'email', 'phone', 'department');
            },
            'statusTicket' => function($query) {
                $query->select('id', 'status', 'title');
            },
            'performerUser' => function($query) {
                $query->select('id','email', 'name', 'phone', 'title');
            },
            'isNewHandlerComment'
        ]);

        return response()->json([
            'success' => true,
            'tickets' => $tickets,
            'count'   => $count,
            'offset' => $offset
        ]);


//        $count = Ticket::count();
//        $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;
//        $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();
//        $tickets->load([
//            'user' => function($query) {
//                $query->select('id', 'name', 'email', 'phone', 'department');
//            },
//            'statusTicket' => function($query) {
//                $query->select('id', 'status', 'title');
//            },
//            'performerUser' => function($query) {
//                $query->select('id','email', 'name', 'phone', 'title');
//            }
//            ]);
//        return response()->json([
//           'success' => true,
//           'tickets' => $tickets,
//           'count'   => $count,
//           'offset' => $offset
//        ]);
    }

    /**
     * Кол. страниц с заявками
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function countPage(string $type) {

        if ($type === 'all'){

            $count = Ticket::count();

        } elseif ($type === 'new') {

            $count = Ticket::where('is_new', '=', true)->count();

        } else {

            $st_ticket = StatusTicket::where('status', '=', $type)->first();

            $count = Ticket::where('status_id', '=', $st_ticket->id)->count();
        }

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
            }
        ]);

        return response()->json([
           'success' => true,
           'ticket'  => $ticket
        ]);
    }

    /**
     * Смена статуса заявки
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

            if ($ticket->status_id === StatusTicket::ST_COMPLETED ) // || $ticket->status_id === StatusTicket::ST_REJECTED
                return response()->json([
                    'success' => false,
                    'message' => 'Эта заявка уже завершена.'
                ]);

            $status = StatusTicket::find($request->input('status'));

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

            CommentTicket::create([
                'user_id'   => $user->id,
                'ticket_id' => $ticket->id,
                'description' => sprintf('Статус заявки изменен на: "%s"', $status->title),
                'is_handler'  => true,
                'is_new'      => true
            ]);

            if (!empty($ticket->user->email)) {
                Mail::to($ticket->user->email)->send(new MailHandlerTicket($ticket->user->name, $ticket->title, $ticket->number, $ticket->statusTicket->title, $user->name));
            }

            return response()->json([
                'success' => true,
                'ticket'  => $ticket
            ]);
        } else
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизирован!']);
    }

    /**
     * Получить состояние заявки (урезанная заявка)
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function state($id)
    {
        $ticket = Ticket::select('id', 'status_id')->find($id);
        $ticket->load([
            'statusTicket'  => function($query) {
                $query->select('id', 'status', 'title');
            }
        ]);

        return response()->json([
            'ticket' => $ticket,
            'success' => true
        ]);
    }

    /**
     * Кол-во заявок по статусам
     * @return \Illuminate\Http\JsonResponse
     */
    public function countTypeTicket(){
        $countAll       = Ticket::count();
        $countPerformed = Ticket::where('status_id', '=', StatusTicket::ST_PERFORMED)->count();
        $countUntouched = Ticket::where('status_id', '=', StatusTicket::ST_UNTOUCHED)->count();
        $countRejected  = Ticket::where('status_id', '=', StatusTicket::ST_REJECTED)->count();
        $countCompleted = Ticket::where('status_id', '=', StatusTicket::ST_COMPLETED)->count();
        $countNew       = Ticket::where('is_new', '=', true)->count();

        return response()->json([
            'success' => true,
            'typeCount' => [
                'all'       => $countAll,
                'new'       => $countNew,
                'performed' => $countPerformed,
                'untouched' => $countUntouched,
                'rejected'  => $countRejected,
                'completed' => $countCompleted
            ]
        ]);
    }

    /**
     * Сброс флага is_new
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetNew($id) {
        $ticket = Ticket::find($id);
        $ticket->is_new = false;
        $ticket->save();

        return response()->json([
            'success' => true
        ]);
    }

}
