<?php namespace App\Http\Controllers\handler {

    use App\Ticket;
    use Illuminate\Http\JsonResponse;
    use App\Http\Controllers\Controller;

    class CHandlerTicketFind extends Controller
    {
        /**
         * ControllerHandlerTicketFind constructor.
         */
        public function __construct() {
            $this->middleware('auth:api')->only('index');
            $this->middleware('check.handler');
        }

        /**
         * @param string $findText
         * @return JsonResponse
         */
        public function index(string $findText): JsonResponse {
            $findText = trim(strtolower($findText));
            $tickets = Ticket::where('title', 'like', "%$findText%")
                ->orWhere('number', 'like', "%$findText%")
                ->orWhere('description', 'like', "%$findText%")
                ->get()
                ->load([
                    'user' => function ($query) {
                        $query->select('id', 'name', 'email', 'phone', 'department');
                    },
                    'statusTicket' => function ($query) {
                        $query->select('id', 'status', 'title');
                    },
                    'performerUser' => function ($query) {
                        $query->select('id', 'email', 'name', 'phone', 'title');
                    }
                ]);

            return response()->json([
                'success' => true,
                'findText' => $findText,
                'tickets' => $tickets
            ]);
        }
    }
}