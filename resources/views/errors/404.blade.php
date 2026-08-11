<x-layouts.tienda :title="'Página no encontrada — Ferretería BANDEK'">

    <section class="max-w-2xl mx-auto px-4 py-20 text-center">

        <img src="{{ asset('img/logo-icono.png') }}" alt="Ferretería BANDEK" class="w-16 h-16 object-contain mx-auto mb-6">

        <p class="text-red-800 font-bold text-6xl sm:text-7xl mb-2">404</p>

        <h1 class="text-2xl font-bold text-gray-900 mb-3">
            No encontramos esta página
        </h1>

        <p class="text-gray-600 mb-8 leading-7">
            El enlace puede estar mal escrito, o el producto o página que buscás
            ya no está disponible. Probá desde el inicio o el catálogo.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">

            <a href="{{ route('inicio') }}"
               class="bandek-btn-cta bg-red-800 hover:bg-red-900 text-white font-semibold px-6 py-3 rounded-md transition">
                Ir al inicio
            </a>

            <a href="{{ route('catalogo.index') }}"
               class="bandek-btn-cta bg-white border border-gray-300 hover:border-red-800 text-gray-700 font-semibold px-6 py-3 rounded-md transition">
                Ver catálogo
            </a>

        </div>

    </section>

</x-layouts.tienda>
