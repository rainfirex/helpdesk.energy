<?php

namespace App\Http\Controllers;

use App\Modules\LdapInfo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ControllerAuth extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('logout');
    }

    public function login(string $login, string $password){
        if (empty($login) || empty($password)) {
            return response()->json([
                'result'  => false,
                'message' => 'Не указан логин или пароль'
            ]);
        }

        $host = 'vm-adc-01.sakh.dvec.ru';
        $domain = 'SAKH-DEC';
        $ldapDn = 'OU=Управление,OU=Users,OU=Domain Root Entry,DC=sakh,DC=dvec,DC=ru';

        $ldapConnect = ldap_connect($host);// or die("Не могу соединиться с сервером LDAP.");

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
                $filter = '(&(objectClass=user)(objectCategory=person)(samaccountname=' . $login.'))';
                //"(cn=*)" "(ou=*)" array("mail", "telephonenumber", "othertelephone", "mobile", "ipphone", "department", "title")
                $sr = ldap_search($ldapConnect, $ldapDn, $filter,  ['cn', 'dn', 'mail', 'telephonenumber', 'othertelephone', 'mobile', 'department', 'title']);
                $ldapEntries = ldap_get_entries($ldapConnect, $sr);


                $username = isset($ldapEntries[0]['cn']) ? $ldapEntries[0]['cn'][0] : '';
                $email = isset($ldapEntries[0]['mail']) ? $ldapEntries[0]['mail'][0] : '';
                $phone = isset($ldapEntries[0]['telephonenumber']) ? $ldapEntries[0]['telephonenumber'][0] : '';
                $mobile = isset($ldapEntries[0]['mobile']) ? $ldapEntries[0]['mobile'][0] : '';
                $othertelephone = isset($ldapEntries[0]['othertelephone']) ? $ldapEntries[0]['othertelephone'][0] : '';
                $department = isset($ldapEntries[0]['department']) ? $ldapEntries[0]['department'][0] : '';
                $title = isset($ldapEntries[0]['title']) ? $ldapEntries[0]['title'][0] : '';

                ldap_close($ldapConnect);


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
                    'title'      => $user->title
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
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json([
           'success' => true
        ]);
    }
}
