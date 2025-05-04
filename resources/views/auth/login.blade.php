    <x-guest-layout>
        <h1 class="auth-title">Вход в систему</h1>
        
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-icon-wrapper">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </span>
                    <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="your-email@example.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="form-group">
                <div class="flex-between">
                    <label for="password" class="form-label">Пароль</label>
                    @if (Route::has('password.request'))
                        <a class="text-xs auth-link" href="{{ route('password.request') }}">
                            Забыли пароль?
                        </a>
                    @endif
                </div>
                <div class="input-icon-wrapper" style="position: relative;">
                    <span class="input-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input id="password" type="password" name="password" class="form-input password-field" required autocomplete="current-password" placeholder="••••••••">
                    <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div id="capsLockWarning" class="text-xs text-red-500 mt-1" style="display: none;">
                    Включен Caps Lock!
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div>
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="form-checkbox rounded" name="remember">
                    <span class="text-sm">Запомнить меня</span>
                </label>
            </div>

            <div class="mt-6">
                <button type="submit" class="auth-button w-full">
                    ВОЙТИ
                </button>
            </div>
            
            <div class="text-center text-sm mt-4">
                Нет аккаунта? <a href="{{ route('register') }}" class="auth-link">Зарегистрироваться</a>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Проверка Caps Lock
                const passwordFields = document.querySelectorAll('.password-field');
                const capsLockWarning = document.getElementById('capsLockWarning');
                
                passwordFields.forEach(field => {
                    field.addEventListener('keydown', function(e) {
                        if (e.getModifierState('CapsLock')) {
                            capsLockWarning.style.display = 'block';
                        } else {
                            capsLockWarning.style.display = 'none';
                        }
                    });
                    
                    field.addEventListener('blur', function() {
                        capsLockWarning.style.display = 'none';
                    });
                });
                
                // Переключение видимости пароля
                const toggleBtns = document.querySelectorAll('.toggle-password');
                
                toggleBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const passwordField = this.closest('.input-icon-wrapper').querySelector('.password-field');
                        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordField.setAttribute('type', type);
                        
                        // Изменение иконки
                        const eyeIcon = this.querySelector('.eye-icon');
                        if (type === 'password') {
                            eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
                        } else {
                            eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07A3 3 0 1 1 9.88 9.88"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                        }
                    });
                });
            });
        </script>
    </x-guest-layout>
