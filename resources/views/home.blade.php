@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="fw-bold">Bienvenido 1</h1>
        <!--Logout button-->
        <div class="mt-4">
            <a href="{{ route('logout') }}" class="btn btn-danger">Cerrar Sesión</a>
        </div>
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
</script>
@endsection
