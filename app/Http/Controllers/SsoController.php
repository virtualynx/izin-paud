<?php

namespace App\Http\Controllers;

use VLynx\Sso\VAuthSsoClient;
use Illuminate\Routing\Controller;

class SsoController extends Controller
{
    private $sso;

    public function __construct()
    {
        $server_url = config('app.sso.server_url');
        $client_id = config('app.sso.client_id');
        $client_secret = config('app.sso.client_secret');

        $this->sso = new VAuthSsoClient($server_url, $client_id, $client_secret);
    }

    public function callback(){
        $params = request()->all();

        $resp = $this->sso->SsoCallbackHandler();

        if($resp['action'] == 'login'){
            $redirect = 'docs';

            if(!empty($params['redirect'])){
                $redirect = $params['redirect'];
            }

            return redirect($redirect);
        }
    }
}
