<?php

namespace App\Http\Controllers;

use App\CommentTicket;
use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ControllerTicket extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index', 'store', 'update', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status = null)
    {
        //1 - untouched, performed
        //2 - completed, rejected

        $user = Auth::user();

        if ($user) {

            switch ($status) {
                case 1:
                    $tickets = Ticket::select('id','category', 'number', 'status_id', 'title', 'created_at')
                        ->where('user_id', $user->id)
                        ->whereIn('status_id', [StatusTicket::ST_PERFORMED, StatusTicket::ST_UNTOUCHED])
//                ->where([
//                    ['status_id', StatusTicket::ST_COMPLETED],
//                    ['status_id', StatusTicket::ST_UNTOUCHED]
//                ])
                        ->orderBy('id', 'DESC')
                        ->get();
                    break;
                case 2:
                    $tickets = Ticket::select('id','category', 'number', 'status_id', 'title', 'created_at')
                        ->where('user_id', $user->id)
                        ->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])
                        ->orderBy('id', 'DESC')
                        ->get();
                    break;
                default:
                    $tickets = Ticket::select('id','category', 'number', 'status_id', 'title', 'created_at')->where('user_id', $user->id)->orderBy('id', 'DESC')->get();
            }



//            $tickets = Ticket::select('id','category', 'number', 'status_id', 'title', 'created_at')->where('user_id', $user->id)->orderBy('id', 'DESC')->get();
            $tickets->load('statusTicket');

            return response()->json([
                'success' => true,
                'tickets' => $tickets
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
            'phone'       => $request->input('phone'),
            'department'  => $request->input('department'),
            'title'       => $request->input('title'),
            'category'    => $request->input('category'),
            'description' => $request->input('description')
        ], [
            'phone'      => 'required',
            'department' => 'required',
            'title'      => 'required',
            'category'   => 'required',
            'description'=> 'required'
        ], [
            'phone.required'       => 'Не указан номер телефона.',
            'department.required'  => 'Не указан отдел.',
            'title.required'       => 'Не указан заголовок.',
            'category.required'    => 'Не выбрана категория.',
            'description.required' => 'Не указано описание.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->all()]);
        }

        $user = Auth::user();

        if ($user) {

            $ticket = Ticket::create([
                'user_id'     => $user->id,
                'number'      => uniqid('ticket.', true),
                'phone'       => $request->input('phone'),
                'department'  => $request->input('department'),
                'category'    => $request->input('category'),
                'title'       => $request->input('title'),
                'description' => $request->input('description'),
                'status_id'   => StatusTicket::ST_UNTOUCHED
            ]);

            return response()->json(['success' => true, 'ticket_id' => $ticket->id, 'ticket_number' => $ticket->number]);
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
            if ($id) {
                $ticket = Ticket::find($id);
                $ticket->load([
                    'statusTicket',
                    'performerUser' => function($query) {
                        $query->select('id','name', 'email', 'phone', 'title');
                    }
                    ]);

                return response()->json([
                    'success' => true,
                    'ticket'  => $ticket
                ], 200);
            }
        }
    }

    /**
     * Проверка состояния заявки
     * @param $number
     * @return \Illuminate\Http\JsonResponse
     */
    public function check($number): JsonResponse{

        $ticket = Ticket::where('number', $number)->first();

            if ($ticket) {
                $ticket->load([
                    'performerUser' => function($query) {
                        $query->select('id','email', 'name', 'phone', 'title');
                    },
                    'statusTicket' => function($query){
                        $query->select('title','status', 'id');
                    },
                    'comments' => function($query) {
                        $query->select('created_at', 'description', 'ticket_id');
                        $query->orderBy('created_at', 'desc');
                        $query->limit(4);
                }]);
            }

            return response()->json([
                'success' => true,
                'ticket'  => $ticket
            ]);
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
        $validator = Validator::make([
            'ticket_id'   => $id,
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


            $ticket = Ticket::find($id);
            if ($ticket->status_id === StatusTicket::ST_COMPLETED || $ticket->status_id === StatusTicket::ST_REJECTED)
                return response()->json([
                    'success' => false,
                    'message' => 'Эта заявка уже завершена.'
                ]);


            $comment = CommentTicket::create([
                'user_id'   => $user->id,
                'ticket_id' => $id,
                'description' => '(Заявка завершена вами) '. $request->input('description')
            ]);

            $ticket->status_id = StatusTicket::ST_COMPLETED;
            $ticket->save();

            return response()->json([
                'success' => true,
                'comment_id' => $comment->id
            ]);
        }
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
}
