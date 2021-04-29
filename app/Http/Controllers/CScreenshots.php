<?php namespace App\Http\Controllers {

    use App\ScreenshotFile;
    use Illuminate\Http\JsonResponse;

    class CScreenshots extends Controller
    {
        /**
         * ControllerScreenshots constructor.
         */
        public function __construct() {
            $this->middleware('auth:api')->only('index');
        }

        /**
         * @param int $ticket_id
         * @return JsonResponse
         */
        public function index(int $ticket_id): JsonResponse {
            $screenshots = ScreenshotFile::select('id', 'url', 'name', 'mime_type')->where('ticket_id', '=', $ticket_id)->get();
            return response()->json(compact('screenshots'));
        }
    }
}