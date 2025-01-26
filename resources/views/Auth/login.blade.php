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
</style>
@endsection
@section('content')
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

            <!-- Submit button -->
            <div class="col-12 justify-content-center d-flex mt-2">
                <button type="submit" class="btn btn-primary col-12">Iniciar Sesion</button>
            </div>

            <!-- Register link -->
            <div class="text-center mt-3">
                <p>¿No estas registrado? <a href="/register" class="text-decoration-none text-reset fw-bold">Registrate</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script>
    //Define los cuadros de dialogo q mostrara dependiendo lo que retorne la respuesta
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
    //Define el cuadro de dialogo de carga
    document.getElementById('loginForm').addEventListener('submit', function() {
        Swal.fire({
            title: 'Cargando...',
            text: 'Por favor, espera mientras procesamos tu solicitud.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });
    });
</script>
@endsection