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
            'client_id' => env('PASSPORT_CLIENT_ID'),
            'client_secret' => env('PASSPORT_CLIENT_SECRET'),
            'username' => $email,
            'password' => $password,
        ]);

        return (array)json_decode(app()->handle($response)->getContent());
    }
}

?>