<?php namespace App\Http\Controllers\handler {

    use App\StatusTicket;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;

    class CHandlerStatusTicket extends Controller
    {
        /**
         * ControllerHandlerStatusTicket constructor.
         */
        public function __construct()
        {
            $this->middleware('auth:api')->only('index');
            $this->middleware('check.handler');
        }

        /**
         * @return JsonResponse
         */
        public function index(): JsonResponse {
            $statusTicket = StatusTicket::all();
            return response()->json([
                'success'=> true,
                'statusTicket' => $statusTicket
            ]);
        }
    }
}
