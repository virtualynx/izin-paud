<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Services\UserService;
use VLynx\Sso\VAuthSsoClient;
use Illuminate\Routing\Controller;

class SsoController extends Controller
{
    private $sso;
    private $userService;

    public function __construct(
        UserService $userService
    ){
        $server_url = config('app.sso.server_url');
        $client_id = config('app.sso.client_id');
        $client_secret = config('app.sso.client_secret');

        $this->sso = new VAuthSsoClient($server_url, $client_id, $client_secret);
        $this->userService = $userService;
    }

    public function loginPage(){
        $server_url = config('app.sso.server_url');
        $client_id = config('app.sso.client_id');
        $client_secret = config('app.sso.client_secret');
        $server_url_local = config('app.sso.server_url_local');

        $ssoClient = new VAuthSsoClient($server_url, $client_id, $client_secret, $server_url_local);
        $ssoClient->LoginPage();
    }

    public function callback(){
        $params = request()->all();

        $resp = $this->sso->SsoCallbackHandler();

        if($resp['action'] == 'login'){
            $sso_user = $resp['data'];
            $user = UserProfile::where('user_id', $sso_user->user_id)->first();

            if(empty($user)){
                $user = new UserProfile([
                    'user_id' => $sso_user->user_id,
                    'name' => $sso_user->email
                ]);
                $user->save();
            }

            $is_verificator = $this->userService->isVerificator($sso_user->user_id);
            session()->put('is_verificator', $is_verificator);
            $is_approver = $this->userService->isApprover($sso_user->user_id);
            session()->put('is_approver', $is_approver);

            $redirect = '/';

            if(!empty($params['redirect'])){
                $redirect = $params['redirect'];
            }

            return redirect($redirect);
        }
    }

    public function logout(){
        session()->invalidate();

        $this->sso->Logout();
    }
}
