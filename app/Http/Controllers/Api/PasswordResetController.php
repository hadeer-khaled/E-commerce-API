<?php

namespace App\Http\Controllers\APi;

use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
    
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email'),
          
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'message' => __($status)
            ];
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    // public function resetPassword(ResetPasswordRequest $request)
    // {

    //     $matchingRecord = null;

    //     $request->validated();

    //     $tokenRecords = DB::table('password_reset_tokens')->get();

    //     foreach ($tokenRecords as $tokenRecord) {
    //         if (Hash::check( $request->token, $tokenRecord->token)) {
    //             $matchingRecord = $tokenRecord;
    //             break;
    //         }
    //     }

    //     if ($matchingRecord) {
    //         $request['email'] = $matchingRecord->email;

    //         $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

    //         $status = Password::reset($credentials, function ($user, $password) {
    //             $user->password = bcrypt($password);
    //             $user->save();
    //         });

    //         if ($status == Password::PASSWORD_RESET) {
    //             return response([
    //                 'message'=> 'Password reset successfully'
    //             ]);
    //         }
    
    //         return response([
    //             'message'=> __($status)
    //         ], 400);
    //     }
    //     return response([
    //         'message'=> "Cannot find matched token"
    //     ], 404);


    // }
    public function resetPassword(ResetPasswordRequest $request)
    {

        $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message'=> 'Password reset successfully'
            ]);
        }

        return response([
            'message'=> __($status)
        ], 400);

    }

}