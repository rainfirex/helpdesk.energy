<?php namespace App\Http\Controllers {

    use App\Http\Middleware\CheckHandler;
    use App\Services\LdapAuthService;
    use App\User;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;

    class CAuth extends Controller
    {
        /**
         * @var LdapAuthService
         */
        private $ldapAuthService;

        /**
         * ControllerAuth constructor.
         * @param LdapAuthService $ldapAuthService
         */
        public function __construct(LdapAuthService $ldapAuthService)
        {
            $this->middleware('auth:api')->only('logout');
            $this->ldapAuthService = $ldapAuthService;
        }

        /**
         * Авторизация
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function login(Request $request): JsonResponse{
            $login    = trim($request->input('login'));
            $password = $request->input('password');

            if (empty($login) || empty($password)) {
                return response()->json([
                    'success'  => false,
                    'message' => 'Не указан логин или пароль'
                ]);
            }

            if(!$this->ldapAuthService->loadConfig($host, $domain, $ldapDn)){
                return response()->json([
                    'success' => false,
                    'message' => 'Файл настроек LDAP не обнаружен!'
                ]);
            }

            if($this->ldapAuthService->ldapConnect($login, $password)){
                $userInfo = $this->ldapAuthService->getUserInfo($login);
                if (empty($userInfo['username'])) return response()->json(['success'  => false, 'message' => 'Пользователь не найден!']);
                $user = User::whereName($userInfo['username'])->first();

                if(!$user) {
                    $user = new User();
                }

                $user->password = bcrypt($password);
                $user->name  = $userInfo['username'];
                $user->email = $userInfo['email'];
                $user->phone = $userInfo['phone'];
                $user->mobile = $userInfo['mobile'];
                $user->title = $userInfo['title'];
                $user->othertelephone = $userInfo['othertelephone'];
                $user->department = $userInfo['department'];
                $user->api_token = Str::random(60);
                $user->last_ip = $_SERVER['REMOTE_ADDR'];
                $user->user_agent = $_SERVER['HTTP_USER_AGENT'];
                $user->is_handler = ($user->department === CheckHandler::HANDLER_DEPARTMENT) ? true : false;
                $user->save();

                $response = [
                    'success'    => true,
                    'user_id'    => $user->id,
                    'api_token'  => $user->api_token,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'phone'      => $user->phone,
                    'mobile'     => $user->mobile,
                    'department' => $user->department,
                    'title'      => $user->title,
                    'last_ip'    => $user->last_ip,
                    'user_agent' => $user->user_agent,
                    'is_handler' => $user->is_handler
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Не могу соединиться с сервером LDAP.'
                ];
            }
            return response()->json($response, 200);
        }

        /**
         * @param Request $request
         * @return JsonResponse
         */
        public function logout(Request $request): JsonResponse {
            $user = $request->user();
            $tokenApi = (!empty($request->header('Authorization')) ? str_replace('Bearer ', '', $request->header('Authorization')) : null);
            $isUserFound = false;

            if (!empty($user))
                $isUserFound = true;
            elseif(!empty($tokenApi)) {
                $user = User::where('api_token','=', $tokenApi)->first();
                if (!empty($user))
                    $isUserFound = true;
            }

            if ($isUserFound) {
                $user->api_token = null;
                $user->save();
            }

            return response()->json(['success' => $isUserFound]);
        }

        /**
         * Проверить авторизован ли пользователь
         * @return JsonResponse
         */
        public function isAuth(): JsonResponse {
            $user = Auth::user();
            $result = (!empty($user)) ? true : false;
            return response()->json(compact('result'));
        }
    }
}
