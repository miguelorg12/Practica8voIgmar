@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <h1 class="display-1 fw-bold">404 Not Found</h1>
        <p class="lead">Lo sentimos, la página que estás buscando no se pudo encontrar.</p>
        <p>Serás redirigido a la página anterior en 5 segundos.</p>
    </div>
</div>
@endsection
@section('js')
<script>
    // Redirige a la página anterior después de 5 segundos
    setTimeout(function() {
            window.history.back();
        }, 5000);
</script>
@endsection