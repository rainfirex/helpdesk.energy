<?php namespace App\Http\Controllers {

    use App\DocFile;
    use Illuminate\Http\JsonResponse;

    class CHandlerDocs extends Controller
    {
        /**
         * CHandlerDocs constructor.
         */
        public function __construct()
        {
            $this->middleware('auth:api')->only('index');
        }

        /**
         * @param int $ticket_id
         * @return JsonResponse
         */
        public function index(int $ticket_id): JsonResponse{
            $docs = DocFile::select('id', 'url', 'name', 'mime_type')->where('ticket_id', '=', $ticket_id)->get();
            return response()->json(['docs' => $docs]);
        }
    }
}