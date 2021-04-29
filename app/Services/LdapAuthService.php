<?php namespace App\Services {
    use Illuminate\Support\Facades\Config;

    class LdapAuthService
    {
        private $message;

        private $host;
        private $domain;
        private $ldapDn;

        private $ldapConnect;

        /**
         * @return mixed
         */
        public function getMessage(){
            return $this->message;
        }

        /**
         * @param $host
         * @param $domain
         * @param $ldapDn
         * @return bool
         */
        public function loadConfig(&$host, &$domain, &$ldapDn): bool {
            $option = Config::get('ldapconfig');
            $this->host = $host = $option['host'];
            $this->domain = $domain = $option['domain'];
            $this->ldapDn = $ldapDn = $option['ldapDn'];
            return ($option == null) ? false: true;
        }

        /**
         * @param string $login
         * @param string $password
         * @return bool
         */
        public function ldapConnect(string $login, string $password): bool {
            $this->ldapConnect = ldap_connect($this->host);
            if ($this->ldapConnect){
                ldap_set_option($this->ldapConnect, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($this->ldapConnect, LDAP_OPT_REFERRALS, 0);

                try{
                    return ldap_bind($this->ldapConnect, $this->domain.'\\'.$login, $password);
                }catch (\Exception $ex) {
                    $this->message = ($ex->getMessage() === 'ldap_bind(): Unable to bind to server: Invalid credentials') ? 'Неверные учетные данные': $ex->getMessage();
                }
            }
            return false;
        }

        /**
         * @param string $login
         * @return array|null
         */
        public function getUserInfo(string $login): ?array {
            //привязка LDAP прошла успешно...
            $filter = '(&(objectClass=user)(objectCategory=person)(sAMAccountName='.$login.'))';
            // $sr = ldap_search($ldapConnect, $ldapDn, $filter,  ['cn', 'dn', 'mail', 'telephonenumber', 'othertelephone', 'mobile', 'department', 'title']);
            $sr = ldap_search($this->ldapConnect, $this->ldapDn, $filter,  ['*']);
            $ldapEntries = ldap_get_entries($this->ldapConnect, $sr);

            $username = isset($ldapEntries[0]['cn']) ? $ldapEntries[0]['cn'][0] : '';
            $email = isset($ldapEntries[0]['mail']) ? $ldapEntries[0]['mail'][0] : '';
            $phone = isset($ldapEntries[0]['telephonenumber']) ? $ldapEntries[0]['telephonenumber'][0] : '';
            $mobile = isset($ldapEntries[0]['mobile']) ? $ldapEntries[0]['mobile'][0] : '';
            $othertelephone = isset($ldapEntries[0]['othertelephone']) ? $ldapEntries[0]['othertelephone'][0] : '';
            $department = isset($ldapEntries[0]['department']) ? $ldapEntries[0]['department'][0] : '';
            $title = isset($ldapEntries[0]['title']) ? $ldapEntries[0]['title'][0] : '';

            ldap_close($this->ldapConnect);

            if (empty($username)){
                $this->message = 'Пользователь не найден!';
                return null;
            } else {
                return [
                    'username' => $username,
                    'email'    => $email,
                    'phone'    => $phone,
                    'mobile'   => $mobile,
                    'title'    => $title,
                    'othertelephone' => $othertelephone,
                    'department'     => $department
                ];
            }
        }
    }
}