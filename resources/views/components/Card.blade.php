<div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
    @if(isset($title))
        <h3 class="text-lg font-bold text-gray-800 mb-4">{{ $title }}</h3>
    @endif
    
    <div class="text-gray-700">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="mt-4 pt-4 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div>
