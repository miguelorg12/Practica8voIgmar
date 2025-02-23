<?php

namespace App\Http\Controllers\Auth;

use Exception;
use PDOException;
use App\Models\User;
use App\Rules\reCaptcha;
use App\Mail\SuccesActivate;
use Illuminate\Http\Request;
use App\Mail\ActivateAccount;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;   
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerificationCode as MailVerificationCode;


class AuthController extends Controller
{
    /* Show login form 
    * @return \Illuminate\View\View
    */
    public function showLoginForm()
    {
        return view('auth.login');
    }
    /* Login user
    * Este metodo se encarga de validar el usuario y la contraseña, 
    si son correctos, se envia un correo con un codigo de verificacion
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\View\View
    */
    public function login(Request $request){
            //Notificacion a slack
            Log::channel('slack')->info('Intento de inicio de sesion ' . $request->email);
            //Validacion del request
            $this->validate($request, [
                'email' => 'required|email|max:255',
                'password' => 'required|max:255',
                'g-recaptcha-response' => ['required', new reCaptcha],
            ], [
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser una dirección válida.',
                'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.max' => 'La contraseña no puede tener más de 255 caracteres.',
                'g-recaptcha-response.required' => 'El reCaptcha es obligatorio.',
            ]);
            //Validar q el usuario exista
            $user = User::where('email', $request->email)->first();
            //Si el usuario es existente y esta activo
            if($user !== null){
                if($user->status === 'I'){
                    return back()->withErrors('Cuenta inactiva')
                    ->withInput()
                    ->with('error', 'Cuenta inactiva');
                }
                //Valida la contraseña
                if(Hash::check($request->password, $user->password)){
                //Genera un codigo de verificacion para mandarlo por correo  
                $code = rand(100000, 999999);
                $verificationCode = VerificationCode::where('user_id', $user->id)->first();
                //Si el codigo de verificacion ya existe, se actualiza
                if($verificationCode !== null){
                    $verificationCode->code = Hash::make($code);
                    $verificationCode->save();
                }
                //Si el codigo de verificacion no existe, se crea uno nuevo
                else{
                    $verificationCode = new VerificationCode();
                    $verificationCode->user_id = $user->id;
                    $verificationCode->expires_at = now()->addMinutes(5);
                    $verificationCode->code = Hash::make($code);
                    $verificationCode->save();
                }
                //Envia el correo con el codigo de verificacion
                Mail::to($user->email)->send(new MailVerificationCode($code));
                //Guardar el correo en la sesion
                session(['email' => $user->email]);
                return redirect('verifyCode')->with('success', 'Hemos enviado un codigo de verificacion a su correo');
                }else{
                    return back()->withErrors('Credenciales incorrectas')
                    ->withInput()
                    ->with('error', 'Credenciales incorrectas');
                }

            }
            else{
                //Si el usuario no existe, se redirige al login con el error
                return back()->withErrors('Credenciales incorrectas')
                ->withInput()
                ->with('error', 'Credenciales incorrectas');
            }
        
    }

    /* Show verification code form */
    public function showVerifyCodeForm(){
        return view('auth.verification_code');
    }
    /* Verify user
    * Recibe el codigo de verificacion y lo compara con el almacenado 
    en la base de datos para verificar la autenticidad del usuario
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\View\View
    */
    public function verificationCode(Request $request)
    {
        // Notificación a Slack
        Log::channel('slack')->info('Intento de verificacion de correo ' . session('email') . ' desde ' . $request->ip());
        // Validación del request
        $this->validate($request, [
            'code' => 'required|numeric|digits:6',
            'g-recaptcha-response' => ['required', new reCaptcha],
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.numeric' => 'El código debe ser un número.',
            'code.digits' => 'El código debe tener exactamente 6 dígitos.',
            'g-recaptcha-response.required' => 'El reCaptcha es obligatorio.',            
        ]);
        // Validar que el correo esté en la sesión
        $user = User::where('email', session('email'))->first();
        // Validar que el usuario exista
        if ($user !== null) {
            // Validar el código de verificación
            $verificationCode = VerificationCode::where('user_id', $user->id)->first();

            if ($verificationCode !== null && Hash::check($request->code, $verificationCode->code)) {
                // Verificar el tiempo de expiración real
                if (now() > Carbon::parse($verificationCode->expires_at)) {
                    return back()->withErrors('Codigo de verificacion expirado')
                        ->withInput()
                        ->with('error', 'Codigo de verificacion expirado');
                }

                Auth::login($user);
                // Eliminar el correo de la sesión
                session()->forget('email');
                // Eliminar el código de verificación
                $verificationCode->delete();
                // Redirigir al home
                return redirect('/home')->with('success', 'Bienvenido');
            } else {
                // Si el código de verificación no es correcto se redirige devuelta con el error
                return back()->withErrors('Codigo de verificacion incorrecto')
                    ->withInput()
                    ->with('error', 'Codigo de verificacion incorrecto');
            }
        } else {
            return redirect('/login');
        }
    }

