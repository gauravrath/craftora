<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Log in') }} | Craftora</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #281a2c; }
        .login-layout { display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; }
        .login-brand, .login-content { display: flex; align-items: center; justify-content: center; padding: 48px; }
        .login-brand { background: #faf3fb; }
        .login-brand img { display: block; width: min(100%, 520px); height: auto; }
        .login-content { background: #fff; }
        .login-form { width: 100%; max-width: 400px; }
        .login-form h1 { margin: 0 0 10px; font-size: 32px; color: #42034e; }
        .login-intro { margin: 0 0 32px; color: #685c6b; line-height: 1.5; }
        .form-field { margin-bottom: 20px; }
        .form-field label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; }
        .form-field input { width: 100%; padding: 12px 14px; border: 1px solid #d6ccd8; border-radius: 8px; font: inherit; }
        .form-field input:focus { outline: 2px solid #7e4488; outline-offset: 2px; }
        .field-errors { padding: 0; margin: 8px 0 0; list-style: none; color: #b42318; font-size: 14px; }
        .login-options { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin: 24px 0; font-size: 14px; }
        .remember-label { display: inline-flex; align-items: center; gap: 8px; }
        .remember-label input { accent-color: #500d5d; }
        .login-options a { color: #650f74; }
        .login-button { width: 100%; padding: 13px; border: 0; border-radius: 8px; background: #500d5d; color: #fff; font: inherit; font-weight: 600; cursor: pointer; }
        .login-button:hover { background: #3d0847; }
        .login-button:focus-visible, .login-options a:focus-visible, .remember-label input:focus-visible { outline: 2px solid #7e4488; outline-offset: 3px; }
        @media (max-width: 700px) {
            .login-layout { grid-template-columns: 1fr; grid-template-rows: auto 1fr; }
            .login-brand { padding: 32px 24px; }
            .login-brand img { width: min(100%, 300px); }
            .login-content { align-items: flex-start; padding: 40px 24px; }
        }
    </style>
</head>
<body>
    <main class="login-layout">
        <div class="login-brand">
            <img src="{{ asset('dist/assets/img/logo.png') }}" alt="Craftora">
        </div>
        <div class="login-content">
            <div class="login-form">
                <h1>{{ __('Welcome back') }}</h1>
                <p class="login-intro">{{ __('Log in to your account to continue.') }}</p>

                <x-auth-session-status :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-field">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="field-errors" />
                    </div>

                    <div class="form-field">
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="field-errors" />
                    </div>

                    <div class="login-options">
                        <label for="remember_me" class="remember-label">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>{{ __('Remember me') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                        @endif
                    </div>

                    <button class="login-button" type="submit">{{ __('Log in') }}</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
