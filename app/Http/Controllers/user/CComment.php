<?php namespace App\Http\Controllers\user {

    use App\CommentTicket;
    use App\StatusTicket;
    use App\Ticket;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Validator;
    use App\Http\Controllers\Controller;

    class CComment extends Controller
    {
        /**
         * ControllerComment constructor.
         */
        public function __construct()
        {
            $this->middleware('auth:api')->only('index', 'store', 'show', 'resetNew');
        }

        /**
         * @param int $ticket_id
         * @return JsonResponse
         */
        public function index(int $ticket_id): JsonResponse
        {
            $comments = CommentTicket::select('id', 'created_at', 'description', 'user_id', 'is_handler', 'is_new')
                ->where('ticket_id', $ticket_id)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->load(['user' => function ($query) {
                    $query->select('id', 'name', 'title');
                }]);

            return response()->json(['success' => true, 'comments' => $comments], 200);
        }

        /**
         * @param Request $request
         * @return JsonResponse
         */
        public function store(Request $request): JsonResponse
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
            $ticket = Ticket::find($request->input('ticket_id'));
            if ($ticket->status_id === StatusTicket::ST_COMPLETED || $ticket->status_id === StatusTicket::ST_REJECTED){
                $response = ['success' => false, 'message' => 'Эту заявку нельзя комментировать.'];
            }else{
                $comment = CommentTicket::create([
                    'user_id' => $user->id,
                    'ticket_id' => $request->input('ticket_id'),
                    'description' => $request->input('description'),
                    'is_handler' => false,
                    'is_new' => true
                ]);

                $response = ['success' => true, 'comment_id' => $comment->id];
            }
            return response()->json($response, 200);
        }

        /**
         * @param int $id
         * @return JsonResponse
         */
        public function show(int $id): JsonResponse
        {
            $comment = CommentTicket::find($id);
            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);
        }

        /**
         * Сбросить флаг new
         * @param int $id
         * @return JsonResponse
         */
        public function resetNew(int $id): JsonResponse {
            $comment = CommentTicket::find($id);
            $comment->is_new = false;
            $comment->is_read = true;
            $comment->save();
            return response()->json(['success' => true]);
        }
    }
}