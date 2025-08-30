<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
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
            $sso_user = $resp['data'];
            $user = UserProfile::where('user_id', $sso_user->user_id)->first();

            if(empty($user)){
                $new_user = new UserProfile([
                    'user_id' => $sso_user->user_id,
                    'name' => $sso_user->email
                ]);
                $new_user->save();
            }

            $redirect = '/';

            if(!empty($params['redirect'])){
                $redirect = $params['redirect'];
            }

            return redirect($redirect);
        }
    }
}
