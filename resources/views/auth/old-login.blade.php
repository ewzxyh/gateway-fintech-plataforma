@php
use App\Helpers\Helper;
$setting = Helper::getSetting();
@endphp
<x-guest-layout :route="'Login'">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .form-outlined {
     position: relative;
     margin-top: 2rem; }

    .form-outlined input {
     padding: 1.25rem 2.5rem 0.5rem 0.75rem;
     border: 1px solid #ced4da;
     border-radius: 0.375rem;
     outline: none;
     width: 100%;
     background: transparent;
     min-height: 56px !important;

    }

    .form-outlined label {
     position: absolute;
     top: 1rem;
     left: 0.75rem;
     background: white;
     padding: 0 0.25rem;
     color: #6c757d;
     font-size: 1rem;
     transition: 0.2s ease all;
     pointer-events: none;
    }

    .form-outlined input:focus + label,
    .form-outlined input:not(:placeholder-shown) + label {
     top: -0.6rem;
     left: 0.5rem;
     font-size: 0.75rem;
     color: var(--color-gateway);
    }

    .form-outlined input:focus {
      border-color: var(--color-gateway) !important;
      box-shadow: 0 0 2px 1px var(--color-gateway) !important;
    }
    .toggle-visibility {
     position: absolute;
     top: 50%;
     right: 0.75rem;
     transform: translateY(-50%);
     background: transparent;
     border: none;
     font-size: 1.25rem;
     color: #6c757d;
     cursor: pointer;
    }
    .form-title,
    .mdc-label::after,
    .form-check label {
      color: gray !important;
    }
    .form-subtitle{
      color: gray !important;
    }
    .form-check {
      margin-top: 10px !important;
      margin-bottom: 10px !important;
    }

    .form-check-input:checked {
      background-color: var(--color-gateway) !important;
      border-color: var(--color-gateway) !important;
      box-shadow: 0 0 3px var(--color-gateway) !important;
    }
   </style>
  <x-auth-session-status class="mb-4" :status="session('status')" />
  <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8">
    <div class="mt-5 mb-4 card card-raised shadow-10 mt-xl-10">
      <div class="p-5 card-body">
        <div class="text-center">
          <img class="mb-3 theme-logo" 
             src="{{ asset($setting->gateway_logo) }}" 
             alt="..." 
             style="height: 48px"
             data-light-src="{{ asset($setting->gateway_logo) }}"
             data-dark-src="{{ asset($setting->gateway_logo_dark ?? $setting->gateway_logo) }}" />
          <h1 class="mb-0 display-5 form-title">Efetue o login</h1>
          <div class="mb-5 subheading-1 form-subtitle">para continuar</div>
        </div>

        <form method="POST" action="{{ route('login') }}">
          @csrf
          @if (session('error'))
          <div class="alert alert-danger w-100">{{ session('error') }} </div>
          @endif
          <div class="mb-4">
            <div class="form-outlined">
              <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="" />
              <label for="email">Email</label>
            </div>
            @if ($errors->has('email'))
            <span class="text-danger">{{ $errors->first('email') }} </span>
            @endif
          <div class="mb-4">
            <div class="form-outlined">
              <input type="password" id="passwordInput" name="password" value="{{ old('password') }}" class="form-control" placeholder="" />
              <label for="passwordInput">Senha</label>
              <button type="button" id="togglePassword" class="toggle-visibility">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
            @if ($errors->has('password'))
            <span class="text-danger">{{ $errors->first('password') }} </span>
            @endif
          <div class="d-flex align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="checkDefault">
              <label class="form-check-label" for="checkDefault">
               Lembrar-me
              </label>
             </div>

          </div>
          <div class="mt-4 mb-0 form-group d-flex align-items-center justify-content-between">
            <a class="small fw-500 text-decoration-none" href="/register">Cadastrar-me</a>
            <button type="submit" class="btn btn-primary" >Acessar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
     const toggleBtn = document.getElementById("togglePassword");
     const passwordInput = document.getElementById("passwordInput");
     const icon = toggleBtn.querySelector("i");

     toggleBtn.addEventListener("click", function () {
      const isPassword = REDACTED_SECRET ==="password";
      passwordInput.type = isPassword ?"text" :"password";
      icon.classList.toggle("bi-eye");
      icon.classList.toggle("bi-eye-slash");
     });
    });
   </script>

</x-guest-layout>
