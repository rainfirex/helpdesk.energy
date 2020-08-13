<?php

namespace App\Http\Controllers\user;

use App\CommentTicket;
use App\Mail\FeedMail;
use App\Mail\MailTicketCompleted;
use App\Mail\MailTicketCreate;
use App\ScreenshotFile;
use App\StatusTicket;
use App\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;

class ControllerTicket extends Controller
{
    const LIMIT_ON_PAGE = 10;

    public function __construct()
    {
        $this->middleware('auth:api')->only('index', 'store', 'update', 'show', 'countPage');
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $page
     * @return JsonResponse
     */
    public function index(int $page = 1)
    {
        // - untouched, performed

        $user = Auth::user();

        if ($user) {

            $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_PERFORMED, StatusTicket::ST_UNTOUCHED])->count();
            $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;

            $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)
                ->select('id','category', 'number', 'status_id', 'title', 'created_at')
                ->where('user_id', $user->id)
                ->whereIn('status_id', [StatusTicket::ST_PERFORMED, StatusTicket::ST_UNTOUCHED])
                ->orderBy('id', 'DESC')
                ->get();

//            $tickets = Ticket::select('id', 'category', 'number', 'status_id', 'title', 'created_at')
//                ->where('user_id', $user->id)
//                ->whereIn('status_id', [StatusTicket::ST_PERFORMED, StatusTicket::ST_UNTOUCHED])
//                ->orderBy('id', 'DESC')
//                ->get();

            $tickets->load('statusTicket');

            return response()->json([
                'success' => true,
                'tickets' => $tickets,
                'count'   => $count,
                'offset'  => $offset
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
                'status_id'   => StatusTicket::ST_UNTOUCHED,
                'is_new'      => true
            ]);

            $files = $request->file();

            if (!empty($files)) {

                $dirStorageApp = storage_path('app/public/');
                $dirScreenshots = 'screenshots/';
                $dirToday = $dirStorageApp.$dirScreenshots.date('d.m.Y');

                if (!file_exists($dirStorageApp.$dirScreenshots)) {
                    mkdir($dirStorageApp.$dirScreenshots);
                    chmod($dirStorageApp.$dirScreenshots, 0777);
                }

                if (!file_exists($dirToday)) {
                    mkdir($dirToday);
                    chmod($dirToday, 0777);
                }

                foreach ($files as $file){
                    if (in_array($file->extension(),['png', 'jpg', 'jpeg'])) {
                        $path = $file->store('public/'.$dirScreenshots.date('d.m.Y'));
                        chmod(storage_path('app/').$path, 0777);

                        ScreenshotFile::create([
                            'path' => $path,
                            'url' => asset('storage/screenshots/'.date('d.m.Y').'/'.$file->hashName()),
                            'name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'user_id'   => $user->id,
                            'ticket_id' => $ticket->id
                        ]);
                    }
                }
            }

            if (!empty($user->email)) {
                Mail::to($user->email)->send(new MailTicketCreate($user->name, $ticket->title, $ticket->number));
            }

            return response()->json(['success' => true, 'ticket_id' => $ticket->id, 'ticket_number' => $ticket->number]);
        } else
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизирован!']);
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
                    }]);

                return response()->json([
                    'success' => true,
                    'ticket'  => $ticket
                ], 200);
            }
        }

        return response()->json(['success' => false, 'message' => 'Пользователь не авторизирован!']);
    }

    /**
     * Проверка состояния заявки по номеру
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
     * Получить состояние заявки (урезанная заявка)
     * @param $id
     * @return JsonResponse
     */
    public function state($id)
    {
        $ticket = Ticket::select('id', 'performer_user_id', 'status_id')->find($id);
        $ticket->load([
            'statusTicket'  => function($query) {
                $query->select('id', 'status', 'title');
            },
            'performerUser' => function($query) {
                $query->select('id', 'name', 'email', 'phone', 'department', 'title');
            }
        ]);

        return response()->json([
            'ticket' => $ticket,
            'success' => true
        ], 200);
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

            if (!empty($user->email)) {
                Mail::to($user->email)->send(new MailTicketCompleted($user->name, $ticket->title, $ticket->number));
            }

            return response()->json([
                'success' => true,
                'comment_id' => $comment->id
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Пользователь не авторизирован!']);
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
     * @return JsonResponse
     */
    public function countPage(){

        $user = Auth::user();

        $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_UNTOUCHED, StatusTicket::ST_PERFORMED])->count();
        $countPage = ceil( $count / self::LIMIT_ON_PAGE);
        return response()->json([
            'success' => true,
            'count' => $countPage
        ]);
    }
}
