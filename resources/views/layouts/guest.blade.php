<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Task Manager') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Дополнительные стили -->
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #111827;
                color: #e5e7eb;
            }
            .auth-wrapper {
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 1rem;
            }
            .auth-card {
                width: 100%;
                max-width: 420px;
                background-color: #111927;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.05);
                padding: 2rem;
            }
            .auth-form {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
            }
            .auth-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: white;
                text-align: center;
                margin-bottom: 1.5rem;
            }
            .auth-logo {
                width: 2.5rem;
                height: 2.5rem;
                background-color: #6d28d9;
                border-radius: 9999px;
                margin: 0 auto 1.5rem;
            }
            .form-group {
                margin-bottom: 1rem;
            }
            .form-label {
                display: block;
                margin-bottom: 0.5rem;
                font-size: 0.875rem;
                color: #e5e7eb;
            }
            .form-input {
                width: 100%;
                padding: 0.75rem 1rem 0.75rem 2.5rem;
                background-color: #1e293b;
                border: 1px solid #374151;
                border-radius: 0.375rem;
                color: white;
                transition: all 0.2s;
            }
            .form-input:focus {
                outline: none;
                border-color: #6d28d9;
                box-shadow: 0 0 0 2px rgba(109, 40, 217, 0.1);
            }
            .form-input::placeholder {
                color: #4b5563;
            }
            .input-icon-wrapper {
                position: relative;
            }
            .input-icon {
                position: absolute;
                left: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                color: #4b5563;
                width: 1.25rem;
                height: 1.25rem;
            }
            .form-checkbox {
                margin-right: 0.5rem;
                background-color: #1e293b;
                border-color: #374151;
            }
            .auth-button {
                background-color: #6d28d9;
                color: white;
                font-weight: 600;
                padding: 0.75rem 1rem;
                border-radius: 0.375rem;
                text-transform: uppercase;
                transition: all 0.2s;
                border: none;
                cursor: pointer;
            }
            .auth-button:hover {
                background-color: #5b21b6;
            }
            .auth-link {
                color: #6d28d9;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.2s;
            }
            .auth-link:hover {
                color: #8b5cf6;
            }
            .text-center {
                text-align: center;
            }
            .text-sm {
                font-size: 0.875rem;
            }
            .text-xs {
                font-size: 0.75rem;
            }
            .text-right {
                text-align: right;
            }
            .flex-between {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .text-muted {
                color: #6b7280;
            }
            .mt-6 {
                margin-top: 1.5rem;
            }
            .mt-4 {
                margin-top: 1rem;
            }
            .mt-2 {
                margin-top: 0.5rem;
            }
        </style>
    </head>
    <body>
        <div class="auth-wrapper">
            <div class="auth-card">
                <div class="auth-logo"></div>
                
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
