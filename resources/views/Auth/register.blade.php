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
<title>Register</title>
<div class="d-flex justify-content-center align-items-center vh-100 bg-dark">
    <div class="card p-4 shadow-lg " style="width: 30rem; animation: fadeIn 1.5s ease-in-out;">
        <div class="text-center">
            <h6 class="fs-4">Registro</h6>
        </div>
        <form method="POST" action="{{ route('registerUser') }}" id="registerForm">
            @csrf
            <!-- Name input -->
            <div class="form-group fs-6">
                <label for="name">Nombre completo</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Ingrese nombre completo">
                <small class="form-text text-danger">
                    @if($errors->has('name'))
                    {{ $errors->first('name') }}
                    @endif
                </small>
                <small id="nameHelp" class="form-text text-muted">El nombre solo debe contener letras y espacios.</small>
                <div class="valid-feedback">Nombre válido.</div>
                <div class="invalid-feedback">El nombre solo debe contener letras y espacios.</div>
            </div>
            <!-- Email input -->
            <div class="form-group fs-6 mt-2">
                <label for="email">Correo</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Ingrese correo">
                <small class="form-text text-danger">
                    @if($errors->has('email'))
                    {{ $errors->first('email') }}
                    @endif
                </small>
                <small id="emailHelp" class="form-text text-muted">Ingrese un correo electrónico válido.</small>
                <div class="valid-feedback">Correo válido.</div>
                <div class="invalid-feedback">Ingrese un correo electrónico válido.</div>
            </div>

            <!-- Password input -->
            <div class="form-group fs-6 mt-2">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" value="{{ old('password') }}">
                <small class="form-text text-danger">
                    @if($errors->has('password'))
                    {{ $errors->first('password') }}
                    @endif
                </small>
                <small id="passwordHelp" class="form-text text-muted">La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.</small>
                <div class="valid-feedback">Contraseña válida.</div>
                <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.</div>
            </div>
            <!-- Password confirmation input -->
            <div class="form-group fs-6 mt-2">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmar contraseña" value="{{ old('password_confirmation') }}">
                <small class="form-text text-danger">
                    @if($errors->has('password_confirmation'))
                    {{ $errors->first('password_confirmation') }}
                    @endif
                </small>
                <small id="passwordConfirmationHelp" class="form-text text-muted">Confirme su contraseña.</small>
                <div class="valid-feedback">Las contraseñas coinciden.</div>
                <div class="invalid-feedback">Las contraseñas no coinciden.</div>
            </div>
            <!-- Recaptcha -->
            <label for="recaptcha mt-2">reCaptcha</label>
            <div class="g-recaptcha mt-2 col-12" data-sitekey="{{env('RECAPTCHA_SITE_KEY')}}"></div>
            <small class="form-text text-danger">
                @if($errors->has('g-recaptcha-response'))
                {{ $errors->first('g-recaptcha-response') }}
                @endif
            </small>
            <!-- Submit button -->
            <div class="col-12 justify-content-center d-flex mt-2">
                <button type="submit" class="btn btn-primary col-12">Registrarte</button>
            </div>
        </form>
        <!-- Register link -->
        <div class="text-center mt-3">
            <p>¿Ya estás registrado? <a href="/login" class="text-decoration-none text-reset fw-bold">Inicia sesión</a></p>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    // Función para validar los campos
    function validateField(field, regex, helpTextId, validFeedback, invalidFeedback) {
        const value = field.value;
        const helpText = document.getElementById(helpTextId);
        if (value === '') {
            field.classList.remove('is-valid', 'is-invalid');
            helpText.style.display = 'block';
            validFeedback.style.display = 'none';
            invalidFeedback.style.display = 'none';
        } else if (regex.test(value)) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            helpText.style.display = 'none';
            validFeedback.style.display = 'block';
            invalidFeedback.style.display = 'none';
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            helpText.style.display = 'none';
            validFeedback.style.display = 'none';
            invalidFeedback.style.display = 'block';
        }
    }

    // Validación del nombre
    document.getElementById('name').addEventListener('input', function() {
        validateField(this, /^[a-zA-Z\s]+$/, 'nameHelp', this.nextElementSibling.nextElementSibling.nextElementSibling, this.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling);
    });

    // Validación del correo
    document.getElementById('email').addEventListener('input', function() {
        validateField(this, /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'emailHelp', this.nextElementSibling.nextElementSibling.nextElementSibling, this.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling);
    });

    // Validación de la contraseña
    document.getElementById('password').addEventListener('input', function() {
        validateField(this, /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/, 'passwordHelp', this.nextElementSibling.nextElementSibling.nextElementSibling, this.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling);
    });

    // Validación de la confirmación de la contraseña
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const passwordConfirmation = this.value;
        const helpText = document.getElementById('passwordConfirmationHelp');
        const validFeedback = this.nextElementSibling.nextElementSibling.nextElementSibling;
        const invalidFeedback = validFeedback.nextElementSibling;
        if (passwordConfirmation === '') {
            this.classList.remove('is-valid', 'is-invalid');
            helpText.style.display = 'block';
            validFeedback.style.display = 'none';
            invalidFeedback.style.display = 'none';
        } else if (password === passwordConfirmation) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            helpText.style.display = 'none';
            validFeedback.style.display = 'block';
            invalidFeedback.style.display = 'none';
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
            helpText.style.display = 'none';
            validFeedback.style.display = 'none';
            invalidFeedback.style.display = 'block';
        }
    });

    // Validación del formulario antes de enviarlo
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        if (!name.classList.contains('is-valid') || !email.classList.contains('is-valid') || !password.classList.contains('is-valid') || !passwordConfirmation.classList.contains('is-valid')) {
            event.preventDefault();
            Swal.fire({
                title: '¡Error!',
                text: 'Por favor, asegúrate de que todos los campos estén correctos.',
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

    // Validar los campos al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Validar nombre
        validateField(document.getElementById('name'), /^[a-zA-Z\s]+$/, 'nameHelp', document.getElementById('name').nextElementSibling.nextElementSibling.nextElementSibling, document.getElementById('name').nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling);
        // Validar correo
        validateField(document.getElementById('email'), /^[^\s@]+@[^\s@]+\.[^\s@]+$/, 'emailHelp', document.getElementById('email').nextElementSibling.nextElementSibling.nextElementSibling, document.getElementById('email').nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling);
        // Validar contraseña
        validateField(document.getElementById('password'), /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/, 'passwordHelp', document.getElementById('password').nextElementSibling.nextElementSibling.nextElementSibling, document.getElementById('password').nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling);
        // Validar confirmación de contraseña
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;
        const helpText = document.getElementById('passwordConfirmationHelp');
        const validFeedback = document.getElementById('password_confirmation').nextElementSibling.nextElementSibling.nextElementSibling;
        const invalidFeedback = validFeedback.nextElementSibling;
        if (passwordConfirmation === '') {
            document.getElementById('password_confirmation').classList.remove('is-valid', 'is-invalid');
            helpText.style.display = 'block';
            validFeedback.style.display = 'none';
            invalidFeedback.style.display = 'none';
        } else if (password === passwordConfirmation) {
            document.getElementById('password_confirmation').classList.remove('is-invalid');
            document.getElementById('password_confirmation').classList.add('is-valid');
            helpText.style.display = 'none';
            validFeedback.style.display = 'block';
            invalidFeedback.style.display = 'none';
        } else {
            document.getElementById('password_confirmation').classList.remove('is-valid');
            document.getElementById('password_confirmation').classList.add('is-invalid');
            helpText.style.display = 'none';
            validFeedback.style.display = 'none';
            invalidFeedback.style.display = 'block';
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