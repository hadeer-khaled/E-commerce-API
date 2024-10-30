<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PassportHelper
{
    public static function generateTokens($email , $password) : bool|array
    {
        $response = Request::create('/oauth/token', 'POST', [
            'grant_type' => "password",
//            'client_id' => env('PASSPORT_CLIENT_ID'),
//            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'client_id' =>6,
            'client_secret' => 'zdONqUPak3wnBMw4Uc3wkFL84KjbjDDWKT2EsSd9',
            'username' => $email,
            'password' => $password,
        ]);
        return (array)json_decode(app()->handle($response)->getContent());
    }
}

?>
