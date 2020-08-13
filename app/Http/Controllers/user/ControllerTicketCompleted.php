<?php

namespace App\Http\Controllers\user;

use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class ControllerTicketCompleted extends Controller
{
    const LIMIT_ON_PAGE = 10;

    public function __construct()
    {
        $this->middleware('auth:api')->only('index', 'store', 'update', 'show', 'countPage');
    }

    /**
     * * Display a listing of the resource.
     *
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $page = 1)
    {
        // completed, rejected

        $user = Auth::user();

        if ($user) {

            $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])->count();
            $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;

            $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)
                ->select('id','category', 'number', 'status_id', 'title', 'created_at')
                ->where('user_id', $user->id)
                ->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])
                ->orderBy('id', 'DESC')
                ->get();

//            $tickets = Ticket::select('id','category', 'number', 'status_id', 'title', 'created_at')
//                ->where('user_id', $user->id)
//                ->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])
//                ->orderBy('id', 'DESC')
//                ->get();

            $tickets->load('statusTicket');

            return response()->json([
                'success' => true,
                'tickets' => $tickets,
                'count'   => $count,
                'offset' => $offset
            ], 200);
        } else {
            return  response()->json([
                'success' => false
            ]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function countPage() {

        $user = Auth::user();

        $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])->count();
        $countPage = ceil( $count / self::LIMIT_ON_PAGE);
        return response()->json([
            'success' => true,
            'count' => $countPage
        ]);
    }
}
