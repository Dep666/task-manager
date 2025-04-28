@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'auth-input w-full border focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm py-2.5 px-4 transition duration-200']) }}>
