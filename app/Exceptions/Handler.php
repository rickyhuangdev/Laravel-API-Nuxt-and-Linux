<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
//        $this->reportable(function (Throwable $e) {
//            //
//        });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Record not found.'
                ], 404);
            }
        });
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'You are not authorized to access the resource.'
                ], 403);
            }
        });
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Permission Denied.'
                ], 403);
            }
        });
        $this->renderable(function (ModelNotDefined $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'No model defined.'
                ], 500);
            }
        });


    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException || $e instanceof AccessDeniedHttpException) {
            if ($request->expectsJson()) {
                return response()->json([
                    "errors" => [
                        "message" => "You are not authorized to access the resource"
                    ]
                ], 403);
            }
        }
        return parent::render($request, $e); // TODO: Change the autogenerated stub
    }
}
