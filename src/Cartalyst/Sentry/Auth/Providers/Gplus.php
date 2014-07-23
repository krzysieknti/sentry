<?php namespace Cartalyst\Sentry\Auth\Providers;

class Gplus extends SocialAbstract {

    /**
     * metoda do autoryzacji
     * @param array $credentials
     * @param bool $remember
     * @return mixed
     */
    public function authorize(array $credentials, $remember=false) {
        @session_start();

        $helper = new FacebookRedirectLoginHelper();
        try {
            $session = $helper->getSessionFromRedirect();
        } catch(FacebookRequestException $ex) {
            // When Facebook returns an error
            return false;
        } catch(\Exception $ex) {
          // When validation fails or other local issues
            return false;
        }
        if ($session) {
          var_dump("ZALOGOWANO");
          var_dump("ZALOGOWANO");
          var_dump("ZALOGOWANO");
          var_dump("ZALOGOWANO");
        }
    }

    /**
     * rejestracja uzytkownika w systemie
     * @return mixed
     */
    public function register($appId, $secret) {
        @session_start();

        $client = new Google_Client();
        $client->setClientId($appId);
        $client->setClientSecret($secret);
        $client->setRedirectUri($_SERVER['HTTP_HOST'] . '/public/users/do_googleRegister');
        $client->setScopes('https://www.googleapis.com/auth/userinfo.profile email');

        try
        {
            $client->authenticate($_GET['code']);
        }
        catch(Google_Auth_Exception $e)
        {
            //nieprawidlowy parametr code
            echo 'Nieprawidłowy link.';
            die();
        }

        // czysty access token w stringu
        $access_token = json_decode($client->getAccessToken(), 1)['access_token'];

        // pobranie userinfo - id, email, verified_email, name, given_name, family_name, link, pricutre, gender, locale
        $userinfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $access_token), 1);

        $userdata = array(
            'application' => 'google',
            'openid'      => $userinfo['id'],
            'email'       => $userinfo['email'],
            'first_name'  => $userinfo['given_name'],
            'last_name'   => $userinfo['family_name']
        );

        return $userdata ;
    }
    
    /**
     * wylogowuje uzytkownika z systemu
     * @return void
     */
    public function logout() {
        
    }
    
    
    /**
     * pobiera login do logowania przez gplusa
     * @return string
     */
    public function getLoginURL($appId = null, $secret = null) {
        $client = new Google_Client();
        $client->setClientId(Config::get('google.clientId'));
        $client->setClientSecret(Config::get('google.secret'));
        $client->setRedirectUri('http://localhost/fnts/public/users/do_googleRegister');
        $client->setScopes('https://www.googleapis.com/auth/userinfo.profile email');

        return $client->createAuthUrl();
    }
}