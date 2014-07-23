<?php namespace Cartalyst\Sentry\Auth\Providers;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookCanvasLoginHelper;

class Facebook extends SocialAbstract {

    /**
     * metoda do autoryzacji
     * @param array $credentials
     * @param bool $remember
     * @return mixed
     */
    public function authorize(array $credentials, $remember=false) {
        @session_start();
        
        $helper = new FacebookRedirectLoginHelper( $credentials['redirect'], $credentials['appId'], $credentials['secret'] );
        
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
        }
    }

    /**
     * rejestracja uzytkownika w systemie
     * @return mixed
     */
    public function register($appId, $secret) {
        @session_start();

        FacebookSession::setDefaultApplication($appId, $secret);

        $helper = new FacebookRedirectLoginHelper(asset('users/do_facebookRegister'));

        try
        {
            $session = $helper->getSessionFromRedirect();
        }
        catch(FacebookRequestException $ex)
        {
            //
        }
        catch(\Exception $ex)
        {
            //
        }

        if(isset($session))
        {
            $facebok_user = (new FacebookRequest($session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());

            $userdata = array(
                'application' => 'facebook',
                'openid'      => $facebok_user->getProperty('id'),
                'email'       => $facebok_user->getProperty('email'),
                'first_name'  => $facebok_user->getProperty('first_name'),
                'last_name'   => $facebok_user->getProperty('last_name')
            );

            return $userdata;
        }
        
        return false;
    }
    
    /**
     * wylogowuje uzytkownika z systemu
     * @return void
     */
    public function logout() {
        
    }
    
    
    /**
     * pobiera login do logowania przez facebook
     * @return string
     */
    public function getLoginURL($appId = null, $secret = null) {
        FacebookSession::setDefaultApplication($appId, $secret);
        
        $helper = new FacebookRedirectLoginHelper(asset('users/do_facebookRegister'), $appId, $secret);
        
        return $helper->getLoginUrl(array(
                    'scope' => 'email'
        ));
    }
}