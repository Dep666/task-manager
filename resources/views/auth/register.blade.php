<x-guest-layout>
    <h1 class="auth-title">Создание аккаунта</h1>
    
    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Имя</label>
            <div class="input-icon-wrapper">
                <span class="input-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </span>
                <input id="name" type="text" name="name" class="form-input" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Иван Иванов">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

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
                <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autocomplete="username" placeholder="your-email@example.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Пароль</label>
            <div class="input-icon-wrapper" style="position: relative;">
                <span class="input-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                <input id="password" type="password" name="password" class="form-input password-field" required autocomplete="new-password" placeholder="••••••••">
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
            <div class="text-xs text-muted mt-2">Минимум 8 символов</div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Подтвердите пароль</label>
            <div class="input-icon-wrapper" style="position: relative;">
                <span class="input-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-input password-field" required autocomplete="new-password" placeholder="••••••••">
                <button type="button" class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            <div id="capsLockWarning2" class="text-xs text-red-500 mt-1" style="display: none;">
                Включен Caps Lock!
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <button type="submit" class="auth-button w-full">
                ЗАРЕГИСТРИРОВАТЬСЯ
            </button>
        </div>
        
        <div class="text-center text-sm mt-4">
            Уже есть аккаунт? <a href="{{ route('login') }}" class="auth-link">Войти</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Проверка Caps Lock
            const passwordFields = document.querySelectorAll('.password-field');
            const capsLockWarning = document.getElementById('capsLockWarning');
            const capsLockWarning2 = document.getElementById('capsLockWarning2');
            
            passwordFields.forEach(field => {
                field.addEventListener('keydown', function(e) {
                    if (e.getModifierState('CapsLock')) {
                        // Показываем предупреждение рядом с активным полем
                        if (this.id === 'password') {
                            capsLockWarning.style.display = 'block';
                        } else if (this.id === 'password_confirmation') {
                            capsLockWarning2.style.display = 'block';
                        }
                    } else {
                        // Скрываем предупреждения
                        capsLockWarning.style.display = 'none';
                        capsLockWarning2.style.display = 'none';
                    }
                });
                
                field.addEventListener('blur', function() {
                    // Скрываем все предупреждения при потере фокуса
                    capsLockWarning.style.display = 'none';
                    capsLockWarning2.style.display = 'none';
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

