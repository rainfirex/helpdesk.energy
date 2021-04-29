<?php namespace App\Http\Controllers\user {

    use App\Mail\MailTicketCreate;
    use App\Services\UserTicketService;
    use App\StatusTicket;
    use App\Ticket;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Validator;

    use App\Http\Controllers\Controller;

    class CTicket extends Controller
    {
        private $userTicketService;
        const LIMIT_ON_PAGE = 10;

        public function __construct(UserTicketService $userTicketService)
        {
            $this->middleware('auth:api')
                ->only('index',
                    'store',
                    'show',
                    'pages',
                    'search');
            date_default_timezone_set("Asia/Sakhalin");
            $this->userTicketService = $userTicketService;
        }

        /**
         * Display a listing of the resource.
         *
         * @param int $page
         * @return JsonResponse
         */
        public function index(int $page = 1): JsonResponse
        {
            $user = Auth::user();
            $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_PERFORMED, StatusTicket::ST_UNTOUCHED])->count();
            $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;
            $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)
                ->select('id', 'category', 'number', 'status_id', 'title', 'created_at')
                ->where('user_id', $user->id)
                ->whereIn('status_id', [StatusTicket::ST_PERFORMED, StatusTicket::ST_UNTOUCHED])
                ->orderBy('id', 'DESC')
                ->get()
                ->load([
                    'statusTicket',
                    'isNewUserComment' => function ($query) {
                        $query->select('ticket_id');
                    }
                ]);

            return response()->json([
                'success' => true,
                'tickets' => $tickets,
                'count' => $count,
                'offset' => $offset
            ], 200);

        }

        /**
         * @param Request $request
         * @return JsonResponse
         */
        public function store(Request $request): JsonResponse
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
            $ticket = $this->userTicketService->storeTicket($request, $user);

            if (!empty($user->email)) {
                Mail::to($user->email)->send(new MailTicketCreate($user->name, $ticket->title, $ticket->number));
            }
            return response()->json(['success' => true, 'ticket_id' => $ticket->id, 'ticket_number' => $ticket->number]);
        }

        /**
         * Получить заявку по ID
         * @param int $id
         * @return JsonResponse
         */
        public function show(int $id): JsonResponse
        {
            $ticket = Ticket::find($id)
                ->load([
                    'statusTicket',
                    'performerUser' => function ($query) {
                        $query->select('id', 'name', 'email', 'phone', 'title');
                    }]);

            return response()->json([
                'success' => true,
                'ticket' => $ticket
            ], 200);
        }

        /**
         * Проверка состояния заявки по номеру
         * @param $number
         * @return \Illuminate\Http\JsonResponse
         */
        public function monitor($number): JsonResponse {
            $ticket = Ticket::where('number', $number)->first();

            if (!empty($ticket)) {
                $ticket->load([
                    'performerUser' => function ($query) {
                        $query->select('id', 'email', 'name', 'phone', 'title');
                    },
                    'statusTicket' => function ($query) {
                        $query->select('title', 'status', 'id');
                    },
                    'comments' => function ($query) {
                        $query->select('created_at', 'description', 'ticket_id');
                        $query->orderBy('created_at', 'desc');
                        $query->limit(8);
                    }]);
            }
            return response()->json(compact('ticket'));
        }

        /**
         * Получить состояние заявки (урезанная заявка)
         * @param $id
         * @return JsonResponse
         */
        public function status($id): JsonResponse {
            $ticket = Ticket::select('id', 'performer_user_id', 'status_id')
                ->find($id)
                ->load([
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
         * Количество страниц с заявками
         * @return JsonResponse
         */
        public function pages(): JsonResponse {
            $user = Auth::user();
            $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_UNTOUCHED, StatusTicket::ST_PERFORMED])->count();
            $countPage = ceil( $count / self::LIMIT_ON_PAGE);
            return response()->json([
                'success' => true,
                'count' => $countPage
            ]);
        }

        /**
         * Поиск заявок
         * @param string $findText
         * @return JsonResponse
         */
        public function search(string $findText): JsonResponse {
            $findText = trim(strtolower($findText));
            $user = Auth::user();

            $tickets = Ticket::where([
                ['user_id', '=', $user->id],
                ['title', 'like', "%$findText%"]
            ])->orWhere([
                ['user_id', '=', $user->id],
                ['number', 'like', "%$findText%"]
            ])->orWhere([
                ['user_id', '=', $user->id],
                ['description', 'like', "%$findText%"]
            ])
                ->get()
                ->load([
                    'statusTicket' => function ($query) {
                        $query->select('id', 'status', 'title');
                    },
                    'performerUser' => function ($query) {
                        $query->select('id', 'email', 'name', 'phone', 'title');
                    }
                ]);
            return  response()->json(['success' => true, 'tickets' => $tickets]);
        }
    }
}