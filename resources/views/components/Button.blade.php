<button
    type="{{ $type ?? 'button' }}"
    class="px-4 py-2 rounded-md font-semibold transition-all duration-200
        {{ $variant === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' : '' }}
        {{ $variant === 'success' ? 'bg-green-600 hover:bg-green-700 text-white' : '' }}
        {{ $variant === 'warning' ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : '' }}
        {{ $variant === 'secondary' ? 'bg-gray-400 hover:bg-gray-500 text-white' : '' }}
        {{ !isset($variant) || $variant === 'primary' ? 'bg-blue-600 hover:bg-blue-700 text-white' : '' }}
        {{ ($size ?? '') === 'sm' ? 'px-3 py-1 text-sm' : '' }}
        {{ ($size ?? '') === 'lg' ? 'px-6 py-3 text-lg' : '' }}
        {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes }}
>
    {{ $slot }}
</button>
