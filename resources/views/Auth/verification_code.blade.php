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
            <h6 class="fs-4">Codigo de verificacion</h6>
        </div>
        <form method="POST" action="{{ route('verifyCodeUser') }}" id="verifyCodeForm">
            @csrf
            <!-- Email input -->
            <div class="form-group fs-6">
                <label for="code">Codigo</label>
                <input type="number" class="form-control" id="code" name="code" placeholder="Codigo" value="{{ old('code') }}" oninput="limitInput(this)">
                <small class="form-text text-danger">
                    @if($errors->has('code'))
                    {{$errors->first('code')}}
                    @endif
                  </small>
            </div>
            <!-- Submit button -->
            <div class="col-12 justify-content-center d-flex mt-2">
                <button type="submit" class="btn btn-primary col-12">Verificar Codigo</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script>
    //Define una funcion para limitar la cantidad de caracteres que se pueden ingresar en el input de codigo
    function limitInput(element) {
        if (element.value.length > 6) {
            element.value = element.value.slice(0, 6);
        }
    }
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
    //Define un cuadro de dialogo de carga al enviar el formulario
    document.getElementById('verifyCodeForm').addEventListener('submit', function() {
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