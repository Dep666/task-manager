<button {{ $attributes->merge(['type' => 'submit', 'class' => 'auth-button w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
