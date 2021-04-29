<?php namespace App\Http\Controllers\user {

    use App\CategoryTicket;
    use App\Http\Controllers\Controller;
    use Illuminate\Http\JsonResponse;

    class CCategoryTicket extends Controller
    {
        /**
         * ControllerCategoryTicket constructor.
         */
        public function __construct()
        {
            $this->middleware('auth:api')->only('index');
        }

        /**
         * @return JsonResponse
         */
        public function index(): JsonResponse
        {
            $categories = CategoryTicket::all();
            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        }
    }
}