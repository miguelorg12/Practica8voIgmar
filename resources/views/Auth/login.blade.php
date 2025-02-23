@extends('layouts.app')
@section('css')
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .fade-in {
        animation: fadeIn 1.5s ease-in-out;
    }
    .valid-feedback {
        display: none;
        color: green;
    }
    .invalid-feedback {
        display: none;
        color: red;
    }
</style>
@endsection
@section('content')
<title>Login</title>
<div class="d-flex justify-content-center align-items-center vh-100 bg-dark">
    <div class="card p-4 shadow-lg " style="width: 30rem; animation: fadeIn 1.5s ease-in-out;">
        <div class="text-center">
            <h6 class="fs-4">Login</h6>
        </div>
        <form method="POST" action="{{ route('loginUser') }}" id="loginForm">
            @csrf
            <!-- Email input -->
            <div class="form-group fs-6">
                <label for="email">Correo</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese correo" value="{{ old('email') }}">
                <small class="form-text text-danger">
                    @if($errors->has('email'))
                    {{$errors->first('email')}}
                    @endif
                </small>
            </div>

            <!-- Password input -->
            <div class="form-group fs-6 mt-2">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" value="{{ old('password') }}">
                <small class="form-text text-danger">
                    @if($errors->has('password'))
                    {{$errors->first('password')}}
                    @endif
                </small>
            </div>

            <!-- Recaptcha -->
            <label for="password mt-4">reCaptcha</label>
            <div class="g-recaptcha col-12" data-sitekey="{{env('RECAPTCHA_SITE_KEY')}}"></div>
            <small class="form-text text-danger">
                @if($errors->has('g-recaptcha-response'))
                {{ $errors->first('g-recaptcha-response') }}
                @endif
            </small>
            <!-- Submit button -->
            <div class="col-12 justify-content-center d-flex mt-2">
                <button type="submit" class="btn btn-primary col-12">Iniciar Sesion</button>
            </div>

            <!-- Register link -->
            <div class="text-center mt-3">
                <p>¿No estas registrado? <a href="/register" class="text-decoration-none text-reset fw-bold">Registrate</a></p>
                <p>Server 1</p>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script>
    // Validación del formulario antes de enviarlo
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');

        if (email.value.trim() === '' || password.value.trim() === '') {
            event.preventDefault();
            Swal.fire({
                title: '¡Error!',
                text: 'Por favor, asegúrate de que todos los campos estén llenos.',
                icon: 'error',
                timer: 3000,
                showConfirmButton: false,
            });
        } else {
            Swal.fire({
                title: 'Cargando...',
                text: 'Por favor, espera mientras procesamos tu solicitud.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
        }
    });

    // Define los cuadros de dialogo que mostrara dependiendo lo que retorne la respuesta
    @if(session('success'))
        Swal.fire({
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: false,
        });
    @elseif(session('error'))
        Swal.fire({
            title: '¡Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            timer: 3000,
            showConfirmButton: false,
        });
    @endif
</script>
@endsection