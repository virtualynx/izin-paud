<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use App\Services\UserService;
use VLynx\Sso\VAuthSsoClient;
use Illuminate\Routing\Controller;

class TestController extends Controller
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

    public function testmail(){
        // $status = 'success';
        $status = 'verified';
        // $status = 'revision';

        return view('emails.status', [
            'profileName' => 'User Test',
            'paudName' => 'Paud Kemuning',
            'status' => $status,
            'actionUrl' => url('permit/request_list'),
            'actionText' => 'Detail'
        ]);
    }
}
