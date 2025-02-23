@extends('Layouts.app')
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
<div class="d-flex justify-content-center align-items-center vh-100 bg-dark">

    <div class="card p-4 shadow-lg " style="width: 30rem; animation: fadeIn 1.5s ease-in-out;">
        <div class="text-center">
            <h6 class="fs-4">Codigo de verificacion</h6>
        </div>
        <form method="POST" action="{{ route('verifyCodeUser') }}" id="verifyCodeForm">
            @csrf
            <!-- Code input -->
            <div class="form-group fs-6">
                <label for="code">Codigo</label>
                <input type="number" class="form-control" id="code" name="code" placeholder="Codigo" value="{{ old('code') }}" oninput="limitInput(this)">
                <small class="form-text text-danger">
                    @if($errors->has('code'))
                    {{$errors->first('code')}}
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
                <button type="submit" class="btn btn-primary col-12">Verificar Codigo</button>
            </div>
        </form>
        <!-- Resend code button -->
        <div class="text-center mt-3">
            <form method="POST" action="{{ route('resendCode') }}" id="resendCodeForm">
                @csrf
                <button type="submit" class="btn btn-info text-white" id="resendButton" style="display: none;">Reenviar Codigo</button>
            </form>
            <div id="timer" class="mt-2">Puedes reenviar el código en <span id="countdown">60</span> segundos.</div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    // Define una funcion para limitar la cantidad de caracteres que se pueden ingresar en el input de codigo
    function limitInput(element) {
        if (element.value.length > 6) {
            element.value = element.value.slice(0, 6);
        }
    }

    // Iniciar el temporizador de 60 segundos
    let countdown = 60;
    const countdownElement = document.getElementById('countdown');
    const timerElement = document.getElementById('timer');
    const resendButton = document.getElementById('resendButton');
    timerElement.style.display = 'block';
    const interval = setInterval(function() {
        countdown--;
        countdownElement.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(interval);
            resendButton.style.display = 'block';
            timerElement.style.display = 'none';
        }
    }, 1000);

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

    // Define un cuadro de dialogo de carga al enviar el formulario de verificación
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

    // Define un cuadro de dialogo de carga al enviar el formulario de reenvío
    document.getElementById('resendCodeForm').addEventListener('submit', function(event) {
        event.preventDefault();
        resendButton.disabled = true;
        Swal.fire({
            title: 'Reenviando...',
            text: 'Por favor, espera mientras reenviamos el código.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        // Enviar el formulario de reenvío
        this.submit();

        // Deshabilitar el botón de reenviar durante 60 segundos
        let countdown = 60;
        const interval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                resendButton.disabled = false;
                resendButton.style.display = 'block';
                timerElement.style.display = 'none';
            }
        }, 1000);

        // Ocultar el botón de reenviar y mostrar el temporizador
        resendButton.style.display = 'none';
        timerElement.style.display = 'block';
    });
</script>
@endsection