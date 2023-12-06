<?php

namespace App\Http\Controllers;

use Facebook\Facebook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShareVideoController extends Controller
{
    public function shareWidget($file_id)
    {
        $app_id = '189787587542245';
        $app_secret = '23c951e9d5d1f46ea0d9cdbcb1f970e5';


        $fb = new Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v18.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $login_url = $helper->getLoginUrl($redirect_uri, ['email']);
        return $login_url;
    }
}
