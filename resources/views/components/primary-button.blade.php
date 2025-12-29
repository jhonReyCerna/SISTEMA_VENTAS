<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center w-full px-4 py-3 bg-blue-900 text-white rounded-lg font-semibold text-sm shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-1 transition']) }}>
    {{ $slot }}
</button>
