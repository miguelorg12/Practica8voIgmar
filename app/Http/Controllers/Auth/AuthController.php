<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SuccesActivate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;   
use Illuminate\Database\QueryException;
use App\Rules\reCaptcha;
use PDOException;
use Exception;
use App\Models\User;
use App\Mail\ActivateAccount;
use App\Mail\VerificationCode as MailVerificationCode;
use App\Models\VerificationCode;


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
        try{
            //Notificacion a slack
            Log::channel('slack')->info('Intento de inicio de sesion ' . $request->email . ' desde ' . $request->ip());
            //Validacion del request
            $validator = Validator::make($request->all(), [
                'email'=> 'required|email|max:255',
                'password'=> 'required|max:255',
                'g-recaptcha-response' => [new reCaptcha()],
            ]);
            //Si la validacion falla, se redirige al login con los errores
            if($validator->fails()){
                return redirect('/login')
                ->withInput()
                ->withErrors($validator); 
            }
            //Validar q el usuario exista
            $user = User::where('email', $request->email)->first();
            //Si el usuario es existente y esta activo
            if($user !== null && $user->status !== 'I'){
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

            }else{
                //Si el usuario no existe o esta inactivo, se redirige al login con el error
                return back()->withErrors('Credenciales incorrectas')
                ->withInput()
                ->with('error', 'Credenciales incorrectas');
            }
        } 
        catch(QueryException $e){
            // Manejo de errores de la consulta
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput()
            ->with('error', 'Error interno del servidor, intentelo mas tarde');
        } catch(PDOException $e){
            // Manejo de errores de la excepción de PDO
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput();
        }
        catch(Exception $e){
            // Manejo de errores de la excepción general
            return back()->withErrors('Error en el servidor')
            ->withInput()
            ->with('error', 'Error en el servidor');
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
    public function verificationCode(Request $request){
        try{
            //Notificacion a slack
            Log::channel('slack')->info('Intento de verificacion de correo ' . session('email') . ' desde ' . $request->ip());
            //Validacion del request
            $validator = Validator::make($request->all(), [
                'code'=> 'required|numeric|digits:6'
            ]);
            //Si la validacion falla, se redirige al login con los errores
            if($validator->fails()){
                return back()->withErrors($validator);
            }
            //Validar que el correo este en la sesion
            $user = User::where('email', session('email'))->first();
            //Eliminar el correo de la sesion

            //Validar que el usuario exista
            if($user !== null){
                //Validar el codigo de verificacion
                $verificationCode = VerificationCode::where('user_id', $user->id)->first();
                if($verificationCode !== null && Hash::check($request->code, $verificationCode->code)){
                    Auth::login($user);
                    session()->forget('email');
                    return redirect('/home')->with('success', 'Bienvenido');
                }else{
                return back()->withErrors('Codigo de verificacion incorrecto')
                ->withInput()
                ->with('error', 'Codigo de verificacion incorrecto');
                }
            }else{
                return redirect('/login');
            }
        }
        catch(QueryException $e){
            // Manejo de errores de la consulta
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput()
            ->with('error', 'Error interno del servidor, intentelo mas tarde');
        } catch(PDOException $e){
            // Manejo de errores de la excepción de PDO
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput();
        }
        catch(Exception $e){
            // Manejo de errores de la excepción general
            return back()->withErrors('Error en el servidor')
            ->withInput()
            ->with('error', 'Error en el servidor');
        }
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
        try{
            //Notificacion a slack
            Log::channel('slack')->info('Intento de registro ' . $request->email . ' desde ' . $request->ip());
            //Validacion del request
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    'max:255',
                    'regex:/^[a-zA-Z\s]+$/'
                ],
                'email' => 'required|email|max:255|unique:users',
                'password' => [
                    'required',
                    'max:255',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'
                ],
                'password_confirmation' => 'required|same:password',
                'g-recaptcha-response' => 'required|captcha',
            ]);
            //Si la validacion falla, se redirige al registro con los errores
            if($validator->fails()){
                return redirect('/register')
                ->withInput()
                ->withErrors($validator);
            }
            //Validar que el correo no exista
            $user = User::where('email', $request->email)->first();
            if($user !== null){
                //Si el correo ya existe, se redirige al registro con el error
                return back()->withErrors('Correo invalido pruebe con otro')
                ->withInput()
                ->with('error', 'Correo invalido pruebe con otro');
                
            }else{
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
        }
        catch(QueryException $e){
            // Manejo de errores de la consulta
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput()
            ->with('error', 'Error interno del servidor, intentelo mas tarde');
        } catch(PDOException $e){
            // Manejo de errores de la excepción de PDO
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput();
        }
        catch(Exception $e){
            // Manejo de errores de la excepción general
            return back()->withErrors('Error en el servidor')
            ->withInput()
            ->with('error', 'Error en el servidor');
        }
    }
    /* Activate account
    * Este metodo se encarga de activar la cuenta del usuario
    * @param int $user_id
    */
    public function activateAccount(int $user_id){
        try{
            //Validar la ruta firmada
            $user = User::where('id', $user_id)->first();
            //Validar que el usuario exista
            if($user !== null){
                //Si la ruta es valida y el usurio existe, se activa la cuenta
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
        catch(QueryException $e){
            // Manejo de errores de la consulta
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput()
            ->with('error', 'Error interno del servidor, intentelo mas tarde');
        } catch(PDOException $e){
            // Manejo de errores de la excepción de PDO
            return back()->withErrors('Error interno del servidor, intentelo mas tarde')
            ->withInput();
        }
        catch(Exception $e){
            // Manejo de errores de la excepción general
            return back()->withErrors('Error en el servidor')
            ->withInput()
            ->with('error', 'Error en el servidor');
        }
    }

    /* Logout user*/
    public function logout(){
        //Cierra la sesion
        Auth::logout();
        return redirect('/login')->with('success', 'Sesion cerrada exitosamente');
    }
    public function showHome(){
        return view('home');
    }

}