    public function resendVerifyCode(Request $request)
    {
        // Enviar notificación a Slack
        Log::channel('slack')->info('Intento de reenvio de codigo de verificacion ' . session('email') . ' desde ' . $request->ip());
        // Validar que el correo esté en la sesión
        $user = User::where('email', session('email'))->first();
        // Validar que el usuario exista
        if ($user !== null) {
            // Generar un nuevo código de verificación
            $code = rand(100000, 999999);
            // Actualizar el código de verificación en la base de datos
            $verificationCode = VerificationCode::where('user_id', $user->id)->first();
            $verificationCode->code = Hash::make($code);
            $verificationCode->expires_at = now()->addMinutes(5);
            $verificationCode->save();
            // Enviar el nuevo código por correo electrónico
            Mail::to($user->email)->send(new MailVerificationCode($code));
            // Redirigir al formulario de verificación
            return redirect()->back()->with('success', 'El código de verificación ha sido reenviado.');
        }
        return redirect()->back()->with('error', 'No se pudo reenviar el código de verificación.');
    }
    /* Show register form 
    * @return \Illuminate\View\View
    */
    public function showRegisterForm()
    {
        return view('auth.register');
    }
    /* Register user
    * Este metodo se encarga de validar los datos del usuario,
    si son correctos, se crea un nuevo usuario y se envia un correo
    con una ruta firmada para activar la cuenta
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\View\View
    */
    public function register(Request $request){
            //Notificacion a slack
            Log::channel('slack')->info('Intento de registro ' . $request->email . ' desde ' . $request->ip());
            //Validacion del request
            $this->validate($request, [
                'name' => [
                    'required',
                    'max:255',
                    'regex:/^[a-zA-Z\s]+$/',
                    'string'
                ],
                'email' => 'required|email|max:255|unique:users',
                'password' => [
                    'required',
                    'max:255',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'
                ],
                'password_confirmation' => 'required|same:password',
                'g-recaptcha-response' => ['required', new reCaptcha],
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'name.max' => 'El nombre no puede tener más de 255 caracteres.',
                'name.regex' => 'El nombre solo debe contener letras y espacios.',
                'email.required' => 'El correo electrónico es obligatorio.',
                'email.email' => 'El correo electrónico debe ser una dirección válida.',
                'email.max' => 'El correo electrónico no puede tener más de 255 caracteres.',
                'email.unique' => 'El correo electrónico ya está registrado.',
                'password.required' => 'La contraseña es obligatoria.',
                'password.max' => 'La contraseña no puede tener más de 255 caracteres.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',
                'password.regex' => 'La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.',
                'password_confirmation.required' => 'La confirmación de la contraseña es obligatoria.',
                'password_confirmation.same' => 'La confirmación de la contraseña no coincide.',
                'g-recaptcha-response.required' => 'El reCaptcha es obligatorio.',
            ]);
            //Validar que el correo no exista
            $user = User::where('email', $request->email)->first();
            if($user != null){
                return back()->withErrors('El correo ya esta registrado')
                ->withInput()
                ->with('error', 'El correo ya esta registrado');
            }
            //Si el correo no existe se crea un nuevo usuario
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            //Genera una ruta firmada para activar la cuenta
            $signedRoute = URL::temporarySignedRoute(
             'activateAccount', now()->addMinutes(30), ['id_user' => $user->id]
            );
            //Envia el correo con la ruta firmada
            Mail::to($user->email)->send(new ActivateAccount($signedRoute));
            return redirect('/login')->with('success', 'Usuario creado exitosamente, hemos enviado un correo para activar su cuenta');
    }
    /* Activate account
    * Este metodo se encarga de activar la cuenta del usuario
    * @param int $user_id
    */
    public function activateAccount(int $user_id){
            //Validar la ruta firmada
            $user = User::where('id', $user_id)->first();
            //Validar que el usuario exista
            if($user !== null){
                //Si la ruta es valida y el usuario existe, se activa la cuenta
                $user->status = 'A';
                $user->email_verified_at = now();
                $user->save();
                //Envia un correo de confirmacion de activacion
                Mail::to($user->email)->send(new SuccesActivate( $user )); 
                return redirect('/login')->with('success', 'Cuenta activada exitosamente');
            }else{
                //Si la ruta no es valida, se redirige al login
                return redirect('/login');
            }
    }

    

    /* Logout user*/
    public function logout(){
        //Cierra la sesion
        Auth::logout();
        return redirect('/login')->with('success', 'Sesion cerrada exitosamente');
    }
    /* Show home */
    public function showHome(){
        return view('home');
    }

}
