<?php namespace App\Http\Controllers\handler {

    use App\CommentTicket;
    use App\Mail\MailHandlerCommentNew;
    use App\StatusTicket;
    use App\Ticket;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Mail;
    use Illuminate\Support\Facades\Validator;
    use App\Http\Controllers\Controller;

    class CHandlerComment extends Controller
    {
        /**
         * ControllerHandlerComment constructor.
         */
        public function __construct()
        {
            $this->middleware('auth:api')->only('index','store', 'show', 'resetNew');
            $this->middleware('check.handler');
        }

        /**
         * @param int $ticket_id
         * @return JsonResponse
         */
        public function index(int $ticket_id): JsonResponse{
            $comments = CommentTicket::select('id', 'created_at', 'description', 'user_id', 'is_handler', 'is_new', 'is_read')
                ->where('ticket_id', $ticket_id)
                ->orderBy('created_at', 'DESC')
                ->get()
                ->load([
                    'commentViewer',
                    'user' => function ($query) {
                        $query->select('id', 'name', 'title');
                    }
                ]);

            foreach ($comments as $comment) {
                foreach ($comment->commentViewer as $v) {
                    $v->load([
                        'user' => function($query) {
                            $query->select('id', 'name', 'phone');
                        }
                    ]);
                }
            }
            return response()->json([
                'success' => true,
                'comments' => $comments
            ], 200);
        }

        /**
         * @param Request $request
         * @return JsonResponse
         */
        public function store(Request $request): JsonResponse{
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
            } else {
                $comment = CommentTicket::create([
                    'user_id' => $user->id,
                    'ticket_id' => $request->input('ticket_id'),
                    'description' => trim($request->input('description')),
                    'is_handler' => true,
                    'is_new' => true
                ]);

                if (!empty($ticket->user->email)) {
                    Mail::to($ticket->user->email)->send(new MailHandlerCommentNew($ticket->user->name, $ticket->title, $ticket->number, $user->name, trim($request->input('description'))));
                }
                $response = [ 'success' => true, 'comment_id' => $comment->id];
            }
            return response()->json($response, 200);
        }

        /**
         * @param int $id
         * @return JsonResponse
         */
        public function show(int $id): JsonResponse {
            $comment = CommentTicket::find($id);
            $comment->load([
                'user' => function ($query) {
                    $query->select('id', 'name', 'title');
                },
                'commentViewer'
            ]);

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
            $comment->save();
            return response()->json(['success' => true]);
        }
    }
}