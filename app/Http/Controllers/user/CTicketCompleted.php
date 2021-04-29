<?php namespace App\Http\Controllers\user {

    use App\CommentTicket;
    use App\Mail\MailTicketCompleted;
    use App\StatusTicket;
    use App\Ticket;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Validator;

    class CTicketCompleted extends Controller
    {
        const LIMIT_ON_PAGE = 10;

        /**
         * ControllerTicketCompleted constructor.
         */
        public function __construct()
        {
            $this->middleware('auth:api')->only('index', 'pages', 'complete');
        }

        /**
         * * Display a listing of the resource.
         *
         * @param int $page
         * @return \Illuminate\Http\JsonResponse
         */
        public function index(int $page = 1): JsonResponse
        {
            $user = Auth::user();
            $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])->count();
            $offset = ($page * self::LIMIT_ON_PAGE) - self::LIMIT_ON_PAGE;
            $tickets = Ticket::offset($offset)->limit(self::LIMIT_ON_PAGE)
                ->select('id', 'category', 'number', 'status_id', 'title', 'created_at')
                ->where('user_id', $user->id)
                ->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])
                ->orderBy('id', 'DESC')
                ->get()
                ->load('statusTicket');

            return response()->json([
                'success' => true,
                'tickets' => $tickets,
                'count' => $count,
                'offset' => $offset
            ], 200);
        }

        /**
         * @return JsonResponse
         */
        public function pages(): JsonResponse {
            $user = Auth::user();
            $count = Ticket::where('user_id', $user->id)->whereIn('status_id', [StatusTicket::ST_COMPLETED, StatusTicket::ST_REJECTED])->count();
            $countPage = ceil( $count / self::LIMIT_ON_PAGE);
            return response()->json([
                'success' => true,
                'count' => $countPage
            ]);
        }

        /**
         * @param Request $request
         * @param int $id
         * @return JsonResponse
         */
        public function complete(Request $request, int $id): JsonResponse {
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
            $ticket = Ticket::find($id);
            if ($ticket->status_id === StatusTicket::ST_COMPLETED || $ticket->status_id === StatusTicket::ST_REJECTED)
                return response()->json([
                    'success' => false,
                    'message' => 'Эта заявка уже завершена.'
                ]);

            $comment = CommentTicket::create([
                'user_id' => $user->id,
                'ticket_id' => $id,
                'description' => '(Заявка завершена автором) ' . $request->input('description'),
                'is_handler' => false,
                'is_new' => true
            ]);

            $ticket->status_id = StatusTicket::ST_COMPLETED;
            $ticket->save();

            if (!empty($user->email)) {
                Mail::to($user->email)->send(new MailTicketCompleted($user->name, $ticket->title, $ticket->number));
            }

            return response()->json(['success' => true, 'comment_id' => $comment->id]);
        }
    }
}