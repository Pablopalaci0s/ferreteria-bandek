<form method="GET" class="flex items-center gap-2 mb-4">

    <div class="relative flex-1 max-w-sm">

        <input
            type="text"
            name="buscar"
            value="{{ request('buscar') }}"
            placeholder="{{ $placeholder ?? 'Buscar...' }}"
            class="w-full border border-gray-300 rounded-md pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-red-700 focus:border-red-700"
        >

        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5A6.5 6.5 0 114 10.5a6.5 6.5 0 0113 0z" />
        </svg>

    </div>

    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-md">
        Buscar
    </button>

    @if (request('buscar'))
        <a href="{{ url()->current() }}" class="text-sm text-gray-500 hover:text-red-800">
            Limpiar
        </a>
    @endif

</form>
