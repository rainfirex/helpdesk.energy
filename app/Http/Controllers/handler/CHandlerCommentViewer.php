<?php namespace App\Http\Controllers\handler {

    use App\CommentTicket;
    use App\CommentViewer;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Auth;

    class CHandlerCommentViewer extends Controller
    {
        public function __construct()
        {
            $this->middleware('auth:api')->only('store');
        }

        public function index() {
            $comments = CommentTicket::orderBy('id', 'DESC')->get();
            $comments->load(['commentViewer']);

            foreach ($comments as $c) {
                foreach ($c->commentViewer as $v) {
                    $v->load([
                        'user' => function($query) {
                            $query->select('id', 'name', 'phone');
                        }
                    ]);
                }
            }
            return response()->json(compact('comments'));
        }

        /**
         * @param int $commentId
         * @return JsonResponse
         */
        public function store(int $commentId): JsonResponse {
            $result = false;
            $user = Auth::user();

            $commentViewer = CommentViewer::where([
                ['user_id', '=', $user->id],
                ['comment_id', '=', $commentId]
            ])->first();

            if (empty($commentViewer)) {
                $commentViewer = CommentViewer::create([
                    'user_id'    => $user->id,
                    'comment_id' => $commentId
                ]);
                $commentViewer->load([
                    'user' => function($query) {
                    $query->select('id', 'name', 'phone');
                }]);
                $result = (!empty($commentViewer)) ? true : false;
            }

            return response()->json(compact('result', 'commentViewer'));
        }
    }
}