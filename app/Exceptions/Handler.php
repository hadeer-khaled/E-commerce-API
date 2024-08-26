<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */

    //    public function register()
    //     {
    //         $this->renderable(function (Throwable $e, $request) {
    //                     if ($e instanceof ModelNotFoundException) {
    //                         $model = strtolower(class_basename($exception->getModel()));
    //                         return response()->json(['message' => $model.' not found' , "status" => 404], 404);
                    
    //                     }
    //                     if($e instanceof  \Spatie\Permission\Exceptions\UnauthorizedException){
    //                         return response()->json([
    //                             'message' => 'You do not have the required authorization.',
    //                             'status'  => 403,
    //                         ], 403);
    //                     }
                    
    //         });
    //     }
    public function register()
    {
        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->json([
                'message' => 'You do not have the required authorization.',
                'status'  => 403,
            ], 403);
        });
    }

    public function render($request, Throwable $exception)
    {
        if( $request->is('api/*'))
        {
            if ($exception instanceof ModelNotFoundException) {
                $model = strtolower(class_basename($exception->getModel()));
                return response()->json(['message' => $model.' not found' , "status" => 404], 404);

            }
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                            'error' => 'Resource not found'
                        ], 404);
                            
            }
        }
    }
}
