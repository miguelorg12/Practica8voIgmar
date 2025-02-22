<?php

namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Session\TokenMismatchException;


class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            
        });
    }
     /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        //Handle validacion de errores
        if ($exception instanceof ValidationException) {
            return redirect()->back()->withErrors($exception->errors())->withInput();
        }

        // Handle base de datos errores
        if ($exception instanceof QueryException) {
            return redirect()->back()->with('error', 'Error en el servidor, intenta de nuevo mas tarde.')->withInput();
        }

         // Handle 404 errors
         if ($exception instanceof NotFoundHttpException) {
            return response()->view('Errors.404', [], 404);
        }

        // Handle 419 errors
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('login')->with('error', 'Tu sesion ha expirado vuelve a loggear tu cuenta.');
        }
         // Default behavior: pass the exception to Laravel's default handler
        return parent::render($request, $exception);
    }
}
