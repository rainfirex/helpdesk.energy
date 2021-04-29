<?php namespace App\Http\Controllers\handler {

    use App\Mail\MailHandlerTicket;
    use App\Services\HandlerTicketService;
    use App\StatusTicket;
    use App\Ticket;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Validator;
    use App\Http\Controllers\Controller;

    class CHandlerTicket extends Controller
    {
        const LIMIT_ON_PAGE = 10;

        /**
         * @var HandlerTicketService
         */
        private $handlerTicketService;

        /**
         * ControllerHandlerTicket constructor.
         * @param HandlerTicketService $handlerTicketService
         */
        public function __construct(HandlerTicketService $handlerTicketService)
        {
            $this->middleware('auth:api')->only('index',
                'changeStatus','countPage','show','status',
                'countTypeTicket','resetNew', 'getTicketsOnStatus');
            $this->middleware('check.handler');
            $this->handlerTicketService = $handlerTicketService;
        }

        /**
         * @param int $page
         * @param string $type
         * @return JsonResponse
         */
        public function index(int $page = 1, string $type = 'all'): JsonResponse {
            $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;

            if ($type === 'all') {
                $count = Ticket::count();
                $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();
            } elseif ($type === 'new') {
                $count = Ticket::where('is_new', '=', true)->count();
                $tickets = Ticket::where('is_new', '=', true)->offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();
            }elseif ($type == 'performer'){
                $user = Auth::user();
                $count = Ticket::where('performer_user_id', '=', $user->id)->count();
                $tickets = Ticket::where('performer_user_id', '=', $user->id)->offset($offset)->limit(self::LIMIT_ON_PAGE)->orderBy('id', 'DESC')->get();
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
                'masterUser' => function($query) {
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
        }

        /**
         * Кол. страниц с заявками
         * @param string $type
         * @return \Illuminate\Http\JsonResponse
         */
        public function countPage(string $type): JsonResponse {
            if ($type === 'all'){
                $count = Ticket::count();
            } elseif ($type === 'new') {
                $count = Ticket::where('is_new', '=', true)->count();
            } elseif ($type === 'performer') {
                $user = Auth::user();
                $count = Ticket::where('performer_user_id', '=', $user->id)->count();
            } else {
                $st_ticket = StatusTicket::where('status', '=', $type)->first();
                $count = Ticket::where('status_id', '=', $st_ticket->id)->count();
            }

            $countPage = ceil( $count / self::LIMIT_ON_PAGE);

            return response()->json(['success' => true, 'count' => $countPage]);
        }

        /**
         * @param int $id
         * @return JsonResponse
         */
        public function show(int $id): JsonResponse {
            $ticket = Ticket::find($id)
                ->load([
                    'statusTicket' => function ($query) {

                    },
                    'user' => function ($query) {
                        $query->select('id', 'name', 'email', 'phone', 'title');
                    },
                    'performerUser' => function ($query) {
                        $query->select('id', 'name', 'email');
                    },
                    'masterUser' => function ($query) {
                        $query->select('id', 'email', 'name');
                    },
                ]);

            return response()->json(['success' => true, 'ticket'  => $ticket]);
        }

        /**
         * Смена статуса заявки
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function changeStatus(Request $request): JsonResponse {
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
            $ticket = $this->handlerTicketService->changeStatusTicket($request, $user);

            if (!empty($ticket)) {
                if (!empty($ticket->user->email)) {
                    Mail::to($ticket->user->email)->send(new MailHandlerTicket($ticket->user->name, $ticket->title, $ticket->number, $ticket->statusTicket->title, $user->name));
                }
                $response = ['success' => true, 'ticket' => $ticket];
            } else {
                $response = ['success' => false, 'message' => $this->handlerTicketService->getMessage()];
            }
            return response()->json($response);
        }

        /**
         * Получить состояние заявки (урезанная заявка)
         * @param int $id
         * @return JsonResponse
         */
        public function status(int $id): JsonResponse {
            $ticket = Ticket::select('id', 'status_id')
                ->find($id)
                ->load([
                    'statusTicket' => function ($query) {
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
        public function countTypeTicket(): JsonResponse {
            $countAll       = Ticket::count();
            $countPerformed = Ticket::where('status_id', '=', StatusTicket::ST_PERFORMED)->count();
            $countUntouched = Ticket::where('status_id', '=', StatusTicket::ST_UNTOUCHED)->count();
            $countRejected  = Ticket::where('status_id', '=', StatusTicket::ST_REJECTED)->count();
            $countCompleted = Ticket::where('status_id', '=', StatusTicket::ST_COMPLETED)->count();
            $performer      = Ticket::where('performer_user_id', '=', Auth::user()->id)->count();
            $countNew       = Ticket::where('is_new', '=', true)->count();

            return response()->json([
                'success' => true,
                'typeCount' => [
                    'all'       => $countAll,
                    'new'       => $countNew,
                    'performed' => $countPerformed,
                    'untouched' => $countUntouched,
                    'rejected'  => $countRejected,
                    'completed' => $countCompleted,
                    'performer' => $performer
                ]
            ]);
        }

        /**
         * Сброс флага is_new
         * @param int $id
         * @return JsonResponse
         */
        public function resetNew(int $id): JsonResponse {
            $ticket = Ticket::find($id);
            $ticket->is_new = false;
            $ticket->save();
            return response()->json(['success' => true]);
        }
    }
}