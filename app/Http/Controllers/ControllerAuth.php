<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckHandler;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class ControllerAuth extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('logout');
    }

    public function login(Request $request){

        $login    = trim($request->input('login'));
        $password = $request->input('password');

        if (empty($login) || empty($password)) {
            return response()->json([
                'success'  => false,
                'message' => 'Не указан логин или пароль'
            ]);
        }

        $option = Config::get('ldapconfig');
        if ($option == null) {
            return response()->json([
                'success' => false,
                'message' => 'Файл настроек LDAP не обнаружен!'
            ]);
        }

        $host = $option['host'];
        $domain = $option['domain'];
        $ldapDn = $option['ldapDn'];

        $ldapConnect = ldap_connect($host);

        if ($ldapConnect) {
            ldap_set_option($ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConnect, LDAP_OPT_REFERRALS, 0);

            try{
                $ldapBind = ldap_bind($ldapConnect, $domain.'\\'.$login, $password);
            }catch (\Exception $ex) {
                $message = $ex->getMessage();

                if ($message == 'ldap_bind(): Unable to bind to server: Invalid credentials')
                    $message = 'Неверные учетные данные';

                return response()->json([
                   'success'  => false, 'message' => $message
                ]);
            }

            if ($ldapBind) {
                //привязка LDAP прошла успешно...
                $filter = '(&(objectClass=user)(objectCategory=person)(sAMAccountName='.$login.'))';
                $sr = ldap_search($ldapConnect, $ldapDn, $filter,  ['cn', 'dn', 'mail', 'telephonenumber', 'othertelephone', 'mobile', 'department', 'title']);

//                $filter = '(&(objectClass=user)(objectCategory=person)(sAMAccountName=' . $login.'))';
//                $filter = '(&(objectClass=user)(objectCategory=person)(sAMAccountName=' .'Poplavskiy_AA'.'))';
//                $filter = '(&(objectClass=user)(objectCategory=person)(sAMAccountName=' .'litin_dv '.'))';
//                $filter = '(&(objectClass=user)(objectCategory=person)(sAMAccountName=' .'ldap_users'.'))';
//                $filter = '(&(objectClass=user)(objectCategory=person)(sAMAccountName=' .'smirnov_mv'.'))';


                $sr = ldap_search($ldapConnect, $ldapDn, $filter,  ['*']);

                $ldapEntries = ldap_get_entries($ldapConnect, $sr);

//                dd($filter, $ldapEntries);

                $username = isset($ldapEntries[0]['cn']) ? $ldapEntries[0]['cn'][0] : '';
                $email = isset($ldapEntries[0]['mail']) ? $ldapEntries[0]['mail'][0] : '';
                $phone = isset($ldapEntries[0]['telephonenumber']) ? $ldapEntries[0]['telephonenumber'][0] : '';
                $mobile = isset($ldapEntries[0]['mobile']) ? $ldapEntries[0]['mobile'][0] : '';
                $othertelephone = isset($ldapEntries[0]['othertelephone']) ? $ldapEntries[0]['othertelephone'][0] : '';
                $department = isset($ldapEntries[0]['department']) ? $ldapEntries[0]['department'][0] : '';
                $title = isset($ldapEntries[0]['title']) ? $ldapEntries[0]['title'][0] : '';

                ldap_close($ldapConnect);

                if(empty($username)){
                    return response()->json([
                        'success'  => false, 'message' => 'Пользователь не найден!'
                    ]);
                }

                $user = User::whereName($username)->first();

                if(!$user) {
                    $user = new User();
                }

                $user->password = bcrypt($password);
                $user->name = $username;
                $user->email = $email;
                $user->phone = $phone;
                $user->mobile = $mobile;
                $user->othertelephone = $othertelephone;
                $user->department = $department;
                $user->title = $title;
                $user->api_token = Str::random(60);
                $user->last_ip = $_SERVER['REMOTE_ADDR'];
                $user->user_agent = $_SERVER['HTTP_USER_AGENT'];
                $user->is_handler = ($user->department === CheckHandler::HANDLER_DEPARTMENT) ? true : false;
                $user->save();

                return response()->json([
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
                ], 200);
            } else {
                //привязка LDAP не удалась...
                ldap_close($ldapConnect);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'Не могу соединиться с сервером LDAP.'
        ]);
    }

    public function logout(Request $request) {
        $result = false;
        $user = $request->user();
        $headerTokenApi = $request->header('Authorization');

        if (!empty($user)) {
            $result = true;
        } elseif($headerTokenApi) {

            $user = User::where('api_token','=', str_replace('Bearer ', '', $headerTokenApi))->first();

            if (!empty($user))
                $result = true;

        } else {
            $result = false;
        }

        if ($result && !empty($user)) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'success' => $result
        ]);
    }

}
